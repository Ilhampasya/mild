<?php

namespace App\Console\Commands;

class CacheForgetCommand extends Command
{
    /**
     * Set command name
     *
     * @var string
     */
    protected $name = 'cache:forget';
    /**
     * Set command description
     *
     * @var string
     */
    protected $description = 'Remove cache by name in the application';
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
    protected $arguments = [
        'name' => [
            self::MODE => self::ARG_VALUE_REQUIRED,
            self::DESCRIPTION => 'The name of the cache'
        ]
    ];

    /**
     * @throws \ReflectionException
     */
    public function handle()
    {
        $cache = $this->mild->get('cache');
        $name = $this->getArgument('name');
        if ($cache->has($name)) {
            $cache->put($name);
            $this->info('Cache ['.$name.'] has been removed.');  
        } else {
            $this->error('Cache ['.$name.'] does not exists.');
        }
    }
}