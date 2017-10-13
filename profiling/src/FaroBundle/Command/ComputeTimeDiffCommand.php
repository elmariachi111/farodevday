<?php

namespace FaroBundle\Command;

use Carbon\Carbon;
use FaroBundle\Classes\OpeningHours;
use FaroBundle\Classes\TimeDiff;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ComputeTimeDiffCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('faro:timerange')
            ->addArgument("from", InputArgument::REQUIRED)
            ->addArgument("to", InputArgument::REQUIRED)
            ->setDescription('demonstrates profiler usage');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $openingHours = new OpeningHours([
            Carbon::MONDAY => [[8, 20]],
            Carbon::TUESDAY => [ [9, 12], [14, 18] ],
            Carbon::WEDNESDAY => [[8, 20]],
            Carbon::THURSDAY => [ [7, 10], [14, 17] ],
            Carbon::FRIDAY => [[8, 19]],
            Carbon::SATURDAY => [10,16],
            Carbon::SUNDAY => [12, 16]
        ]);

        $timeDiff = new TimeDiff($openingHours);

        $from = Carbon::parse($input->getArgument("from"));
        $to   = Carbon::parse($input->getArgument("to"));

        $officeMinutes = $timeDiff->diffWithoutNonworkingHours($from, $to);

        $output->writeln($officeMinutes);
    }

}
