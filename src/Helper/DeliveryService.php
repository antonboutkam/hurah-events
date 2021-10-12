<?php

namespace Hurah\Event\Helper;


use DateTime;
use Hurah\Event\Context;
use Hurah\Types\Exception\InvalidArgumentException;
use Hurah\Types\Type\Json;
use Hurah\Types\Type\Path;
use Hurah\Types\Util\JsonUtils;

class DeliveryService
{
    private Path $listenerDirectoryPath;
    private int $sequence;
    private int $count;
    private string $first;
    private string $last;

    /**
     * @param Path $listenerDirectoryPath
     *
     * @throws InvalidArgumentException
     */
    public function __construct(Path $listenerDirectoryPath)
    {
        $this->listenerDirectoryPath = $listenerDirectoryPath;
        $this->loadData();
    }

    /**
     * @param Context $oContext
     *
     * @throws InvalidArgumentException
     */
    public function writeToInbox(Context $oContext)
    {
        $iNextId = $this->nextSequence();
        $oContext->setSequence($iNextId);
        $oContext->setDelivery($this->getTimestamp());
        $oContext->setCount($this->count);

        $jsonFile = $this->listenerDirectoryPath->makeDir()->extend("inbox", "$iNextId.json");
        $jsonFile->write($oContext->toJson());
        $this->persistTracking();
    }

    public function nextSequence(): int
    {
        $this->sequence++;
        $this->count++;
        $this->last = $this->getTimestamp();
        return $this->sequence;
    }

    /**
     * @throws InvalidArgumentException
     */
    private function persistTracking()
    {
        $trackingData = $this->makeTrackingData($this->sequence, $this->count, $this->first, $this->last);
        $this->getTrackingFile()->write($trackingData);
    }

    private function getTrackingFile(): Path
    {
        return $this->listenerDirectoryPath->extend('tracking.json');
    }

    /**
     * @throws InvalidArgumentException
     */
    private function loadData(): void
    {
        $trackingFile = $this->getTrackingFile();
        if (!$trackingFile->exists())
        {
            $trackingFile->write($this->getInitialTrackingData());
        }
        $mTrackingData = $trackingFile->contents()->toJson()->toArray();
        $this->sequence = $mTrackingData['sequence'];
        $this->count = $mTrackingData['count'];
        $this->first = $mTrackingData['first'];
        $this->last = $mTrackingData['last'];
    }

    private function getTimestamp(): string
    {
        return (new DateTime())->format('c');
    }

    /**
     * @param int $sequence
     * @param int $count
     * @param string $first
     * @param string $last
     *
     * @return Json
     * @throws InvalidArgumentException
     */
    private function makeTrackingData(int $sequence, int $count, string $first, string $last): Json
    {
        return JsonUtils::encode([
            'sequence' => $sequence,
            'count' => $count,
            'first' => $first,
            'last' => $last
        ]);
    }

    /**
     * @return Json
     * @throws InvalidArgumentException
     */
    private function getInitialTrackingData(): Json
    {
        return $this->makeTrackingData(0, 0, $this->getTimestamp(), $this->getTimestamp());
    }
}