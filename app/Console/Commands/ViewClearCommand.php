<?php

namespace App\Console\Commands;

class ViewClearCommand extends Command
{
    /**
     * Set command name
     *
     * @var string
     */
    protected $name = 'view:clear';
    /**
     * Set command description
     *
     * @var string
     */
    protected $description = 'Clear all compiled view files';
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
        foreach (glob($this->mild->get('view')->getCompiledPath().'/*') as $file) {
            @unlink($file);
        }
        $this->info('Compiled views cleared.');
    }
}