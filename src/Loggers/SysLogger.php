<?php

declare(strict_types=1);

namespace MinVWS\Logging\Laravel\Loggers;

use MinVWS\AuditLogger\Events\Logging\GeneralLogEvent;
use MinVWS\AuditLogger\Loggers\LogEventInterface;
use MinVWS\AuditLogger\Loggers\LoggerInterface;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

class SysLogger implements LoggerInterface
{
    protected PsrLoggerInterface $logger;

    public function __construct(
        protected bool $encrypt,
        protected string $pubKey,
        protected string $privKey,
        PsrLoggerInterface $logger
    ) {
        if ($this->encrypt && !function_exists('sodium_crypto_box')) {
            throw new \Exception(
                "libsodium cound not found. Please install libsodium or do not use encryption in the syslogger"
            );
        }

        $this->logger = $logger;
    }

    #[Override]
    public function log(LogEventInterface $event): void
    {
        $data = $event->getMergedPiiData();

        if ($this->encrypt) {
            $pair = sodium_crypto_box_keypair_from_secretkey_and_publickey($this->privKey, $this->pubKey);
            $nonce = random_bytes(SODIUM_CRYPTO_BOX_NONCEBYTES);
            $encrypted = sodium_crypto_box(json_encode($data, JSON_THROW_ON_ERROR), $nonce, $pair);

            $data = $nonce . $encrypted;
        } else {
            $data = json_encode($data, JSON_THROW_ON_ERROR);
        }

        $this->logger->info('AuditLog: ' . base64_encode($data));
    }

    #[Override]
    public function canHandleEvent(LogEventInterface $event): bool
    {
        if (is_a($event, GeneralLogEvent::class)) {
            return true;
        }

        return false;
    }
}
