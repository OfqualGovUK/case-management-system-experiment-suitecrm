<?php

namespace App\Extension\Ofqual\backend\Install\LegacyHandler;

use App\Engine\LegacyHandler\LegacyHandler;
use App\Engine\LegacyHandler\LegacyScopeState;
use App\Engine\Model\Feedback;
use App\Install\Service\Installation\InstallStatus;
use App\Install\Service\InstallationUtilsTrait;
use App\Install\Service\InstallPreChecks;
use App\Security\AppSecretGenerator;
use Exception;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PDO;
use PDOException;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RequestStack;
use Throwable;
use \BeanFactory;
use Psr\Log\LoggerInterface;

class StartupVariables extends LegacyHandler
{
    use InstallationUtilsTrait;

    public const HANDLER_KEY = 'ofqual-startup-variables';
    protected $legacyDir;
    protected $projectDir;
    private $logger;

    private $aclactions;
    private $envstore;

    public function getHandlerKey(): string
    {
        return self::HANDLER_KEY;
    }
    public function __construct(
        string $projectDir,
        string $legacyDir,
        string $legacySessionName,
        string $defaultSessionName,
        LegacyScopeState $legacyScopeState,
        RequestStack $requestStack,
        LoggerInterface $logger
    ) {
        parent::__construct(
            $projectDir,
            $legacyDir,
            $legacySessionName,
            $defaultSessionName,
            $legacyScopeState,
            $requestStack
        );
        $this->legacyDir = $legacyDir;
        $this->projectDir = $projectDir;
        $this->logger = $logger;
    }

    public function initLegacy(): void
    {
        $this->switchSession($this->legacySessionName);
        chdir($this->legacyDir);
    }

    public function closeLegacy(): void
    {
        chdir($this->projectDir);
        $this->switchSession($this->defaultSessionName);
    }

    public function loadLegacyConfig(): ?array
    {
        return $this->getLegacyConfig($this->legacyDir);
    }

    public function createBasicUser(): mixed
    {
        $userbean = BeanFactory::newBean('Users');
        $userbean->user_name = 'ofqual_api_user';
        $userbean->first_name = 'Ofqual';
        $userbean->last_name = 'API User';
        $userbean->status = 'Active';
        $userbean->is_admin = false;
        $userbean->save();

        $userbean->load_relationship('aclroles');
        $basicRole = $this->createBasicRole();
        $userbean->aclroles->add($basicRole);
        $client = $this->createAPIClient();
        $client->assigned_user_id = $userbean->id;
        $client->save();
        $this->envstore['OfqualAPIClientID'] = $client->id;

        return $userbean;
    }

    public function createBasicRole()
    {
        $actions = $this->aclactions;

        $role = BeanFactory::newBean('ACLRoles');
        $role->name = 'Ofqual API No Access Role';
        $role->description = 'Role with no access, for Default Ofqual API user';

        foreach ($actions as $action) {
            $role->setAction($role->id, $action->id, -99);
        }

        $role->save();

        return $role;
    }

    public function createAdvancedRole(): mixed
    {
        $actions = $this->aclactions;

        $role = BeanFactory::newBean('ACLRoles');
        $role->name = 'Ofqual API Access Role';
        $role->description = 'Role with access, for API user';

        foreach ($actions as $action) {
            switch ($action->name) {
                case 'access':
                case 'massupdate':
                case 'import':
                case 'export':
                    $level = 89; //Allow enabled.
                    break;
                case 'list':
                case 'edit':
                case 'delete':
                case 'view':
                    $level = 80; //group
                    // $level = 90; //all
                    break;
                default:
                    $level = -99; // No access
                    break;
            }
            $role->setAction($role->id, $action->id, $level);
        }

        $role->save();

        return $role;
    }

    public function createAdminRole(): mixed
    {

        $actions = $this->aclactions;
        $role = BeanFactory::newBean('ACLRoles');
        $role->name = 'Ofqual API Admin Role';
        $role->description = 'Role with admin access, for API user';

        foreach ($actions as $action) {
            switch ($action->name) {
                case 'access':
                case 'massupdate':
                case 'import':
                case 'export':
                    $level = 89; //Allow enabled.
                    break;
                case 'list':
                case 'edit':
                case 'delete':
                case 'view':
                    //$level = 80; //group
                    $level = 90; //all
                    break;
                default:
                    $level = 90; // all access
                    break;
            }
            $role->setAction($role->id, $action->id, $level);
        }

        $role->save();

        return $role;
    }

    private function generateClientSecret(): string
    {
        if (function_exists('openssl_random_pseudo_bytes')) {
            return bin2hex(openssl_random_pseudo_bytes(6));
        } else {
            return bin2hex(random_bytes(6));
        }
    }

    public function createAPIClient(): mixed
    {

        $secret = $this->generateClientSecret() . '-' . $this->generateClientSecret() .
            '_' . $this->generateClientSecret() . '-' . $this->generateClientSecret();
        $this->envstore['OfqualAPIClientPassword'] = $secret;

        $clientBean = BeanFactory::newBean('OAuth2Clients');
        $clientBean->name = 'Ofqual API Client';
        $clientBean->secret = hash('sha256', $secret);
        $clientBean->is_confidential = true;
        return $clientBean;
    }

    public function getStoredSecrets(): void
    {
        $keylocation = $this->legacyDir . '/Api/V8/OAuth2/';
        $keyfile = $keylocation . 'private.key';
        $publickeyfile = $keylocation . 'public.key';

        $this->envstore['OAUTH_PRIVKEY'] = file_get_contents($keyfile);
        $this->envstore['OAUTH_PUBKEY'] = file_get_contents($publickeyfile);

        $envstore = $this->projectDir . '/.env.local';
        if (file_exists($envstore)) {
            $lines = file($envstore, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (str_starts_with($line, 'APP_SECRET=')) {
                    $parts = explode('=', $line, 2);
                    if (count($parts) == 2) {
                        $this->envstore['APP_SECRET'] = trim($parts[1]);
                    }
                }
            }
        }

        $config = $this->loadLegacyConfig();

        $this->envstore['SUITECRM_UNIQUE_KEY'] = $config['oauth2_encryption_key'] ?? '';
        $this->envstore['SUITECRM_OAUTH_ENCRYPTION_KEY'] = $config['unique_key'] ?? '';
    }

    public function pushToVault(): void
    {

        $file = $this->projectDir . '/' . 'pushsecrets.sh';

        $clientid = '$VAULT_CLIENT_ID';

        $string = <<<EOF
#!/bin/bash

az login --identity --client-id "$clientid" >/dev/null

EOF;
        $secrets = [
            'APP_SECRET' => ['varname' => 'suiteenvappsecret', 'location' => 'Container'],
            'OfqualOAuth2PrivateKey' => ['varname' => 'suiteoauthprivatekey', 'location' => 'Container'],
            'OfqualOAuth2PublicKey' => ['varname' => 'suiteoauthpublickey', 'location' => 'Container'],
            'OAuth2EncryptionKey' => ['varname' => 'suiteoauthencryptionkey', 'location' => 'Container'],
            'UniqueKey' => ['varname' => 'suiteuniquekey', 'location' => 'Container'],
            'OfqualAPIClientID' => ['varname' => 'SuiteOfqualAPIClientID', 'location' => 'APIM'],
            'OfqualAPIClientPassword' => ['varname' => 'SuiteOfqualAPIClientPassword', 'location' => 'APIM'],
        ];

        $test = getenv('DEV_TEST', true);
        foreach ($secrets as $envKey => $details) {
            $tmpstring = '';
            if ($test == 'true') {
                $vaultkey = 'test-' . $details['varname'];
            } else {
                $vaultKey = $details['varname'];
            }
            switch ($details['location']) {
                case 'Container':
                    $vaultname = '$AZURE_SUITECRM_KEYVAULT_NAME';
                    break;
                case 'APIM':
                    $vaultname = '$AZURE_APIM_KEYVAULT_NAME';
                    break;
                default:
                    $this->logger->error("Unknown vault location for {$envKey}");
                    continue 2;
            }


            if (isset($this->envstore[$envKey])) {
                $value = $this->envstore[$envKey];

                $tmpstring = <<<EOF
az keyvault secret set --vault-name "$vaultname" --name "$vaultKey" --value "$value"

EOF;
                $string .= $tmpstring;
            } else {
                $this->logger->warning("Environment variable {$envKey} not found in envstore.");
            }
        }

        $handle = fopen($file, 'w');
        try {
            fwrite($handle, $string);
            fclose($handle);
            chmod($file, 0755);
        } catch (IOExceptionInterface $exception) {
            $this->logger->error('An error occurred while writing to file at ' . $exception->getPath());
        }

        // Placeholder for pushing to Vault logic
        // This would typically involve using a Vault client library to store the secrets securely
        //  $this->logger->info('Pushing secrets to Vault is not yet implemented.');
    }
    public function pushSecrets(): void
    {
        $localfile = $this->projectDir . '/suiteenv.env';

        if (file_exists($localfile) && getenv('LOCAL_DEV') === 'true') {
            $handle = fopen($localfile, 'w');
            try {

                foreach ($this->envstore as $key => $value) {
                    fwrite($handle, $key . '=\'' . $value . "'\n");
                }
                fclose($handle);
            } catch (\Exception $exception) {
                $this->logger->error('An error occurred while writing to file at ' . $exception->getPath());
            }
        } else {
            $this->pushToVault();
        }
    }

    public function run()
    {
        $this->initLegacy();
        $actionsbean = BeanFactory::getBean('ACLActions');
        $actions = $actionsbean->get_list();

        $this->aclactions = $actions['list'];

        $this->createBasicUser();
        $this->getStoredSecrets();
        $this->pushSecrets();
        $this->closeLegacy();
    }
}
