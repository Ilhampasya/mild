<?php

namespace App\Console\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClosureCommand extends Command
{
    /**
     * @var string
     */
    protected $name;
    /**
     * @var callable
     */
    protected $callback;

    /**
     * ClosureCommand constructor.
     * @param $name
     * @param callable $callback
     */
    public function __construct($name, callable $callback)
    {
        $this->name = $name;
        $this->callback = $callback;
        parent::__construct();
    }

    /**
     * @param string $description
     * @return Command
     */
    public function describe($description)
    {
        return $this->setDescription($description);
    }

    /**
     * @param mixed $name
     * @param null $mode
     * @param string $description
     * @param null $default
     * @return Command
     */
    public function argument($name, $mode = null, $description = '', $default = null)
    {
        return $this->addArgument($name, $mode, $description, $default);
    }

    /**
     * @param mixed $name
     * @param null $shortcut 
     * @param null $mode
     * @param string $description
     * @param null $default
     * @return Command
     */
    public function option($name, $shortcut = null, $mode = null, $description = '', $default = null)
    {
        return $this->addOption($name, $shortcut = null, $mode = null, $description = '', $default = null);
    }

    /**
     * @param Symfony\Component\Console\Input\InputInterface $input
     * @param Symfony\Component\Console\Output\OutputInterface $output
     * @return mixed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return $this->mild->call($this->callback, array_filter(array_values(array_merge([$this], array_slice($input->getArguments(), 1), $input->getOptions()))));
    }
}