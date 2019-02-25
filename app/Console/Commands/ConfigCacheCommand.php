<?php

namespace App\Console\Commands;

class ConfigCacheCommand extends Command
{
    /**
     * Set command name
     *
     * @var string
     */
    protected $name = 'config:cache';
    /**
     * Set command description
     *
     * @var string
     */
    protected $description = 'Create a config cache';
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
     *
     */
    public function handle()
    {
        if (file_put_contents($this->mild->getConfigCachePath(), '<?php return '.collect($this->mild->get('config')->all())->export().';')) {
            $this->info('Config has been cached');
        } else {
            $this->error('couldnt create cache config');
        }
    }
}