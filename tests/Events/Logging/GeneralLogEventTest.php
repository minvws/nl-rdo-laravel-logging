<?php

declare(strict_types=1);

namespace MinVWS\Logging\Laravel\Tests\Events\Logging;

use MinVWS\Logging\Laravel\Events\Logging\RegistrationLogEvent;
use PHPUnit\Framework\TestCase;

class GeneralLogEventTest extends TestCase
{
    public function testEvent()
    {
        $data = [
            'field_1' => 12,
            'old' => [
                'feature' => [ 'foo', 'bar', 'baz' ],
            ],
            'new' => [
                'feature' => [ 'foo', 'bar' ],
            ]
        ];

        $piiData = [
            'field_2' => 34,
            'old' => [
                'name' => 'john',
            ],
            'new' => [
                'name' => 'billy',
            ]
        ];

        $event = (new RegistrationLogEvent())->withData($data)->withPiiData($piiData);

        $result = [
            'field_1' => 12,
            'field_2' => 34,
            'old' => [
                'feature' => [ 'foo', 'bar', 'baz' ],
                'name' => 'john',
            ],
            'new' => [
                'feature' => [ 'foo', 'bar' ],
                'name' => 'billy',
            ]
        ];
        $this->assertEquals($result, $event->getMergedPiiData()['request']);
    }
}
