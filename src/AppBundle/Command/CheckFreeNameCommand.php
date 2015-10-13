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
        $total = 5000;
        $oneTimeProcessLimit = 10;
        $limit = 100;
        $offset = 0;

        /* @var $processes Process[] */
        /* @var $queue Process[] */
        $queue = $processes = [];
        $count = $total / $limit;
        while ($count--) {
            $queue[] = new Process("app/console fn:check:name:process --offset=$offset --limit=$limit");
            $offset += $limit;
        }

        do {
            /* @var $process Process */
            foreach ($processes as $key => $process) {
                if ($process->isRunning()) {
                    $output->writeln("Running: <info>{$process->getPid()}</info>");
                    continue;
                }
                $output->writeln($process->getOutput());
                unset($processes[$key]);
            }

            while ($queue && count($processes) < $oneTimeProcessLimit) {
                $process = array_shift($queue);
                $process->start();
                $processes[] = $process;
            }

            if (empty($processes)) {
                break;
            }
            $output->writeln('Sleep');
            sleep(2);
        } while (true);

        $duration = microtime(true) - $startTime;
        $output->writeln("Total duration: <info>$duration</info>");
        $output->writeln("Total tries: <info>$total</info>");
    }
}