<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class CheckFreeNameCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('fn:check:name');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $startTime = microtime(true);
        $limit = 125;
        $offset = 0;
        $count = 5;
        $tries = $count * $limit;

        /* @var $processes Process[] */
        $processes = [];
        while ($count--) {
            $process = new Process("app/console fn:check:name:process --offset=$offset --limit=$limit");
            $process->start();
            $processes[] = $process;
            $offset += $limit;
        }

        sleep(2);

        do {
            foreach ($processes as $key => $process) {
                if ($process->isRunning()) {
                    $output->writeln("Running: <info>{$process->getPid()}</info>");
                    continue;
                }
                $output->writeln($process->getOutput());
                unset($processes[$key]);
            }
            if (empty($processes)) {
                break;
            }
            $output->writeln('Sleep');
            sleep(2);
        } while (true);

        $duration = microtime(true) - $startTime;
        $output->writeln("Total duration: <info>$duration</info>");
        $output->writeln("Total tries: <info>$tries</info>");
    }
}