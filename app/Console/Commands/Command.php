<?php

namespace App\Console\Commands;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

class Command extends SymfonyCommand
{
    /**
     * The shortcut key usage just on options key property
     */
    const SHORTCUT = 0;
    /**
     * The mode key usage on arguments and options property
     */
    const MODE = 1;
    /**
     * The description key usage on arguments and options property
     */
    const DESCRIPTION = 2;
    /**
     * The default key usage on arguments and options property
     */
    const DEFAULT = 3;
    /**
     * Set in mode on arguments property if the value must be optional value
     */
    const ARG_VALUE_OPTIONAL = InputArgument::OPTIONAL;
    /**
     * Set in mode on arguments property if the value must be array value
     */
    const ARG_VALUE_IS_ARRAY = InputArgument::IS_ARRAY;
    /**
     *  Set in mode on arguments property if the value must be required value
     */
    const ARG_VALUE_REQUIRED = InputArgument::REQUIRED;
    /**
     *  Set in mode on options property if the value must be empty value
     */
    const OPT_VALUE_NONE = InputOption::VALUE_NONE;
    /**
     * Set in mode on options property if the value must be optional value
     */
    const OPT_VALUE_OPTIONAL = InputOption::VALUE_OPTIONAL;
    /**
     * Set in mode on options property if the value must be array value
     */
    const OPT_VALUE_IS_ARRAY = InputOption::VALUE_IS_ARRAY;
    /**
     *  Set in mode on options property if the value must be required value
     */
    const OPT_VALUE_REQUIRED = InputOption::VALUE_REQUIRED;

    /**
     * Set command name
     *
     * @var string
     */
    protected $name;
    /**
     * @var \Mild\App
     */
    protected $mild;
    /**
     * @var InputInterface
     */
    protected $input;
    /**
     * @var SymfonyStyle $output
     */
    protected $output;
    /**
     * Set command description
     *
     * @var string
     */
    protected $description;
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
     * Command constructor.
     */
    public function __construct()
    {
        parent::__construct($this->name);
        $this->setDescription($this->description);
        $this->configureArguments();
        $this->configureOptions();
    }

    /**
     * @param \Mild\App $mild
     */
    public function setMild($mild)
    {
        $this->mild = $mild;
    }

    /**
     * @return \Mild\App
     */
    public function getMild()
    {
        return $this->mild;
    }

    /**
     * Configure an options
     *
     * @return void
     */
    protected function configureOptions()
    {
        $shortcut = null;
        $mode = null;
        $description = '';
        $default = null;
        foreach ($this->options as $key => $value) {
            if (isset($value[self::SHORTCUT])) {
                $shortcut = $value[self::SHORTCUT];
            }
            if (isset($value[self::MODE])) {
                $mode = $value[self::MODE];
            }
            if (isset($value[self::DESCRIPTION])) {
                $description = $value[self::DESCRIPTION];
            }
            if (isset($value[self::DEFAULT])) {
                $default = $value[self::DEFAULT];
            }
            $this->addOption($key, $shortcut, $mode, $description, $default);
        }
    }

    /**
     * Configure an arguments
     *
     * @return void
     */
    protected function configureArguments()
    {
        $mode = null;
        $description = '';
        $default = null;
        foreach ($this->arguments as $key => $value) {
            if (isset($value[self::MODE])) {
                $mode = $value[self::MODE];
            }
            if (isset($value[self::DESCRIPTION])) {
                $description = $value[self::DESCRIPTION];
            }
            if (isset($value[self::DEFAULT])) {
                $default = $value[self::DEFAULT];
            }
            $this->addArgument($key, $mode, $description, $default);
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Exception
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        $this->output = new SymfonyStyle($this->input = $input, $output);
        return parent::run($this->input, $this->output);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|mixed|null
     * @throws \ReflectionException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return $this->mild->call([$this, 'handle']);
    }

    /**
     * @param $command
     * @param array $arguments
     * @return int
     * @throws \Exception
     */
    public function call($command, array $arguments = [])
    {
        $arguments = array_merge(array_filter($this->input->getOptions(), [$this, 'filterOptions']), $arguments);
        return $this->getApplication()->find($command)->run(new ArrayInput($arguments), $this->output);
    }

    /**
     * @param $option
     * @return bool
     */
    public function filterOptions($option)
    {
        return in_array($option, ['ansi', 'no-ansi', 'no-interaction', 'quiet', 'verbose'], true);
    }

    /**
     * @param $question
     * @param null $default
     * @return mixed
     */
    public function ask($question, $default = null)
    {
        return $this->output->ask($question, $default);
    }

    /**
     * @param $question
     * @param array $choices
     * @param null $default
     * @return mixed
     */
    public function askWithCompletion($question, array $choices, $default = null)
    {
        $question = new Question($question, $default);
        $question->setAutocompleterValues($choices);
        return $this->output->askQuestion($question);
    }

    /**
     * @param $question
     * @param bool $fallback
     * @return mixed
     */
    public function askWithSecret($question, $fallback = true)
    {
        $question = new Question($question);
        $question->setHidden(true)->setHiddenFallback($fallback);
        return $this->output->askQuestion($question);
    }

    /**
     * @param $question
     * @param array $choices
     * @param null $default
     * @param null $attempts
     * @param null $multiple
     * @return mixed
     */
    public function askWithChoice($question, array $choices, $default = null, $attempts = null, $multiple = null)
    {
        $question = new ChoiceQuestion($question, $choices, $default);
        $question->setMaxAttempts($attempts)->setMultiselect($multiple);
        return $this->output->askQuestion($question);
    }

    /**
     * @param array $headers
     * @param array $rows
     * @param string $tableStyle
     * @param array $columnStyles
     * @return void
     */
    public function table(array $headers, array $rows, $tableStyle = 'default', array $columnStyles = [])
    {
        $table = new Table($this->output);
        $table->setHeaders($headers)->setRows($rows)->setStyle($tableStyle);
        foreach ($columnStyles as $key => $value) {
            $table->setColumnStyle($key, $value);
        }
        return $table->render();
    }

    /**
     * @return \Symfony\Component\Console\Input\InputInterface
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @return \Symfony\Component\Console\Style\SymfonyStyle
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * @param $message
     * @param int $type
     * @return void
     */
    public function comment($message, $type = 0)
    {
        return $this->write($message, $type, 'comment');
    }

    /**
     * @param $message
     * @param int $type
     * @return void
     */
    public function info($message, $type = 0)
    {
        return $this->write($message, $type, 'info');
    }

    /**
     * @param $message
     * @param int $type
     * @return void
     */
    public function warning($message, $type = 0)
    {
        $formatter = $this->output->getFormatter();
        if (!$formatter->hasStyle('warning')) {
            $style = new OutputFormatterStyle('yellow');
            $formatter->setStyle('warning', $style);
        }
        return $this->write($message, $type, 'warning');
    }

    /**
     * @param $message
     * @param int $type
     * @return void
     */
    public function error($message, $type = 0)
    {
        return $this->write($message, $type, 'error');
    }

    /**
     * @param $message
     * @param int $type
     * @param string $style
     * @return void
     */
    public function write($message, $type = 0, $style = '')
    {
        if (!empty($style)) {
            $message = '<'.$style.'>'.$message.'</'.$style.'>';
        }
        return $this->output->writeln($message, $type);
    }

    /**
     * @param null $key
     * @return array|string|string[]|null
     */
    public function getArgument($key = null)
    {
        if (is_null($key)) {
            return $this->input->getArguments();
        }
        return $this->input->getArgument($key);
    }

    /**
     * @param $key
     * @return bool
     */
    public function hasArgument($key)
    {
        return $this->input->hasArgument($key);
    }

    /**
     * @param null $key
     * @return array|bool|string|string[]|null
     */
    public function getOption($key = null)
    {
        if (is_null($key)) {
            return $this->input->getOptions();
        }
        return $this->input->getOption($key);
    }

    /**
     * @param $key
     * @return bool
     */
    public function hasOption($key)
    {
        return $this->input->hasOption($key);
    }
}