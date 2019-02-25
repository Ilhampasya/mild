<?php

namespace App\Console\Commands;

class RouteCacheCommand extends Command
{
    /**
     * Set command name
     *
     * @var string
     */
    protected $name = 'route:cache';
    /**
     * Set command description
     *
     * @var string
     */
    protected $description = 'create route cache.';
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
        if (file_put_contents($this->mild->getRouteCachePath(), '<?php return '.collect($this->mild->get('router')->getRouteStack())->export().';')) {
            $this->info('Route has been cached');
        } else {
            $this->error('couldnt create cache Route');
        }
    }
}