<?php
namespace Droid\Test\Plugin\Shell\Command;

use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

use Droid\Plugin\Shell\Command\ShellExecCommand;

class ShellExecCommandTest extends PHPUnit_Framework_TestCase
{
    protected $app;
    protected $process;
    protected $processBuilder;
    protected $tester;

    protected function setUp()
    {
        $this->process = $this
            ->getMockBuilder(Process::class)
            ->disableOriginalConstructor()
            ->setMethods(array('run', 'getOutput', 'getErrorOutput', 'getExitCode'))
            ->getMock()
        ;
        $this->processBuilder = $this
            ->getMockBuilder(ProcessBuilder::class)
            ->setMethods(array('setArguments', 'getProcess'))
            ->getMock()
        ;

        $command = new ShellExecCommand($this->processBuilder);

        $this->app = new Application;
        $this->app->add($command);

        $this->tester = new CommandTester($command);
    }

    public function testCommandWillFail()
    {
        $this
            ->processBuilder
            ->expects($this->once())
            ->method('setArguments')
            ->with(array('bash', '-c', 'echo "Hello world"'))
            ->willReturnSelf()
        ;
        $this
            ->processBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->willReturn($this->process)
        ;
        $this
            ->process
            ->expects($this->once())
            ->method('run')
            ->willReturn(1)
        ;
        $this
            ->process
            ->expects($this->atLeastOnce())
            ->method('getExitCode')
            ->willReturn(1)
        ;
        $this
            ->process
            ->expects($this->once())
            ->method('getErrorOutput')
            ->willReturn('something went awry')
        ;

        $this->tester->execute(
            array(
                'command' => $this->app->find('shell:exec'),
                'command-line' => 'echo "Hello world"',
            )
        );

        $this->assertRegExp(
            '/I have failed to execute the given command line\. Code 1: something went awry/',
            $this->tester->getDisplay()
        );
    }

    public function testCommandWillSucceed()
    {
        $this
            ->processBuilder
            ->expects($this->once())
            ->method('setArguments')
            ->with(array('bash', '-c', 'echo "Hello world"'))
            ->willReturnSelf()
        ;
        $this
            ->processBuilder
            ->expects($this->once())
            ->method('getProcess')
            ->willReturn($this->process)
        ;
        $this
            ->process
            ->expects($this->once())
            ->method('run')
            ->willReturn(0)
        ;
        $this
            ->process
            ->expects($this->never())
            ->method('getExitCode')
        ;
        $this
            ->process
            ->expects($this->never())
            ->method('getErrorOutput')
        ;

        $this->tester->execute(
            array(
                'command' => $this->app->find('shell:exec'),
                'command-line' => 'echo "Hello world"',
            )
        );

        $this->assertRegExp(
            '/I have successfully executed the given command line/',
            $this->tester->getDisplay()
        );
    }
}
