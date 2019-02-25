<?php

namespace App\Console\Commands;

class MakeControllerCommand extends Command
{
    /**
     * @var string
     */
    protected $name = 'make:controller';
    /**
     * @var string
     */
    protected $path = 'app/Http/Controllers';
    /**
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';
    /**
     * @var string
     */
    protected $description = 'Create a new controller class';
    /**
     * @var array
     */
    protected $arguments = [
        'name' => [
            self::MODE => self::ARG_VALUE_REQUIRED,
            self::DESCRIPTION => 'The name of the class'
        ]
    ];

    /**
     * @return void
     */
    public function handle()
    {
        $namespace = trim($this->namespace, '\\/');
        $name = trim($this->getArgument('name'), '\\/');
        $path = $this->mild->getPath(rtrim($this->path, '/'));
        $file = $path .'/' .$name .'.php';
        $stub = <<<EOF
<?php

namespace $namespace;

use Mild\Http\Request;
use Mild\Http\Response;

class $name
{
    //
}
EOF;
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        if (is_file($file)) {
            $this->output->writeln('<error>Controller already exists.</error>');
        } elseif (file_put_contents($file, $stub) === false) {
            $this->output->writeln('<error>Controller created failed.</error>');
        } else {
            $this->output->writeln('<info>Controller created successfully.</info>');
        }
    }
}