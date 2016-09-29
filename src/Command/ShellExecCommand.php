<?php

namespace Droid\Plugin\Shell\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Console\Input\InputArgument;

class ShellExecCommand extends Command
{
    private $processBuilder;

    public function __construct(ProcessBuilder $builder, $name = null)
    {
        $this->processBuilder = $builder;
        return parent::__construct($name);
    }

    public function configure()
    {
        $this
            ->setName('shell:exec')
            ->setDescription('Execute the given command line in Bash.')
            ->addArgument(
                'command-line',
                InputArgument::REQUIRED,
                'The command and arguments to be executed by the shell.'
            )
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $p = $this->getProcess(
            array(
                'bash',
                '-c',
                $input->getArgument('command-line')
            )
        );

        if ($p->run()) {
            $output->writeln(
                sprintf(
                    '<error>I have failed to execute the given command line. Code %d: %s</error>',
                    $p->getExitCode(),
                    $p->getErrorOutput()
                )
            );
            return $p->getExitCode();
        }
        $output->writeln('<info>I have successfully executed the given command line.</info>');
        $output->write($p->getOutput());
    }

    /**
     * @return \Symfony\Component\Process\Process
     */
    private function getProcess($arguments, $timeout = 0.0)
    {
        return $this
            ->processBuilder
            ->setArguments($arguments)
            ->setTimeout($timeout)
            ->getProcess()
        ;
    }
}
