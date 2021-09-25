<?php

namespace Hurah\Event\Helper;


use \DateTime;
use Hurah\Event\Context;
use Hurah\Types\Type\Json;
use Hurah\Types\Type\Path;
use Hurah\Types\Util\JsonUtils;
use function array_shift;
use function rtrim;
use function var_dump;

class Endpoint
{
    private Path $endpointRoot;
    private int $sequence;
    private int $count;
    private string $first;
    private string $last;
    private array $lastTen;

    public function __construct(Path $endpointRoot)
    {
        $this->endpointRoot = $endpointRoot;
        $this->loadData();
    }
    public function __destruct()
    {
        $this->persist();
    }
    private function persist()
    {
        $trackingData = $this->makeTrackingData($this->sequence, $this->count, $this->first, $this->last, $this->lastTen);
        $this->getTrackingFile()->write($trackingData);
    }
    private function getTrackingFile():Path{
        return $this->endpointRoot->extend('tracking.json');
    }
    private function loadData():void
    {
        $trackingFile = $this->getTrackingFile();
        if(!$trackingFile->exists())
        {
            $trackingFile->write($this->getInitialTrackingData());
        }
        $mTrackingData = $trackingFile->contents()->toJson()->toArray();
        $this->sequence = $mTrackingData['sequence'];
        $this->count = $mTrackingData['count'];
        $this->first = $mTrackingData['first'];
        $this->last = $mTrackingData['last'];
        $this->lastTen = $mTrackingData['last_10_log'];
    }
    private function getTimestamp():string
    {
        return (new DateTime())->format('c');
    }
    private function makeTrackingData(int $sequence, int $count, string $first, string $last, array $lastTen):Json
    {
        return JsonUtils::encode([
            'sequence' => $sequence,
            'count' => $count,
            'first' => $first,
            'last' => $last,
            'last_10_log' => $lastTen
        ]);
    }
    private function getInitialTrackingData():Json
    {
        return $this->makeTrackingData(0, 0, $this->getTimestamp(), $this->getTimestamp(), []);
    }

    public function inbox(Context $data)
    {
        $iNextId = $this->nextSequence();
        $data->setSequence($iNextId);
        $data->setDelivery($this->getTimestamp());
        $data->setCount($this->count);

        $jsonFile = $this->endpointRoot->makeDir()->extend("{$iNextId}.json");
        $jsonFile->write($data->toJson());
    }

    public function nextSequence():int
    {
        $this->sequence++;
        $this->count++;
        $this->last = $this->getTimestamp();
        return $this->sequence;
    }
}