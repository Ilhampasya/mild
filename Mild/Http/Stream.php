<?php
/**
 * Mild Framework component
 *
 * @author Mochammad Riyadh Ilham Akbar Pasya
 * @link https://github.com/mildphp/mild
 * @copyright 2018
 * @license https://github.com/mildphp/mild/blob/master/LICENSE (MIT Licence)
 */
namespace Mild\Http;

use Exception;
use RuntimeException;
use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;

class Stream implements StreamInterface
{
    /**
     * @var int
     */
    protected $size;
    /**
     * @var resource
     */
    protected $resource;
    /**
     * @var mixed
     */
    protected $seekable;
    /**
     * @var bool
     */
    protected $readable;
    /**
     * @var bool
     */
    protected $writable;
    /**
     * @var array
     */
    protected $metaData = [];
    /**
     * @var array
     */
    protected static $modes = [
        'readable' => [
            'r' => true, 'w+' => true, 'r+' => true, 'x+' => true, 'c+' => true,
            'rb' => true, 'w+b' => true, 'r+b' => true, 'x+b' => true,
            'c+b' => true, 'rt' => true, 'w+t' => true, 'r+t' => true,
            'x+t' => true, 'c+t' => true, 'a+' => true, 'rb+' => true,
        ],
        'writable' => [
            'w' => true, 'w+' => true, 'rw' => true, 'r+' => true, 'x+' => true,
            'c+' => true, 'wb' => true, 'w+b' => true, 'r+b' => true, 'rb+' => true,
            'x+b' => true, 'c+b' => true, 'w+t' => true, 'r+t' => true,
            'x+t' => true, 'c+t' => true, 'a' => true, 'a+' => true
        ]
    ];

    /**
     * Stream constructor.
     * @param resource $resource
     */
    public function __construct($resource)
    {
        if (is_resource($resource) === false) {
            throw new InvalidArgumentException('The type stream must be an resource');
        }
        $this->resource = $resource;
        $this->metaData = stream_get_meta_data($resource);
        $this->seekable = $this->metaData['seekable'];
        $this->readable = isset(static::$modes['readable'][$this->metaData['mode']]);
        $this->writable = isset(static::$modes['writable'][$this->metaData['mode']]);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        try {
            $contents = $this->getContents();
            $this->seek(0);
            return $contents;
        } catch (Exception $e) {
            return '';
        }
    }

    /**
     * @return void
     */
    public function close()
    {
        if (!isset($this->resource)) {
            return;
        }
        fclose($this->resource);
        $this->detach();
    }

    /**
     * @return null|resource
     */
    public function detach()
    {
        if (!isset($this->resource)) {
            return null;
        }
        $resource = $this->resource;
        unset($this->resource);
        $this->size = null;
        $this->metaData = [];
        $this->seekable = false;
        $this->readable = false;
        $this->writable = false;
        return $resource;
    }

    /**
     * @return int|null
     */
    public function getSize()
    {
        if ($this->size === null && isset($this->resource)) {
            $stat = fstat($this->resource);
            if (isset($stat['size'])) {
                $this->size = $stat['size'];
            }
        }
        return $this->size;
    }

    /**
     * @return bool|int
     */
    public function tell()
    {
        if (!isset($this->resource)) {
            throw new RuntimeException('Stream has been detach.');
        }
        $tell = ftell($this->resource);
        if ($tell === false) {
            throw new RuntimeException('Uncaught position a stream.');
        }
        return $tell;
    }

    /**
     * @return bool
     */
    public function eof()
    {
        if (!isset($this->resource)) {
            throw new RuntimeException('Stream has been detach.');
        }
        return feof($this->resource);
    }

    /**
     * @return bool|mixed
     */
    public function isSeekable()
    {
        return $this->seekable;
    }

    /**
     * @param int $offset
     * @param int $whence
     * @return void
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        if (!isset($this->resource)) {
            throw new RuntimeException('Stream has been detach.');
        }
        if (!$this->seekable || fseek($this->resource, $offset, $whence) === false) {
            throw new RuntimeException('Could not seek in stream');
        }
    }

    /**
     * @return void
     */
    public function rewind()
    {
        $this->seek(0);
    }

    /**
     * Returns whether or not the stream is writable.
     *
     * @return bool
     */
    public function isWritable()
    {
        return $this->writable;
    }

    /**
     * @param string $string
     * @return bool|int
     */
    public function write($string)
    {
        if (!isset($this->resource)) {
            throw new RuntimeException('Stream has been detach.');
        } elseif (!$this->writable || ($write = fwrite($this->resource, $string)) === false) {
            throw new RuntimeException('Could not write to stream');
        }
        $this->size = null;
        return $write;
    }

    /**
     * @return bool
     */
    public function isReadable()
    {
        return $this->readable;
    }

    /**
     * @param int $length
     * @return string
     */
    public function read($length)
    {
        if (!isset($this->resource)) {
            throw new RuntimeException('Stream has been detach.');
        }
        if (!$this->readable || ($data = fread($this->resource, $length)) === false) {
            throw new RuntimeException('Could not read from stream');
        }
        return $data;
    }

    /**
     * @return bool|string
     */
    public function getContents()
    {
        if (!isset($this->resource)) {
            throw new RuntimeException('Stream has been detach.');
        }
        if (($contents = stream_get_contents($this->resource)) === false) {
            throw new RuntimeException('Uncaught get a stream');
        }
        return $contents;
    }

    /**
     * @param null $key
     * @return array|mixed|null
     */
    public function getMetadata($key = null)
    {
        if (isset($this->metaData[$key])) {
            return $this->metaData[$key];
        }
        return $this->metaData;
    }

    /**
     * @return resource
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @return void
     */
    public function __destruct()
    {
        $this->close();
    }
}