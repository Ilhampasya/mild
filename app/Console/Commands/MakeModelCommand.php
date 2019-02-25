<?php

namespace App\Console\Commands;

class MakeModelCommand extends Command
{
    /**
     * @var string
     */
    protected $path = 'app/Models';
    /**
     * @var string
     */
    protected $namespace = 'App\Models';
    /**
     * Set command name
     *
     * @var string
     */
    protected $name = 'make:model';
    /**
     * Set command description
     *
     * @var string
     */
    protected $description = 'Create a new model class';
    /**
     * Set options with the name in the key array and the parameter in the value of array
     *
     * @var array
     */
    protected $options = [
        'controller' => [
            self::SHORTCUT => 'c',
            self::MODE => self::OPT_VALUE_NONE,
            self::DESCRIPTION => 'Create a new controller for the model'
        ],
        'table' => [
            self::SHORTCUT => 't',
            self::MODE => self::OPT_VALUE_REQUIRED,
            self::DESCRIPTION => 'Set a table name'
        ]
    ];
    /**
     * Set arguments with the name in the key and parameter in the value of array
     *
     * @var array
     */
    protected $arguments = [
        'name' => [
            self::MODE => self::ARG_VALUE_REQUIRED,
            self::DESCRIPTION => 'The name of model class.'
        ]
    ];

    /**
     * @throws \Exception
     */
    public function handle()
    {
        $namespace = trim($this->namespace, '\\/');
        $name = trim($this->getArgument('name'), '\\/');
        $path = $this->mild->getPath(rtrim($this->path, '/'));
        $file = $path .'/' .$name .'.php';
        $table = $this->getOption('table');
        if (empty($table)) {
            $table = strtolower($name);
            if ($table[-1] !== 's') {
                $table .= 's';
            }
        }
        $stub = <<<EOF
<?php

namespace $namespace;

use Mild\Database\Model;

class $name extends Model
{
    /**
     * Set table name on the database
     *
     * @var string
     */
    protected \$table = '$table';
    /**
     * Set Primary key, it will work on relation 
     *
     * @var string
     */
    protected \$primaryKey = 'id';
    /**
     * Set for pagination num, it will work on pagination
     *
     * @var string
     */
    protected \$perPage = 15;
    /**
     * Set date format, it will if the attribute use a Carbon instance
     *
     * @var string
     */
    protected \$dateFormat = 'Y-m-d H:i:s';
    
    
}
EOF;
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        if (is_file($file)) {
            $this->output->writeln('<error>Model already exists.</error>');
        } elseif (file_put_contents($file, $stub) === false) {
            $this->output->writeln('<error>Model created failed.</error>');
        } else {
            $this->output->writeln('<info>Model created successfully.</info>');
        }
        if ($this->getOption('controller')) {
            $this->call('make:controller', [
                'name' => $name.'Controller',
                ''
            ]);
        }
    }
}