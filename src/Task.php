<?php

namespace Hurah\Event;

use Hurah\Types\Exception\InvalidArgumentException;
use Hurah\Types\Type\Path;
use Hurah\Types\Util\JsonUtils;
use Psr\Log\LoggerInterface;

class Task
{
    public const SUCCESS = 0;
    public const FAILURE = 1;
    public const RETRY = 2;
    public const INVALID = 3;

    private Path $path;
    private Context $context;
    private LoggerInterface $logger;

    public function __construct(Path $path, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->path = $path;
        $this->context = Context::fromPath($this->path);
    }

    /**
     * @param int $iMaxRetryCount
     *
     * @throws InvalidArgumentException
     */
    public function retry(int $iMaxRetryCount)
    {
        $aData = $this->path->contents()->toJson()->toArray();
        $retryCount =  isset($aData['retry_count']) ? $aData['retry_count'] + 1 : 1;
        $this->logger->info("Retry {$this->path} attempt $retryCount of $iMaxRetryCount");
        if($retryCount > $iMaxRetryCount)
        {
            $this->error("Hit retry limit which is set to: $iMaxRetryCount.");
        }
        $aData['retry_count'] = isset($aData['retry_count']) ? $aData['retry_count'] + 1 : 1;
        $this->path->write(JsonUtils::encode($aData));
    }

    public function getPath(): Path
    {
        return $this->path;
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    public function finish(): void
    {
        $archiveDir = $this->path->dirname(2)->extend('archive')->makeDir();
        $this->logger->info("Moving {$this->path->basename()} to {$archiveDir}");
        $this->path->move($archiveDir);
    }

    public function error(string $sReason): void
    {
        $errorDir = $this->path->dirname(2)->extend('error')->makeDir();
        $this->logger->warning("Event job " . $this->context->getSequence() . "could not be processed");
        $this->logger->warning($sReason);
        $this->logger->warning("Moving {$this->path->basename()} to {$errorDir}");

        $this->path->move($errorDir);
    }

}