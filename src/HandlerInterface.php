<?php

namespace Hurah\Event;

interface HandlerInterface
{
    public function handle(Context $context):void;
    public function getType():EventType;
}