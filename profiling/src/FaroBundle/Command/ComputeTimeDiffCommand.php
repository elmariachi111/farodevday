<?php

namespace FaroBundle\Command;

use Carbon\Carbon;
use FaroBundle\Classes\OpeningHours;
use FaroBundle\Classes\OpeningTimeConverter;
use FaroBundle\Classes\TimeDiff;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class ComputeTimeDiffCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('faro:timerange')
            ->addArgument("opening_times", InputArgument::REQUIRED)
            ->addArgument("from", InputArgument::REQUIRED)
            ->addArgument("to", InputArgument::REQUIRED)
            ->setDescription('demonstrates profiler usage');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $stopWatch = new Stopwatch();
        $stopWatch->start('timediff');

        $opening_times = $input->getArgument("opening_times");
        $from = Carbon::parse($input->getArgument("from"));
        $to   = Carbon::parse($input->getArgument("to"));

        $openingTimeConverter = new OpeningTimeConverter();
        $openingHours = $openingTimeConverter->convert($opening_times);

        $timeDiff = new TimeDiff($openingHours);

        $officeMinutes = $timeDiff->diffWithoutNonworkingHours($from, $to);

        $output->writeln($officeMinutes);

        $output->writeln("took: {$stopWatch->stop('timediff')->getDuration()}ms");
    }
}
