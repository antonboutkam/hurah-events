<?php

namespace Hurah\Event;

use Hurah\Types\Type\AbstractCollectionDataType;

class HandlerCollection extends AbstractCollectionDataType
{
    public function add(HandlerInterface $handler)
    {
        $this->array[] = $handler;
    }
    public function current():HandlerInterface
    {
        return $this->array[$this->position];
    }
}