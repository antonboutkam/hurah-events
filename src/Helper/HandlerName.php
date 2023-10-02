<?php

namespace Hurah\Event\Helper;

use Hurah\Types\Exception\InvalidArgumentException;
use Hurah\Types\Type\PlainText;
use Hurah\Types\Type\Regex;

class HandlerName
{
    const EVENT_NAME_CONSTRAINT = '/[a-zA-Z0-9]+/';
    private PlainText $oHandlerName;

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(string $sName)
    {
        $oHandlerName = new PlainText($sName);

        if(!$oHandlerName->matches(new Regex(self::EVENT_NAME_CONSTRAINT)))
        {
            throw new InvalidArgumentException("Event name must match " . self::EVENT_NAME_CONSTRAINT);
        }

        $this->oHandlerName = $oHandlerName;
    }

    public function __toString()
    {
        return "{$this->oHandlerName}";
    }

}
