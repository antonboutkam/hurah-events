<?php

namespace Hurah\Event\Commands\Worker;

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
        $this->addArgument('root_dir', InputArgument::REQUIRED, "The root directory where events are stored");
        $this->addArgument('handler', InputArgument::REQUIRED, "Fully qualified class name of handler");
        $this->addArgument('trigger_file', InputArgument::REQUIRED, "Path to the file containing the context data");

        parent::configure();
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $handler = $input->getArgument('handler');
        $rootDir = $input->getArgument('root_dir');
        $triggerFile = $input->getArgument('trigger_file');


        if($handler = new $handlerClassName)

        return Command::SUCCESS;
    }
}
