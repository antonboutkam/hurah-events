<?php

namespace Test\Hurah\Event;

use Hurah\Event\Context;
use Hurah\Event\Dispatcher;
use Hurah\Event\EventType;
use Hurah\Types\Exception\InvalidArgumentException;
use Hurah\Types\Type\Path;
use Hurah\Types\Type\PathCollection;
use Hurah\Types\Type\Regex;


class DispatcherTest extends BaseTestCase
{
    /**
     * Test of het afvuren van events werkt
     * @return void
     * @throws InvalidArgumentException
     */
    public function testDispatch()
    {
        $productRoot = $this->eventRoot->extend('product');

        /**
         * First we register a bunch of listener locations.
         */
        $oEventTargetLocations = PathCollection::fromPaths(
            $productRoot->extend('price_calculator_listener', 'inbox')->makeDir(),
            $productRoot->extend('stored', 'image_scaler_listener', 'inbox')->makeDir(),
            $productRoot->extend('stored', 'translation_listener', 'inbox')->makeDir()
        );

        /**
         * Check what files are already there.
         */
        $aPreEventJsonFiles = $this->countJsonFilesInFolders($oEventTargetLocations);

        /**
         * Fire a product stored event, this event should recurse up to product.
         */
        $oEventType = new EventType('product', 'stored');
        $oContext = new Context(['product_id' => 1, 'created_by' => __CLASS__]);
        $oDispatcher = new Dispatcher($this->eventRoot);
        $oDispatcher->dispatch($oEventType, $oContext);

        /**
         * Check what files are now there.
         */
        $aAfterEventJsonFiles = $this->countJsonFilesInFolders($oEventTargetLocations);

        foreach($aPreEventJsonFiles as $iIndex => $iCount)
        {
            $this->assertEquals($iCount  + 1, $aAfterEventJsonFiles[$iIndex]);
        }

    }

    /**
     * @param Path $oDestination
     *
     * @return int
     * @throws InvalidArgumentException
     */
    private function countJsonFilesInFolder(Path $oDestination): int
    {
        $oDestinationFiles = $oDestination->getDirectoryIterator()->toPathCollection();
        $oDestinationJsonFiles = $oDestinationFiles->filter(new Regex('/.json$/'));

        if($oDestinationJsonFiles->isEmpty())
        {
            return 0;
        }
        return $oDestinationJsonFiles->length();
    }

    /**
     * @param PathCollection $oDestinations
     * @return array
     * @throws InvalidArgumentException
     */
    private function countJsonFilesInFolders(PathCollection $oDestinations): array
    {
        $aFileCounts = [];
        $i = 0;
        foreach ($oDestinations as $oDestination)
        {
            $aFileCounts[$i] = $this->countJsonFilesInFolder($oDestination);
            $i++;
        }
        return $aFileCounts;
    }

}
