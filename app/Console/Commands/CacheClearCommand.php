<?php

namespace App\Console\Commands;

class CacheClearCommand extends Command
{
    /**
     * Set command name
     *
     * @var string
     */
    protected $name = 'cache:clear';
    /**
     * Set command description
     *
     * @var string
     */
    protected $description = 'Flush a cache in the application';
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
     * @throws \ReflectionException
     */
    public function handle()
    {
        $this->mild->get('cache')->flush();
        $this->info('Application cache cleared.');
    }
}