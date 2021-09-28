<?php

namespace Hurah\Event\Commands;

use Hurah\Event\EventType;
use Hurah\Event\HandlerInterface;
use Hurah\Event\Helper\HandlerName;
use Hurah\Types\Exception\InvalidArgumentException;
use Hurah\Types\Type\Path;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Runner extends Command
{
    public function configure()
    {
        $this->setName('worker:runner');
        $this->setDescription("Will be executed as soon as a file is added to an event directory");
        $this->addArgument('handler_name', InputArgument::REQUIRED, "Choose a name for the handler");
        $this->addArgument('event_type', InputArgument::REQUIRED, "Type of event this handler is used for example product/stored");
        $this->addArgument('handler', InputArgument::REQUIRED, "Fully qualified class name of the Handler");
        $this->addArgument('event_root', InputArgument::REQUIRED, "Path of the root of all events");

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     * @throws InvalidArgumentException
     * @throws ReflectionException
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $sHandlerName = $input->getArgument('handler_name');
        $sEventType = $input->getArgument('event_type');
        $sHandler = $input->getArgument('handler');
        $sEventRoot = $input->getArgument('event_root');

        $oHandlerName = new HandlerName($sHandlerName);
        $oEventRoot = Path::make($sEventRoot);
        $oEventType = EventType::fromString($sEventType);

        $oEventHandler = $this->makeHandler($sHandler, $oHandlerName, $oEventType, $oEventRoot);
        $oEventHandler->handle();
        return Command::SUCCESS;
    }

    /**
     * @param string $sFullyQualifiedClassName
     * @param HandlerName $handlerName
     * @param EventType $eventType
     * @param Path $eventRoot
     *
     * @return HandlerInterface
     * @throws InvalidArgumentException
     * @throws ReflectionException
     */
    private function makeHandler(string $sFullyQualifiedClassName, HandlerName $handlerName, EventType $eventType, Path $eventRoot): HandlerInterface
    {
        $oReflectionClass = new ReflectionClass($sFullyQualifiedClassName);

        /** @var HandlerInterface $oHandler */
        $oHandler = $oReflectionClass->newInstance($handlerName, $eventType, $eventRoot);

        if ($oReflectionClass->implementsInterface(HandlerInterface::class))
        {
            return $oHandler;
        }
        throw new InvalidArgumentException("Handler is expected to be an instance of HandlerInterface");
    }
}
