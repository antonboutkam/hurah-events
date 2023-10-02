<?php

namespace Hurah\Event;

use Hurah\Event\AbstractContext;
use Hurah\Types\Type\Path;

/**
 *
 */
class Context extends AbstractContext
{

    /**
     * Constructor
     * @generate [properties, getters, setters]
     */
    public function __construct($data)
    {
        parent::__construct($data);
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
}
