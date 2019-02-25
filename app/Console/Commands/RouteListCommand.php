<?php

namespace App\Console\Commands;

class RouteListCommand extends Command
{
    /**
     * Set command name
     *
     * @var string
     */
    protected $name = 'route:list';
    /**
     * Set command description
     *
     * @var string
     */
    protected $description = 'Get list route on the application';
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
     * @throws \ReflectionException
     */
    public function handle()
    {
        $lists = [];
        foreach ($this->mild->get('router')->getRouteStack() as $route) {
            $callback = $route->getAction('callback');
            if (is_object($callback)) {
                $callback = get_class($callback);
            }
            if (is_array($callback)) {
                $callback = implode('@', $callback);
            }
            $lists[] = [$route->getUrl(), implode('|', $route->getMethods()), $callback, $route->getAction('name'), collect($route->getAction('middleware'))->map(function ($middleware) {
                if (is_callable($middleware)) {
                    if (is_object($middleware)) {
                        $middleware = get_class($middleware);
                    }
                    if (is_array($middleware)) {
                        $middleware = implode('@', $middleware);
                    }
                }
                return $middleware;
            })->implode(',')];
        }
        return $this->table(['Pattern', 'Method', 'Callback', 'Name', 'Middleware'], $lists);
    }
}