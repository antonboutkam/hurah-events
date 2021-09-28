<?php

namespace Test\Hurah\Event\Helper;

use Hurah\Event\Context;
use Hurah\Event\Helper\DeliveryService;
use Hurah\Types\Type\Path;
use Test\Hurah\Event\BaseTestCase;
use function dirname;
use function var_dump;

class DeliveryServiceTest extends BaseTestCase
{


    public function testDelivery()
    {
        $listenerDirectoryPath = $this->eventRoot->extend('product', 'price_calculator_listener');
        $trackingFile = $listenerDirectoryPath->extend('tracking.json');
        $previousTrackingFileContents = $trackingFile->contents()->toJson()->toArray();

        $oContext = new Context(['product_id' => 1]);
        $deliveryService = new DeliveryService($listenerDirectoryPath);
        $deliveryService->writeToInbox($oContext);

        $trackingFile = $trackingFile->contents()->toJson();
        $newTrackingFileContents = $trackingFile->toArray();

        $this->assertEquals($previousTrackingFileContents['sequence'] + 1, $newTrackingFileContents['sequence']);
        $this->assertEquals($previousTrackingFileContents['first'], $newTrackingFileContents['first']);
    }

    public function testDestruction()
    {
        $path = Path::make(dirname(__DIR__, 2))->extend('data')->makeDir();
        $endpoint = new DeliveryService($path);
        $endpoint->writeToInbox(new Context("1"));

    }
}
