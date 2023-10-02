<?php

namespace Hurah\Event;

use Hurah\Types\Type\Json;
use Hurah\Types\Util\JsonUtils;

abstract class AbstractContext implements ContextInterface
{
    protected array $data;
    protected int $sequence;
    protected int $count;
    protected string $delivery;

    public function __construct($data)
    {
        $this->data = ['payload' => $data];
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
