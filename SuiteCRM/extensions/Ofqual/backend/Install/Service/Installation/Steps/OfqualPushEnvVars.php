<?php

namespace App\Extension\Ofqual\backend\Install\Service\Installation\Steps;

use App\Engine\Model\Feedback;
use App\Engine\Model\ProcessStepTrait;
use App\Extension\Ofqual\backend\Install\LegacyHandler\StartupVariables;
use App\Install\Service\Installation\InstallStepInterface;

class OfqualPushEnvVars implements InstallStepInterface
{
    use ProcessStepTrait;

    public const HANDLER_KEY = 'ofqual-push-env-vars';
    public const POSITION = 999;

    private $handler;

    public function __construct(StartupVariables $startupvars)
    {
        $this->handler = $startupvars;
    }

    public function getKey(): string
    {
        return self::HANDLER_KEY;
    }

    public function getOrder(): int
    {
        return self::POSITION;
    }

    public function execute(array &$context): Feedback
    {
        $this->handler->run($context);
        $feedback = new Feedback();
        $feedback->setSuccess(true)->setMessages(['Ofqual environment variables pushed successfully.']);
        return $feedback;
    }
}
