<?php

namespace Hurah\Event\Helper;

use Hurah\Event\EventType;
use Hurah\Types\Exception\InvalidArgumentException;
use Hurah\Types\Exception\NullPointerException;
use Hurah\Types\Type\DirectoryIterator;
use Hurah\Types\Type\Path;
use Hurah\Types\Type\PathCollection;
use Hurah\Types\Type\Regex;


class FileStructureHelper
{
    private Path $eventRoot;

    public function __construct(Path $eventRoot)
    {
        $this->eventRoot = $eventRoot;
    }

    /**
     * Returns a PathCollection containing all the directories that require a copy of an event Collection
     *
     * @param EventType $eventType
     *
     * @return PathCollection
     * @throws NullPointerException
     * @throws InvalidArgumentException
     */
    public function getDeliveryPoints(EventType $eventType): PathCollection
    {
        $oPathCollection = new PathCollection();
        $oPathCollection->add(Path::make($this->eventRoot, $eventType->asArray()));
        while (!$eventType->isVoid())
        {
            $aEventTypeArray = $eventType->pop()->asArray();
            echo join('>>', $aEventTypeArray) . PHP_EOL;
            $oPathCollection->add(Path::make($this->eventRoot, $aEventTypeArray));
        }
        return $oPathCollection;
    }

    /**
     * Return a list of event listeners for a certain event type.
     * @param EventType $eventType
     * @return PathCollection
     * @throws InvalidArgumentException
     */
    public function findEventListeners(EventType $eventType):PathCollection
    {
        $oPathCollection = new PathCollection();
        $oDeliveryPoints = $this->getDeliveryPoints($eventType);
        foreach ($oDeliveryPoints as $oDeliveryPoint)
        {
            $oDeliveryPoint->makeDir();
            $oPathCollection->appendCollection($this->findListenersInPath($oDeliveryPoint));
        }
        return $oPathCollection;
    }

    /**
     * Finds all directories in the Path that end with _listener
     * @throws InvalidArgumentException
     */
    public function findListenersInPath(Path $oPath): PathCollection
    {
        $oDirectoryIterator = $oPath->getDirectoryIterator();
        $oSubFoldersPathCollection = $oDirectoryIterator->toPathCollection(DirectoryIterator::DIR_ONLY);
        $oResult =  $oSubFoldersPathCollection->filter(new Regex('/_listener$/'));
        return $oResult;
    }



}
