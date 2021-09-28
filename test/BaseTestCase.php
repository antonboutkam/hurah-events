<?php

namespace Test\Hurah\Event;

use Hurah\Event\Helper\FileStructureHelper;
use Hurah\Types\Type\Path;
use PHPUnit\Framework\TestCase;
use function dirname;

class BaseTestCase extends TestCase
{

    public Path $eventRoot;
    public FileStructureHelper $fileStructureHelper;

    public function setUp():void
    {
        $this->eventRoot = Path::make(__DIR__, 'data', 'MockEventRoot');
        $this->fileStructureHelper = new FileStructureHelper($this->eventRoot);

    }

}