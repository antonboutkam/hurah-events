<?php

namespace Test\Hurah\Event\Helper;

use Hurah\Event\Context;
use Hurah\Event\Helper\Endpoint;
use Hurah\Types\Type\Path;
use PHPUnit\Framework\TestCase;
use function dirname;

class EndpointTest extends TestCase
{

    public function testDestruction()
    {
        $path = Path::make(dirname(__DIR__, 2))->extend('data')->makeDir();
        $endpoint = new Endpoint($path);
        $endpoint->inbox(new Context("1"));

    }
}
