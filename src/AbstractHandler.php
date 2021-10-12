<?php

namespace Hurah\Event;

use Hurah\Event\Helper\HandlerName;
use Hurah\Types\Exception\InvalidArgumentException;
use Hurah\Types\Type\Path;
use Hurah\Types\Type\PathCollection;
use Hurah\Types\Type\Regex;
use Psr\Log\LoggerInterface;

abstract class AbstractHandler implements HandlerInterface
{
    private EventType $eventType;
    private Path $inbox;

    public function __construct(HandlerName $name, EventType $eventType, Path $eventRoot)
    {
        $this->inbox = $eventRoot->extend($eventType->asArray(), $name . '_listener', 'inbox')->makeDir();
        $this->eventType = $eventType;
    }

    private function getInboxFiles():PathCollection
    {
        return $this->inbox->getDirectoryIterator()->toPathCollection();
    }
    /**
     * @return TaskCollection
     * @throws InvalidArgumentException
     */
    public function getQueue(): TaskCollection
    {
        $oFilter = new Regex('/[0-9]+.json$/');

        return TaskCollection::fromPathCollection($this->getInboxFiles()->filter($oFilter), $this->getLogger());
    }

    abstract public function getLogger():LoggerInterface;

    /**
     * @throws InvalidArgumentException
     */
    public function handle(): void
    {
        foreach($this->getQueue() as $oTask)
        {
            $this->getLogger()->debug("Processing {$oTask->getPath()}");
            $iTaskStatus = $this->handleTask($oTask->getContext());
            if($iTaskStatus == Task::SUCCESS)
            {
                $oTask->finish();
            }
            elseif($iTaskStatus === Task::INVALID)
            {
                $oTask->error("Task was invalid");
            }
            elseif($iTaskStatus === Task::RETRY)
            {
                $oTask->retry($this->maxAttempts());
            }
        }
    }
    abstract protected function handleTask(Context $context):int;
    protected function maxAttempts():int
    {
        return 0;
    }

    public function getType(): EventType
    {
        return $this->eventType;
    }

}