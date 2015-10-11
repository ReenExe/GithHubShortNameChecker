<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CheckFreeNameProcessCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('fn:check:name:process')
            ->addOption('offset', null, InputOption::VALUE_OPTIONAL)
            ->addOption('limit', null, InputOption::VALUE_OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $offset = (int) $input->getOption('offset');
        $limit = (int) $input->getOption('limit');

        $startTime = microtime(true);
        $this->getContainer()->get('fn.check_name_service')->progress($offset, $limit);
        $duration = microtime(true) - $startTime;
        $output->writeln("Duration: <info>$duration</info>");
    }
}