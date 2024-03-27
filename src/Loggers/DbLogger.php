<?php

declare(strict_types=1);

namespace MinVWS\Logging\Laravel\Loggers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use JsonException;

class DbLogger implements LoggerInterface
{
    // The eloquent model that will be created
    protected string $modelFqcn;

    // An additional factory that will generate the model for us
    protected ?ModelFactoryInterface $modelFactory;

    protected string|null $pair;

    public function __construct(
        protected bool $encrypt,
        protected string $pubKey,
        protected string $privKey,
        string $modelFqcn,
        ?ModelFactoryInterface $modelFactory = null
    ) {
        $this->modelFqcn = $modelFqcn;
        $this->modelFactory = $modelFactory;
        if ($this->encrypt) {
            $this->pair = sodium_crypto_box_keypair_from_secretkey_and_publickey($this->privKey, $this->pubKey);
        }
    }

    public function log(LogEventInterface $event): void
    {
        $data = $event->getLogData();

        $piiData = $event->getPiiLogData();

        if ($this->encrypt && $this->pair !== null) {
            $nonce = random_bytes(SODIUM_CRYPTO_BOX_NONCEBYTES);
            $encrypted = sodium_crypto_box(json_encode($piiData, JSON_THROW_ON_ERROR), $nonce, $this->pair);

            $piiData = $nonce . $encrypted;
        } else {
            $piiData = json_encode($piiData, JSON_THROW_ON_ERROR);
        }

        $data['pii_context'] = base64_encode($piiData);

        if (isset($data['request'])) {
            Log::warning('Deprecated: `request` key is renamed to context, please update your code.');
            $data['context'] = $data['request'];
            unset($data['request']);
        }

        // Create the model based on the FQCN or the factory
        if ($this->modelFactory) {
            $model = $this->modelFactory->create($this->modelFqcn, $data);
        } else {
            $model = new $this->modelFqcn();
        }

        /** @var Model $model */
        $model::create($data);      // @phpstan-ignore-line
    }

    public function canHandleEvent(LogEventInterface $event): bool
    {
        return true;
    }
}
