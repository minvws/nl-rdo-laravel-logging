<?php

declare(strict_types=1);

namespace App\Events\Rabbitmq;

use RabbitEvents\Publisher\Support\AbstractPublishableEvent;

class UnicornEvent extends AbstractPublishableEvent
{
    private int $time;

    public function __construct()
    {
        $this->time = time();
    }

    public function publishEventKey(): string
    {
        return config('rabbitevents.prefix') . '.unicorn';
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
