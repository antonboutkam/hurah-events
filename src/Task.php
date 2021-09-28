<?php

namespace Hurah\Event;

use Hurah\Types\Type\Path;

class Task
{
    private Path $path;
    public function __construct(Path $path)
    {
        $this->path = $path;
    }
    public function getContext():Context
    {
        return Context::fromPath($this->path);
    }
    public function finish():void
    {
        $archiveDir = $this->path->dirname(2)->extend('archive')->makeDir();
        $this->path->move($archiveDir);
    }

    public function error():void
    {
        $errorDir = $this->path->dirname(2)->extend('error')->makeDir();
        $this->path->move($errorDir);
    }

}