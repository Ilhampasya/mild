<?php

namespace App\Console\Commands;

class MakeProviderCommand extends Command
{
    /**
     * Set command name
     *
     * @var string
     */
    protected $name = 'make:provider';
    /**
     * Set a path on service provider
     *
     * @var string
     */
    protected $path = 'app/Providers';
    /**
     * Set a namespace on service provider
     *
     * @var string
     */
    protected $namespace = 'App\Providers';
    /**
     * Set command description
     *
     * @var string
     */
    protected $description = 'Create a new service provider class';
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
            self::DESCRIPTION => 'The name of class provider'
        ]
    ];
    
    /**
     * @return mixed
     */
    public function handle()
    {
        $namespace = trim($this->namespace, '\\/');
        $name = trim($this->getArgument('name', '\\/'));
        $path = $this->mild->getPath(rtrim($this->path, '/'));
        $file = $path.'/'.$name.'.php';
        $stub = <<<EOF
<?php

namespace $namespace;

use Mild\Supports\ServiceProvider;

class $name extends ServiceProvider
{
    /**
     * Set true if the provider should be defered and in the provides 
     * Method you must add an key on registered binding in this service provider
     * 
     * @var bool
     */
    protected \$defer = false;

    /**
     * Register binding
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot a provider
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * If the provider make be defered, set your key on registered binding
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
EOF;
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        if (is_file($file)) {
            $this->error('Provider already exists.');
        } elseif (file_put_contents($file, $stub) === false) {
            $this->error('Provider created failed.');
        } else {
            $this->info('Provider created successfully.');
        }

    }
}