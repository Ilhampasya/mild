<?php

namespace App\Console\Commands;

class MakeBootstrapCommand extends Command
{
    /**
     * Set command name
     *
     * @var string
     */
    protected $name = 'make:bootstrap';
    /**
     * @var string
     */
    protected $path = 'app/Bootstraps';
    /**
     * @var string
     */
    protected $namespace = 'App\Bootstrap';
    /**
     * Set command description
     *
     * @var string
     */
    protected $description = 'Create new bootstrap class';
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
            self::DESCRIPTION => 'The name of bootstrap class'
        ]
    ];
    
    /**
     * @return mixed
     */
    public function handle()
    {
        $namespace = trim($this->namespace,  '\\/');
        $name = trim($this->getArgument('name'), '\\/');
        $path = $this->mild->getPath(rtrim($this->path, '/'));
        $file = $path.'/'.$name.'.php';
        $stub = <<<EOF
<?php

namespace $namespace;

class $name
{
    /**
     * @param \Mild\App \$app
     * @param callable \$next
     * @return void
     */ 
    public function bootstrap(\$app, \$next)
    {
        return \$next(\$app);
    }
}
EOF;

        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        if (is_file($file)) {
            $this->error('Bootstrap already exists.');
        } elseif (file_put_contents($file, $stub) === false) {
            $this->error('Bootstrap created failed.');
        } else {
            $this->info('Bootstrap created successfully.');
        }
    }
}