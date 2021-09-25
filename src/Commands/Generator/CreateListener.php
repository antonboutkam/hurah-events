<?php

namespace Hurah\Event\Commands\Generator;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateListener extends Command
{
    public function configure()
    {
        $this->setName('create:listener');
        $this->setDescription("Generate the code that is needed to deploy a listener");
        $this->addArgument('name', InputArgument::REQUIRED, "Name of your handler, example: \"product-translator\"");
        $this->addArgument('event', InputArgument::REQUIRED, "Name / path of the event, example: /product/created");
        $this->addOption('listener-dir', 'o', InputOption::VALUE_OPTIONAL, "Where to store the generated php listener file");
        $this->addOption('supervisor-dir', 's', InputOption::VALUE_OPTIONAL, "Where to store the supervisord config file");

        parent::configure();
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        return Command::SUCCESS;
    }


}