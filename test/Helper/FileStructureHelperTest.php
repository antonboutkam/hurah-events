<?php

namespace Test\Hurah\Event\Helper;

use Hurah\Event\EventType;
use Hurah\Event\Helper\FileStructureHelper;
use Hurah\Types\Type\Path;
use PHPUnit\Framework\TestCase;
use Test\Hurah\Event\BaseTestCase;
use function dirname;
use function var_dump;
use const PHP_EOL;

class FileStructureHelperTest extends BaseTestCase
{

    public function testGetDeliveryPoints()
    {
        $oPathCollection = $this->fileStructureHelper->getDeliveryPoints(new EventType('product', 'store'));

        $this->assertEquals($this->eventRoot->extend('product','store'), $oPathCollection->current());
        $oPathCollection->next();
        $this->assertEquals($this->eventRoot->extend('product'), $oPathCollection->current());
        $oPathCollection->next();
        $this->assertEquals($this->eventRoot, $oPathCollection->current());
    }

    private function getExpectedProductStoredListeners():array
    {
        return [
            $this->eventRoot->extend('product', 'stored', 'image_scaler_listener'),
            $this->eventRoot->extend('product', 'stored', 'translation_listener'),
            $this->eventRoot->extend('product', 'price_calculator_listener'),
        ];
    }
    public function testFindListeners()
    {
        $oEventType = new EventType('product', 'stored');
        $oEventTypeListeners = $this->fileStructureHelper->findEventListeners($oEventType);
        $this->assertEquals($this->getExpectedProductStoredListeners(), $oEventTypeListeners->toArray());
        $this->assertEquals(3, $oEventTypeListeners->length());


    }
    public function testFindListenersInPath()
    {
        $oProductEventDirectory = $this->eventRoot->extend('product');
        $oListeners = $this->fileStructureHelper->findListenersInPath($oProductEventDirectory);
        $this->assertEquals(1, $oListeners->length());
        $oExpectedToBeFoundListener = Path::make($oProductEventDirectory, 'price_calculator_listener');
        $this->assertEquals($oExpectedToBeFoundListener, $oListeners->current());

        $oProductStoredEventDirectory = $oProductEventDirectory->extend('stored');
        $oListeners = $this->fileStructureHelper->findListenersInPath($oProductStoredEventDirectory);
        $this->assertEquals(2, $oListeners->length());

        $oExpectedImageScalerListener = Path::make($oProductStoredEventDirectory, 'image_scaler_listener');
        $this->assertEquals($oExpectedImageScalerListener, $oListeners->current());
        $oListeners->next();
        $oExpectedTranslationListener = Path::make($oProductStoredEventDirectory, 'translation_listener');
        $this->assertEquals($oExpectedTranslationListener, $oListeners->current());

    }
}
