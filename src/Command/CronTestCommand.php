<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CronTestCommand extends Command
{
    protected static $defaultName = 'test:cron';

    protected function configure()
    {
        $this
            ->setDescription('Test HerokuApp Cron Panel')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        echo "Hello World! This is my symfony command.";

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return 0;
    }
}
