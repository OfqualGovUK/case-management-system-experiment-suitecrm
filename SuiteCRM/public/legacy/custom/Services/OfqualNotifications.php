<?php

/**
 * Interacts with the Ofqual Notify service to send email notifications.
 * Currently uses a fixed set of Source data, with only the outgoing 
 * Summary and Target UPN being variable.
 */
require_once $GLOBALS['BASE_DIR'] . '/custom/Services/EnvStore.php';

use GuzzleHttp\Client;

class OfqualNotifications
{
    protected Client $httpClient;

    // Class implementation goes here
    public function __construct()
    {
        $this->httpClient = new Client();
    }


    public function sendNotification($summary, $targetUpn)
    {

        try {
            $logger = \LoggerManager::getLogger();

            $apiCredentials = [];

            $requiredCredentialKeys = [
                'client_id',
                'client_secret',
                'grant_type',
                'scope',
                'token_endpoint',
                'notification_api',
            ];

            foreach ($requiredCredentialKeys as $key) {
                $keyStore = new EnvStore('notifications_' . $key);

                $value = $keyStore->getValue();
                if ($value === false || empty($value)) {
                    $logger->error("OfqualNotifications: Missing API credential for key: {$key}");
                    throw new \Exception("Missing API credential for key: {$key}");
                }
                $apiCredentials[$key] = $value;
            }

            $tokenResponse = $this->httpClient->post($apiCredentials['token_endpoint'], [
                'form_params' => [
                    'client_id'     => $apiCredentials['client_id'],
                    'client_secret' => $apiCredentials['client_secret'],
                    'grant_type'    => $apiCredentials['grant_type'],
                    'scope'         => $apiCredentials['scope'],
                ],
            ]);

            $tokenData = json_decode($tokenResponse->getBody()->getContents(), true);
            if (empty($tokenData['access_token'])) {
                $logger->error('OfqualNotifications: Failed to retrieve access token.');
                throw new \Exception("Failed to retrieve access token.");
            }
            $accessToken = $tokenData['access_token'];

            $requireddata = ['source_id' => 'SourceId', 'source_upn' => 'SourceUpn'];

            $payloaddata = [
                'SourceModule'  => 1,
                'TargetUpn'     => $targetUpn,
                'Summary'       => $summary
            ];

            foreach ($requireddata as $key => $name) {
                $keyStore = new EnvStore('notifications_' . $key);

                $value = $keyStore->getValue();

                if ($value === false || empty($value)) {
                    $logger->error("OfqualNotifications: Missing payload information for key: {$key}");
                    throw new \Exception("Missing payload information for key: {$key}");
                }
                $payloaddata[$name] = $value;
            }

            $notificationResponse = $this->httpClient->post($apiCredentials['notification_api'] . '/notifications', [
                'json' => $payloaddata,
                'timeout' => 10,
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
            ]);

            $logger->notice('Notification sent successfully.');
            return $notificationResponse->getStatusCode() === 200;
        } catch (Exception $e) {
            $logger->error('Error Sending Notification:', var_export($e->getMessage(), true));
        }
    }
}
