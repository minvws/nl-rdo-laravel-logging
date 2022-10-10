<?php

declare(strict_types=1);

namespace MinVWS\Logging\Laravel\Events\Rabbitmq;

use RabbitEvents\Publisher\Support\AbstractPublishableEvent;

class UnicornEvent extends AbstractPublishableEvent
{
    private string $prefix;
    private int $time;

    public function __construct(string $prefix = "")
    {
        $this->time = time();
        $this->prefix = $prefix;
    }

    public function publishEventKey(): string
    {
        return $this->prefix . '.unicorn';
    }

    private function getUnicorn(): string
    {
        return ""
          . "    \. \n     \'.      ;. \n      \ '. ,--''-.~-~-'-, \n       \,-' ,-.   '.~-~-~~, "
          . "\n     ,-'   (###)    \-~'~=-. \n _,-'       '-'      \=~-\"~~', \n/o               "
          . "     \~-\"\"~=-, \n\__                    \=-,~\"-~, \n   \"\"\"===-----.         \~"
          . "=-\"~-. \n               \         \*=~-\" \n                \         \"=====----  "
          . "\n                 \ \n                  \ \n";
    }

    public function toPublish(): array
    {
        return [
            "message" => $this->getUnicorn(),
            "routingKey" => $this->publishEventKey(),
            "time" => $this->time,
        ];
    }
}
