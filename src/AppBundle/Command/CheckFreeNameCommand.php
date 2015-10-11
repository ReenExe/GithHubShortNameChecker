<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckFreeNameCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('fn:check:name');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $startTime = microtime(true);
        $this->getContainer()->get('fn.check_name_service')->progress(100);
        $duration = microtime(true) - $startTime;
        $output->writeln("Duration: <info>$duration</info>");
    }
}