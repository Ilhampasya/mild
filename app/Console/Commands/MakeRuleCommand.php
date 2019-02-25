<?php

namespace App\Console\Commands;

class MakeRuleCommand extends Command
{
    /**
     * @var string
     */
    protected $path = 'app/Rules';
    /**
     * @var string
     */
    protected $namespace = 'App\Rules';
    /**
     * @var string
     */
    protected $name = 'make:rule';
    /**
     * @var string
     */
    protected $description = 'Create a new validation rule class';
    /**
     * @var array
     */
    protected $arguments = [
        'name' => [
            self::MODE => self::ARG_VALUE_REQUIRED,
            self::DESCRIPTION => 'The name of class rule'
        ]
    ];
    /**
     * @var array
     */
    protected $options = [];
    
    /**
     * @return mixed
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

use Mild\Validation\Rules\Rule;

class $name extends Rule
{
    /**
     * The default options on rule
     * Set true on param if the rule should with a parameter
     * Set num count of parameter in count key, if the num of parameter more than 1, just set 1
     * 
     * @var array
     */
    protected \$options = [
        'param' => false,
        'count' => 0
    ];

    /**
     * @param array \$datas
     * @param \$attribute
     * @param \$value
     * @return bool
     */
    public function passes(array \$datas, \$attribute, \$value)
    {
        //
    }

    /**
     * @return string
     */
    public function message()
    {
        //
    }
}
EOF;
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        if (is_file($file)) {
            $this->error('Rule already exists.');
        } elseif (file_put_contents($file, $stub) === false) {
            $this->error('Rule created failed.');
        } else {
            $this->info('Rule created successfully');
        }
    }
}