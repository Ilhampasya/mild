<?php

namespace App\Console\Commands;

class OptimizeCommand extends Command
{
    /**
     * Set command name
     *
     * @var string
     */
    protected $name = 'optimize';
    /**
     * Set command description
     *
     * @var string
     */
    protected $description = 'Optimize the application with a cache';
    /**
     * Set options with the name in the key array and the parameter in the value of array
     *
     * @var array
     */
    protected $options = [];
    /**
     * Set arguments with the name in the key and parameter in the value of array
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * @throws \Exception
     */
    public function handle()
    {
        $this->call('config:cache');
        $this->call('route:cache');
        $this->info('Files cached successfully!');
    }
}