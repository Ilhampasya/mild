<?php

namespace App\Console;

use Throwable;
use App\Bootstraps\RegisterConfig;
use App\Bootstraps\RegisterFacade;
use App\Bootstraps\RegisterProvider;
use App\Console\Commands\ClosureCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class Kernel
{
    /**
     * The bootstrap index
     *
     * @var int
     */
    protected $queue = 0;
    /**
     * Register path for load route command
     *
     * @var array
     * @see ClosureCommand
     */
    protected $path = [
        'routes/console.php'
    ];
    /**
     * Register bootstrap
     *
     * @var array
     */
    protected $bootstraps = [
        RegisterConfig::class,
        RegisterFacade::class,
        RegisterProvider::class
    ];
    /**
     * Register Command
     *
     * @var array
     */
    protected $commands = [
        Commands\PsyCommand::class,
        Commands\ServeCommand::class,
        Commands\OptimizeCommand::class,
        Commands\MakeRuleCommand::class,
        Commands\ViewClearCommand::class,
        Commands\MakeModelCommand::class,
        Commands\RouteListCommand::class,
        Commands\RouteCacheCommand::class,
        Commands\RouteClearCommand::class,
        Commands\CacheClearCommand::class,
        Commands\CacheForgetCommand::class,
        Commands\MakeConsoleCommand::class,
        Commands\MakeHandlerCommand::class,
        Commands\ConfigCacheCommand::class,
        Commands\ConfigClearCommand::class,
        Commands\MakeProviderCommand::class,
        Commands\MakeBootstrapCommand::class,
        Commands\MakeMiddlewareCommand::class,
        Commands\MakeControllerCommand::class
    ];

    /**
     * Kernel constructor.
     * @param \App\Handlers\Handler $handler
     * @throws Throwable
     */
    public function __construct($handler)
    {
        try {
            $app = $this->bootstrap($handler->getApp());
            $console = new Application('Mild Framework', $app->getVersion());
            $this->loadCommandFromRegisteredPath($app);
            foreach ($this->commands as $command) {
                if (is_string($command)) {
                    $command = $app->instance($command);
                }
                $command->setMild($app);
                $console->add($command);
            }
            $console->run(new ArgvInput, new ConsoleOutput);
        } catch (Throwable $e) {
            $handler->handle($e);
        }
    }

    /**
     * Load command from registered path on path property
     *
     * @param \Mild\App $app
     * @return void
     */
    protected function loadCommandFromRegisteredPath($app)
    {
        $app->set(static::class, $this);
        foreach ($this->path as $path) {
            require $app->getPath($path);
        }
    }

    /**
     * Add command with a closure command to handle.
     * 
     * @param $name
     * @param callable $callback
     * @return ClosureCommand
     */
    public function command($name, $callback)
    {
        $this->commands[] = $command = new ClosureCommand($name, $callback);
        return $command;
    }

    /**
     * Load bootstrap application
     *
     * @param \Mild\App $app
     * @return \Mild\App
     */
    public function bootstrap($app)
    {
        $queue = $this->queue;
        $this->queue++;
        if (!isset($this->bootstraps[$queue])) {
            return $app;
        }
        return (new $this->bootstraps[$queue])->bootstrap($app, [$this, 'bootstrap']);
    }
}