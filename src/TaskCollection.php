<?php

namespace Hurah\Event;

use Hurah\Types\Type\AbstractCollectionDataType;
use Hurah\Types\Type\Path;
use Hurah\Types\Type\PathCollection;

class TaskCollection extends AbstractCollectionDataType
{

    public static function fromPathCollection(PathCollection $pathCollection)
    {
        $oInstance = new self();
        foreach($pathCollection as $path)
        {
            $oInstance->add($path);
        }
        return $oInstance;
    }
    public function add(Path $path)
    {
        $this->array[] = new Task($path);
    }
    public function current():Task
    {
        return $this->array[$this->position];
    }
}