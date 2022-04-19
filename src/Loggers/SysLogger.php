<?php

declare(strict_types=1);

namespace MinVWS\Logging\Laravel\Loggers;

use MinVWS\Logging\Laravel\Events\Logging\GeneralLogEvent;
use Illuminate\Support\Facades\Log;

class SysLogger implements LoggerInterface
{
    public function __construct(protected bool $encrypt, protected string $pubKey, protected string $privKey)
    {
    }

    public function log(LogEventInterface $event): void
    {
        $data = $event->getMergedPiiData();


        if ($this->encrypt) {
            $pair = sodium_crypto_box_keypair_from_secretkey_and_publickey($this->privKey, $this->pubKey);
            $nonce = random_bytes(SODIUM_CRYPTO_BOX_NONCEBYTES);
            $encrypted = sodium_crypto_box(json_encode($data, JSON_THROW_ON_ERROR), $nonce, $pair);

            Log::info('AuditLog: ' . base64_encode($nonce . $encrypted));
        } else {
            Log::info('AuditLog: ' . base64_encode(json_encode($data, JSON_THROW_ON_ERROR)));
        }
    }

    public function canHandleEvent(LogEventInterface $event): bool
    {
        if (is_a($event, GeneralLogEvent::class)) {
            return true;
        }

        return false;
    }
}
