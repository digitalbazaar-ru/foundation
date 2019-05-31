<?php

namespace Foundation\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

class CommandBus
{
    protected $command;
    protected $app;

    public function __construct(Command $command)
    {
        $this->command = $command;
        $this->app = new Application('consoleBusApp');
    }

    /**
     * @param array $arguments
     * @param array $options
     * @param OutputInterface $output
     * @return string
     */
    public function execute($arguments = [], $options = [], OutputInterface $output = null)
    {
        $input = $this->initInput($arguments, $options);

        if (is_null($output)) {
            $output = new BufferedOutput(OutputInterface::VERBOSITY_NORMAL, true);
        }

        $this->app->add($this->command)->run($input, $output);

        return $output->fetch();
    }

    /**
     * @return \Symfony\Component\Console\Input\InputOption[]
     */
    public function getOptionsList()
    {
        return $this->command->getDefinition()->getOptions();
    }

    /**
     * @return \Symfony\Component\Console\Input\InputArgument[]
     */
    public function getArgumentsList()
    {
        return $this->command->getDefinition()->getArguments();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->command->getName();
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->command->getDescription();
    }

    /**
     * @param $arguments
     * @param $options
     * @return ArrayInput
     */
    protected function initInput($arguments, $options)
    {
        $params = array_merge($arguments, [
            'command' => $this->getName(),
        ]);

        foreach ($options as $key => $option) {
            $params['--' . $key] = $option;
        }

        return new ArrayInput($params);
    }
}

