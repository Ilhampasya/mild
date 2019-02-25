<?php

namespace App\Console\Commands;

class RouteClearCommand extends Command
{
    /**
     * Set command name
     *
     * @var string
     */
    protected $name = 'route:clear';
    /**
     * Set command description
     *
     * @var string
     */
    protected $description = 'Clear route cached';
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
     * @return mixed
     */
    public function handle()
    {
        @unlink($this->mild->getRouteCachePath());
        $this->info('Route cache cleared!');
    }
}