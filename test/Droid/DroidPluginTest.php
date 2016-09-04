<?php

namespace Droid\Test\Plugin\Shell;

use PHPUnit_Framework_TestCase;

use Droid\Plugin\Shell\DroidPlugin;

class DroidPluginTest extends PHPUnit_Framework_TestCase
{
    protected $plugin;

    protected function setUp()
    {
        $this->plugin = new DroidPlugin('droid');
    }

    public function testGetCommandsReturnsAllCommands()
    {
        $this->assertSame(
            array(
                'Droid\Plugin\Shell\Command\ShellExecCommand',
            ),
            array_map(
                function ($x) {
                    return get_class($x);
                },
                $this->plugin->getCommands()
            )
        );
    }
}
