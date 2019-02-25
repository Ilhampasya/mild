<?php

namespace App\Console\Commands;

use Psy\Shell;
use Psy\Configuration;

class PsyCommand extends Command
{
    /**
     * Set command name
     *
     * @var string
     */
    protected $name = 'psy';
    /**
     * Set command description
     *
     * @var string
     */
    protected $description = 'Interact with console application';
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
        'include' => [
            self::MODE => self::ARG_VALUE_IS_ARRAY,
            self::DESCRIPTION => 'Include file(s) before starting psy'
        ]
    ];
    
    /**
     * @return mixed
     */
    public function handle()
    {
        $shell = new Shell(new Configuration(['updateCheck' => 'never']));
        $shell->setIncludes($this->getArgument('include'));
        $shell->run();
    }
}