<?php

namespace Hurah\Event;

use Hurah\Types\Type\Path;
use Psr\Log\LoggerInterface;

class Task
{
    private Path $path;
    private Context $context;
    private LoggerInterface $logger;

    public function __construct(Path $path, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->path = $path;
        $this->context = Context::fromPath($this->path);
    }
    public function getContext():Context
    {
        return $this->context;
    }
    public function finish():void
    {
        $archiveDir = $this->path->dirname(2)->extend('archive')->makeDir();
        $this->logger->debug("Moving {$this->path->basename()} to {$archiveDir}");
        $this->path->move($archiveDir);
    }

    public function error(string $sReason):void
    {
        $errorDir = $this->path->dirname(2)->extend('error')->makeDir();
        $this->logger->warning("Event job " . $this->context->getSequence() . "could not be processed");
        $this->logger->info($sReason);
        $this->logger->info("Moving {$this->path->basename()} to {$errorDir}");

        $this->path->move($errorDir);
    }

}