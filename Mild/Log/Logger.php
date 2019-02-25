<?php
/**
 * Mild Framework component
 *
 * @author Mochammad Riyadh Ilham Akbar Pasya
 * @link https://github.com/mildphp/mild
 * @copyright 2018
 * @license https://github.com/mildphp/mild/blob/master/LICENSE (MIT Licence)
 */
namespace Mild\Log;

use Throwable;
use Psr\Log\LoggerInterface;
use InvalidArgumentException;

class Logger implements LoggerInterface
{
    /**
     * @var string
     */
    protected $channel;
    /**
     * @var \Psr\Http\Message\StreamInterface
     */
    protected $stream;
    /**
     * @var int
     */
    protected $minLevel = 0;
    /**
     * @var array
     */
    protected static $levels = [
        'debug' => 0,
        'info' => 1,
        'notice' => 2,
        'warning' => 3,
        'error' => 4,
        'critical' => 5,
        'alert' => 6,
        'emergency' => 7,
    ];

    /**
     * Logger constructor.
     * @param \Psr\Http\Message\StreamInterface $stream
     * @param string $channel
     * @param string $minLevel
     */
    public function __construct($stream, $channel = 'local', $minLevel = 'debug')
    {
        $this->stream = $stream;
        $this->channel = $channel;
        if (!is_null($minLevel)) {
            if (!isset(static::$levels[$minLevel])) {
                throw new InvalidArgumentException('Invalid minimum level ['.$minLevel.'] to write a log.');
            }
            $this->minLevel = static::$levels[$minLevel];
        }
    }
    
    /**
     * System is unusable.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function emergency($message, array $context = array())
    {
        $this->log('emergency', $message, $context);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function alert($message, array $context = array())
    {
        $this->log('alert', $message, $context);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function critical($message, array $context = array())
    {
        $this->log('critical', $message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function error($message, array $context = array())
    {
        $this->log('error', $message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function warning($message, array $context = array())
    {
        $this->log('warning', $message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function notice($message, array $context = array())
    {
        $this->log('notice', $message, $context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function info($message, array $context = array())
    {
        $this->log('info', $message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function debug($message, array $context = array())
    {
        $this->log('debug', $message, $context);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function log($level, $message, array $context = array())
    {
        if (!isset(static::$levels[$level])) {
            throw new InvalidArgumentException('Invalid level ['.$level.'] to write a log.');
        }
        if (static::$levels[$level] < $this->minLevel) {
            return;
        }
        foreach ($context as $key => $value) {
            if (is_null($value) || is_scalar($value)) {
                $context[$key] = $value;
            } elseif (is_object($value)) {
                if ($value instanceof Throwable) {
                    $context[$key] = '[object] ('.get_class($value).'(code: '.$value->getCode().')) '.$value->getMessage().' at '.$value->getFile().':'.$value->getLine().''."\n".'[stacktrace]'."\n".''.$value->getTraceAsString().'';
                } elseif (method_exists($value, '__toString')) {
                    $context[$key] = '[object] ('.get_class($value).') '.$value->__toString().' ';
                }else {
                    $context[$key] = '[object] ('.get_class($value).': '.$this->toJson($value).')';
                }
            } elseif (is_resource($value)) {
                $context[$key] = '[resource] ('.get_resource_type($value).')';
            } else {
                $context[$key] = '[unknown] ('.getType($value).')';
            }
        }
        $contextStr = '';
        if (!empty($context)) {
            $contextStr = str_replace(['\r', '\n'], ["\r", "\n"], $this->toJson($context));
        }
        $this->stream->write('['.date('Y-m-d h:i:s').'] '.$this->channel.'.'.strtoupper($level).': '.$message.' '.$contextStr.''."\n".'');
    }

    /**
     * @param $value
     * @return string
     */
    protected function toJson($value)
    {
        return json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * @return \Psr\Http\Message\StreamInterface
     */
    public function getStream()
    {
        return $this->stream;
    }

    /**
     * @return int
     */
    public function getMinLevel()
    {
        return $this->minLevel;
    }
}