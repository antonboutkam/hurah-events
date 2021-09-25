<?php

namespace Hurah\Event\Commands\Generator;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateSupervisorConfig extends Command
{
    public function configure()
    {
        $this->setName('create:listener:supervisor');
        $this->setDescription("Generate a config file for superisord");
        $this->addArgument('name', InputArgument::REQUIRED, "A name for your handler, for example \"product-translator\"");
        $this->addArgument('topic', InputArgument::REQUIRED, "The event type, for example /product/created");
        $this->addOption('output-dir', 'o', InputOption::VALUE_OPTIONAL, "Where to store the generated file(s), default is current directory");
        $this->addOption('create-inotify', 'i', InputOption::VALUE_OPTIONAL, "Should we generate inotifywait script, default false");
        $this->addOption('create-supervisor', 's', InputOption::VALUE_OPTIONAL, "Should we generate supervisord files to, default false");

        parent::configure();
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        return Command::SUCCESS;
    }


}