<?php

namespace Hurah\Event;

use Hurah\Types\Type\Json;
use Hurah\Types\Type\Path;
use Hurah\Types\Util\JsonUtils;

class Context implements ContextInterface
{
    private array $data;
    private int $sequence;
    private int $count;
    private string $delivery;

    public function __construct($data)
    {
        $this->data = ['payload' => $data];
    }
    public static function fromPath(Path $path):ContextInterface
    {
        $data = $path->contents()->toJson()->toArray();
        $object = new self($data['payload']);
        $object->setSequence($data['sequence']);
        $object->setCount($data['count']);
        $object->setDelivery($data['delivery']);
        $object->data = $data;
        return $object;
    }

    public function getPayload()
    {
        return $this->data['payload'] ?? null;
    }
    public function toJson():Json
    {
        $this->data['sequence'] = $this->sequence;
        $this->data['count'] = $this->count;
        $this->data['delivery'] = $this->delivery;

        return JsonUtils::encode($this->data);
    }

    public function getSequence():int
    {
        return $this->sequence;
    }
    public function setSequence(int $sequence)
    {
        $this->sequence = $sequence;
    }

    public function setCount(int $count)
    {
        $this->count = $count;
    }
    public function setDelivery(string $delivery)
    {
        $this->delivery = $delivery;
    }
}
