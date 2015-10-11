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
        $limit = 100;
        $offset = 0;
        $count = 3;

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
                    $output->writeln('<info>Running</info>');
                    continue;
                }
                $output->writeln($process->getOutput());
                unset($processes[$key]);
            }
            if (empty($processes)) {
                break;
            }
            sleep(2);
        } while (true);

        $duration = microtime(true) - $startTime;
        $output->writeln("Duration: <info>$duration</info>");
        $output->writeln('<info>Success</info>');
    }
}