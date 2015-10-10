<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FillNamesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('fn:fill:default');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $startTime = microtime(true);
        $this->getContainer()->get('fn.default_fill_service')->fill();
        $duration = microtime(true) - $startTime;
        $output->writeln("Duration: <info>$duration</info>");
    }
}