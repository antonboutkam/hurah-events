<?php

namespace Hurah\Event;

use Hurah\Types\Type\Json;
use Hurah\Types\Type\Path;

interface ContextInterface
{
    public function __construct($data);

    public static function fromPath(Path $path): ContextInterface;

    public function getPayload();

    public function toJson(): Json;

    public function getSequence(): int;

    public function setSequence(int $sequence);

    public function setCount(int $count);

    public function setDelivery(string $delivery);
}
