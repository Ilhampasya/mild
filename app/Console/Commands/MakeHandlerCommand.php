<?php

namespace App\Console\Commands;

class MakeHandlerCommand extends Command
{
    /**
     * Set command name
     *
     * @var string
     */
    protected $name = 'make:handler';
        /**
     * @var string
     */
    protected $path = 'app/Handlers';
    /**
     * @var string
     */
    protected $namespace  = 'App\Handlers';
    /**
     * Set command description
     *
     * @var string
     */
    protected $description = 'Create handler class to handler exception';
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
            self::DESCRIPTION => 'The name of handler class'
        ]
    ];
    
    /**
     * @return mixed
     */
    public function handle()
    {
        $namespace = trim($this->namespace, '\\/');
        $name = trim($this->getArgument('name'), '\\/');
        $path = $this->mild->getPath(rtrim($this->path, '/'));
        $file = $path.'/'.$name.'.php';
        $stub = <<<EOF
<?php

namespace $namespace;

class $name
{
    /**
     * @param \Throwable \$e
     * @param \Mild\App \$app
     * @return \Psr\Http\Message\ResponseInterface
     */ 
    public function handle(\$e, \$app)
    {
        //
    }
}
EOF;

        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        if (is_file($file)) {
            $this->error('Handler already exists.');
        } elseif(file_put_contents($file, $stub) === false) {
            $this->error('Handler created failed.');
        } else {
            $this->info('Handler created successfully.');
        }
    }
}