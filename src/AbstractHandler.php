<?php

namespace Hurah\Event;

use Exception;
use Hurah\Event\Helper\HandlerName;
use Hurah\Types\Exception\InvalidArgumentException;
use Hurah\Types\Exception\RuntimeException;
use Hurah\Types\Type\Path;
use Hurah\Types\Type\PathCollection;
use Hurah\Types\Type\Regex;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use function json_encode;

abstract class AbstractHandler implements HandlerInterface
{
    private array $aFailureInfo = [];
    private EventType $eventType;
    private Path $inbox;

    public function __construct(HandlerName $name, EventType $eventType, Path $eventRoot)
    {
        $this->inbox = $eventRoot->extend($eventType->asArray(), $name . '_listener', 'inbox')->makeDir();
        $this->eventType = $eventType;
    }
    protected function addFailureInfo(string ...$mLine)
    {
        foreach($mLine as $sLine)
        {
            $this->aFailureInfo[] = $sLine;
        }
    }
    private function getFailureInfo():array
    {
        return $this->aFailureInfo;
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

    private function logConsole(string $sMessage):void
    {
        $this->getLogger()->warning($sMessage);
    }

    /**
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function handle(): void
    {
        foreach($this->getQueue() as $oTask)
        {
            try
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
                elseif ($iTaskStatus === Task::FAILURE)
                {
                    $this->logConsole("Processing job resulted in a failure");
                    $this->logConsole("File:" . $oTask->getPath());
                    $this->logConsole("Context data:" . $oTask->getContext()->toJson());
                    foreach($this->getFailureInfo() as $i => $sLine)
                    {
                        $this->logConsole("{$i}.) {$sLine}");
                    }
                }
            }
            catch (Exception $e)
            {
                $this->logConsole($e->getMessage());
                $this->logConsole($e->getFile() . ':' . $e->getLine());

                foreach($e->getTrace() as $item)
                {
                    $this->logConsole(json_encode($item));
                }
            }

        }
    }
    abstract protected function handleTask(Context $oContext):int;
    protected function maxAttempts():int
    {
        return 0;
    }

    public function getType(): EventType
    {
        return $this->eventType;
    }

}