<?php

namespace App\Console\Commands;

class MakeMiddlewareCommand extends Command
{
    /**
     * Set command name
     *
     * @var string
     */
    protected $name = 'make:middleware';
    /**
     * Set path of middleware
     * 
     * @var string
     */
    protected $path = 'app/Http/Middleware';
    /**
     * Set namespace of middleware
     * 
     * @var string
     */
    protected $namespace = 'App\Http\Middleware';
    /**
     * Set command description
     *
     * @var string
     */
    protected $description = 'Create a new middleware class';
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
            self::DESCRIPTION => 'The name of middleware class'
        ]
    ];
    
    /**
     * @return mixed
     */
    public function handle()
    {
        $name = trim($this->getArgument('name'), '\\/');
        $namespace = trim($this->namespace, '\\/');
        $path = $this->mild->getPath(rtrim($this->path, '/'));
        $file = $path.'/'.$name.'.php';
        $stub = <<<EOF
<?php

namespace $namespace;

class $name
{
    /**
     * @param \Psr\Http\Message\ServerRequestInterface \$request
     * @param \Psr\Http\Message\ResponseInterface \$response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(\$request, \$response, \$next)
    {
        return \$next(\$request, \$response);
    }
}
EOF;
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        } 
        if (is_file($file)) {
            $this->error('Middleware already exists.');
        } elseif (file_put_contents($file, $stub) === false) {
            $this->error('Middleware created failed.');
        } else {
            $this->info('Middleware created successfully.');
        }        
    } 
}