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

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\MessageInterface;

abstract class Message implements MessageInterface
{
    /**
     * @var StreamInterface
     */
    protected $body;
    /**
     * @var array
     */
    protected $headers = [];
    /**
     * @var array
     */
    private $headerNames = [];
    /**
     * @var string
     */
    protected $protocolVersion = '1.1';

    /**
     * @return array|string
     */
    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    /**
     * @param string $version
     * @return Message|MessageInterface
     */
    public function withProtocolVersion($version)
    {
        $message = clone $this;
        $message->protocolVersion = $version;
        return $message;
    }

    /**
     * @param array $headers
     * @return void
     */
    public function setHeaders(array $headers = [])
    {
        foreach ($headers as $key => $value) {
            if (!is_array($value)) {
                $value = [$value];
            }
            $this->headers[$key] = $value;
            $this->headerNames[strtolower($key)] = $key;
        }
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasHeader($name)
    {
        return isset($this->headerNames[strtolower($name)]);
    }

    /**
     * @param string $name
     * @return array
     */
    public function getHeader($name)
    {
        $normalized = strtolower($name);
        if (!isset($this->headerNames[$normalized])) {
            return [];
        }
        return $this->headers[$this->headerNames[$normalized]];
    }

    /**
     * @param string $name
     * @return string
     */
    public function getHeaderLine($name)
    {
        return implode(',', $this->getHeader($name));
    }

    /**
     * @param string $name
     * @param string|string[] $value
     * @return Message|MessageInterface
     */
    public function withHeader($name, $value)
    {
        if (!is_array($value)) {
            $value = [$value];
        }
        $message = clone $this;
        $normalized = strtolower($name);
        if (isset($message->headerNames[$normalized])) {
            $name = $message->headerNames[$normalized];
        }
        $message->headerNames[$normalized] = $name;
        $message->headers[$name] = $value;
        return $message;
    }

    /**
     * @param string $name
     * @param string|string[] $value
     * @return Message|MessageInterface
     */
    public function withAddedHeader($name, $value)
    {
        if (!is_array($value)) {
            $value = [$value];
        }
        $message = clone $this;
        $normalized = strtolower($name);
        if (isset($message->headerNames[$normalized])) {
            $name = $message->headerNames[$normalized];
            foreach ($value as $val) {
                $message->headers[$name][] = $val;
            }
        } else {
            $message->headerNames[$normalized] = $name;
            $message->headers[$name] = $value;
        }
        return $message;
    }

    /**
     * @param string $name
     * @return Message|MessageInterface
     */
    public function withoutHeader($name)
    {
        $message = clone $this;
        $normalized = strtolower($name);
        if (!isset($message->headerNames[$normalized])) {
            return $message;
        }
        $name = $message->headerNames[$normalized];
        unset($message->headerNames[$normalized], $message->headers[$name]);
        return $message;
    }

    /**
     * @return StreamInterface
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param StreamInterface $body
     * @return Message|MessageInterface
     */
    public function withBody(StreamInterface $body)
    {
        $message = clone $this;
        $message->body = $body;
        return $message;
    }
}