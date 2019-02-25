<?php

namespace App\Handlers;

use Whoops\Run;
use ErrorException;
use Mild\Routing\RouterException;
use Whoops\Handler\PrettyPageHandler;
use Mild\Validation\ValidationException;
use NunoMaduro\Collision\Handler as ConsoleHandler;

class Handler
{
    /**
     * @var \Mild\App
     */
    protected $app;
    /**
     * @var array
     */
    protected $handlers = [
        RouterException::class => RouterHandler::class,
        ValidationException::class => ValidationHandler::class
    ];

    /**
     * @param \Mild\App $app
     * Handler constructor.
     */
    public function __construct($app)
    {
        $this->app = $app;
        error_reporting(-1);
        set_error_handler([$this, 'error']);
        set_exception_handler([$this, 'exception']);
    }

    /**
     * @return \Mild\App
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * @param \Throwable $e
     * @return mixed
     * @throws \Throwable
     */
    public function handle($e)
    {
        if (isset($this->handlers[$class = get_class($e)]) === true) {
            return (new $this->handlers[$class])->handle($e, $this->app);
        }
        $this->app->get('logger')->error($e->getMessage(), [$class => $e]);
        throw $e;
    }

    /**
     * @param $level
     * @param $message
     * @param string $file
     * @param int $line
     * @param array $context
     * @return void
     */
    public function error($level, $message, $file = '', $line = 0, $context = [])
    {
        if (error_reporting() & $level) {
            $this->errorException($message, 0, $level, $file, $line);
        }
    }

    /**
     * @param $e
     * @return string
     */
    public function exception($e)
    {
        $whoops = new Run;
        if ($this->app->runningInConsole()) {
            $whoops->pushHandler(new ConsoleHandler);
        } else {
            $whoops->pushHandler(new PrettyPageHandler);
        }
        return $whoops->handleException($e);
    }

    /**
     * @param $message
     * @param $code
     * @param $severity
     * @param $file
     * @param $line
     * @param null $previous
     * @return string
     */
    protected function errorException($message, $code, $severity, $file, $line, $previous = null)
    {
        return $this->exception(new ErrorException($message, $code, $severity, $file, $line, $previous));
    }
}