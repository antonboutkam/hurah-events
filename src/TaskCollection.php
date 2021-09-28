<?php

namespace Hurah\Event;

use Hurah\Types\Type\AbstractCollectionDataType;
use Hurah\Types\Type\Path;
use Hurah\Types\Type\PathCollection;
use Psr\Log\LoggerInterface;

class TaskCollection extends AbstractCollectionDataType
{

    public static function fromPathCollection(PathCollection $pathCollection, LoggerInterface $logger)
    {
        $oInstance = new self();
        foreach($pathCollection as $path)
        {
            $oInstance->add($path, $logger);
        }
        return $oInstance;
    }
    public function add(Path $path, LoggerInterface $logger)
    {
        $this->array[] = new Task($path, $logger);
    }
    public function current():Task
    {
        return $this->array[$this->position];
    }
}