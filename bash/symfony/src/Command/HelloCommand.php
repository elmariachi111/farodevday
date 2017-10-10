<?php 
namespace Elmariachi\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HelloCommand extends Command
{
    protected function configure()
    {
        $this->setName('elm:hello')
             ->setDescription('says hello')
             ->setHelp('Be nice. Say hello');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Hello');
    }
}