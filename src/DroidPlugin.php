<?php

namespace Droid\Plugin\Shell;

use Symfony\Component\Process\ProcessBuilder;

use Droid\Plugin\Shell\Command\ShellExecCommand;

class DroidPlugin
{
    public function __construct($droid)
    {
        $this->droid = $droid;
    }

    public function getCommands()
    {
        return array(
            new ShellExecCommand(new ProcessBuilder),
        );
    }
}
