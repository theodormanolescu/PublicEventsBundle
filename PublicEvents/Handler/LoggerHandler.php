<?php

namespace Elefant\PublicEventsBundle\PublicEvents\Handler;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class LoggerHandler extends Handler
{
    private $level;
    private $logMessage;

    /**
     * @param string $level
     * @param string $logMessage
     */
    public function __construct($level = LogLevel::INFO, $logMessage = 'public_event')
    {
        $this->level = $level;
        $this->logMessage = $logMessage;
    }

    protected function doHandle($logContext)
    {
        if ($this->logger) {
            $this->logger->log($this->level, $this->logMessage, ['event' => $logContext]);
        }
    }
}
