<?php

namespace App\Console\Commands;

class MakeConsoleCommand extends Command
{
    /**
     * @var string
     */
    protected $path = 'app/Console/Commands';
    /**
     * @var string
     */
    protected $namespace = __NAMESPACE__;
    /**
     * @var string
     */
    protected $name = 'make:command';
    /**
     * @var string
     */
    protected $description = 'Create a new console command';
    /**
     * @var array
     */
    protected $arguments = [
        'name' => [
            self::MODE => self::ARG_VALUE_REQUIRED,
            self::DESCRIPTION => 'The name of the command'
        ]
    ];

    /**
     * @return void
     */
    public function handle()
    {
        $name = trim($this->getArgument('name'), '\\/');
        $path = $this->mild->getPath(rtrim($this->path, '/'));
        $file = $path.'/'.$name.'.php';
        $namespace = trim($this->namespace, '\\/');
        $stub = <<<EOF
<?php

namespace $namespace;

class $name extends Command
{
    /**
     * Set command name
     *
     * @var string
     */
    protected \$name;
    /**
     * Set command description
     *
     * @var string
     */
    protected \$description;
    /**
     * Set options with the name in the key array and the parameter in the value of array
     *
     * @var array
     */
    protected \$options = [];
    /**
     * Set arguments with the name in the key and parameter in the value of array
     *
     * @var array
     */
    protected \$arguments = [];
    
    /**
     * @return mixed
     */
    public function handle()
    {
        //
    }
}
EOF;
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        if (is_file($file)) {
            $this->error('Command already exists.');
        } elseif (file_put_contents($file, $stub) === false) {
            $this->error('Command created failed.');
        } else {
            $this->info('Command created successfully.');
        }
    }
}