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

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\ServerRequestInterface;

class Request extends Message implements ServerRequestInterface
{
    /**
     * @var UriInterface
     */
    protected $uri;
    /**
     * @var string
     */
    protected $method;
    /**
     * @var string
     */
    protected $requestTarget;
    /**
     * @var array
     */
    protected $attributes = [];
    /**
     * @var array
     */
    protected $parsedBody = [];
    /**
     * @var array
     */
    protected $queryParams = [];
    /**
     * @var array
     */
    protected $serverParams = [];
    /**
     * @var array
     */
    protected $cookieParams = [];
    /**
     * @var array
     */
    protected $uploadedFiles = [];

    /**
     * Request constructor.
     * @param $method
     * @param UriInterface $uri
     * @param array $headers
     * @param array $serverParams
     * @param array $cookieParams
     * @param array $queryParams
     * @param array $parsedBody
     * @param array $uploadedFiles
     * @param StreamInterface $body
     * @param string $protocolVersion
     */
    public function __construct($method, $uri, $headers, $serverParams, $cookieParams, $queryParams, $parsedBody, $uploadedFiles, $body, $protocolVersion = '1.1')
    {
        $this->uri = $uri;
        $this->body = $body;
        $this->method = $method;
        $this->setHeaders($headers);
        $this->parsedBody = $parsedBody;
        $this->queryParams = $queryParams;
        $this->serverParams = $serverParams;
        $this->cookieParams = $cookieParams;
        $this->uploadedFiles = $uploadedFiles;
        $this->protocolVersion = $protocolVersion;
    }

    /**
     * @return string
     */
    public function getRequestTarget()
    {
        return $this->requestTarget;
    }

    /**
     * @return string
     */
    public function getClientIp()
    {
        if (isset($this->serverParams['HTTP_CLIENT_IP'])) {
            $ip = $this->serverParams['HTTP_CLIENT_IP'];
        } elseif (isset($this->serverParams['HTTP_X_FORWARDED_FOR'])) {
            $ip = $this->serverParams['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($this->serverParams['HTTP_X_FORWARDED'])) {
            $ip = $this->serverParams['HTTP_X_FORWARDED'];
        } elseif (isset($this->serverParams['HTTP_FORWARDED_FOR'])) {
            $ip = $this->serverParams['HTTP_FORWARDED_FOR'];
        } elseif (isset($this->serverParams['HTTP_FORWARDED_FOR'])) {
            $ip = $this->serverParams['HTTP_FORWARDED_FOR'];
        } elseif (isset($this->serverParams['REMOTE_ADDR'])) {
            $ip = $this->serverParams['REMOTE_ADDR'];
        } else {
            $ip = 'unknown';
        }
        return $ip;
    }

    /**
     * @param mixed $requestTarget
     * @return Request|ServerRequestInterface
     */
    public function withRequestTarget($requestTarget)
    {
        if (preg_match('#\s#', $requestTarget)) {
            throw new InvalidArgumentException('The request target must be an string and no have a whitespace');
        }
        $request = clone $this;
        $request->requestTarget = $requestTarget;
        return $request;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        if (isset($this->parsedBody['_method'])) {
            $this->method = strtoupper($this->parsedBody['_method']);
        }
        return $this->method;
    }

    /**
     * @param string $method
     * @return Request|ServerRequestInterface
     */
    public function withMethod($method)
    {
        $request = clone $this;
        $request->method = $method;
        return $request;
    }

    /**
     * @return UriInterface
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param UriInterface $uri
     * @param bool $preserveHost
     * @return Request|ServerRequestInterface
     */
    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        $request = clone $this;
        $request->uri = $uri;
        $host = $uri->getHost();
        if (!$preserveHost) {
            if ($host !== '') {
                $request->headers['Host'] = [$host];
            }
        } else {
            if ($host !== '' && !$this->hasHeader('Host')) {
                $request->headers['Host'] = [$host];
            }
        }
        return $request;
    }

    /**
     * @return array
     */
    public function getServerParams()
    {
        return $this->serverParams;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getServerParam($key)
    {
        if (isset($this->serverParams[$key])) {
            return $this->serverParams[$key];
        }
        return '';
    }

    /**
     * @return array
     */
    public function getCookieParams()
    {
        return $this->cookieParams;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getCookieParam($key)
    {
        if (isset($this->cookieParams[$key])) {
            return $this->cookieParams[$key];
        }
        return '';
    }

    /**
     * @param array $cookies
     * @return ServerRequestInterface
     */
    public function withCookieParams(array $cookies)
    {
        $request = clone $this;
        $request->cookieParams = $cookies;
        return $request;
    }

    /**
     * @return array
     */
    public function getQueryParams()
    {
        return $this->queryParams;
    }

    /**
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    public function getQueryParam($key, $default = null)
    {
        if (!isset($this->queryParams[$key])) {
            return $default;
        }
        return $this->queryParams[$key];
    }

    /**
     * @param array $query
     * @return Request|ServerRequestInterface
     */
    public function withQueryParams(array $query)
    {
        $request = clone $this;
        $request->queryParams = $query;
        return $request;
    }

    /**
     * @return array
     */
    public function getUploadedFiles()
    {
        return $this->uploadedFiles;
    }

    /**
     * @param array $uploadedFiles
     * @return Request|ServerRequestInterface
     */
    public function withUploadedFiles(array $uploadedFiles)
    {
        $request = clone $this;
        $request->uploadedFiles = $uploadedFiles;
        return $request;
    }

    /**
     * @return array|object|null
     */
    public function getParsedBody()
    {
        return $this->parsedBody;
    }

    /**
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    public function getParsedBodyParam($key, $default = null)
    {
        if (!isset($this->parsedBody[$key])) {
            return $default;
        }
        return $this->parsedBody[$key];
    }

    /**
     * @param array|object|null $data
     * @return Request|ServerRequestInterface
     */
    public function withParsedBody($data)
    {
        $request = clone $this;
        $request->parsedBody = $data;
        return $request;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasAttribute($name)
    {
        return isset($this->attributes[$name]);
    }

    /**
     * @param string $name
     * @param null $default
     * @return mixed|null
     */
    public function getAttribute($name, $default = null)
    {
        if ($this->hasAttribute($name)) {
            return $this->attributes[$name];
        }
        return $default;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return Request|ServerRequestInterface
     */
    public function withAttribute($name, $value)
    {
        $request = clone $this;
        $request->attributes[$name] = $value;
        return $request;
    }

    /**
     * @param string $name
     * @return Request|ServerRequestInterface
     */
    public function withoutAttribute($name)
    {
        $request = clone $this;
        unset($request->attributes[$name]);
        return $request;
    }

    /**
     * @return Request
     */
    public static function capture()
    {
        $headers = [];
        $method = 'GET';
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
        }
        if (isset($_SERVER['REQUEST_METHOD'])) {
            $method = $_SERVER['REQUEST_METHOD'];
        } 
        stream_copy_to_stream(fopen('php://temp', 'w+'), $stream = fopen('php://input','r'));
        rewind($stream);
        return new static($method, Uri::capture(), $headers, $_SERVER, $_COOKIE, $_GET, $_POST, static::parseUploadedFiles($_FILES), new Stream($stream));
    }

    /**
     * @param $files
     * @return array
     */
    protected static function parseUploadedFiles($files)
    {
        $parsers = [];
        foreach ($files as $field => $file) {
            if (!is_array($file['error'])) {
                $parsers[$field] = new UploadedFile($file['tmp_name'], $file['name'], $file['type'], $file['size'], $file['error']);
            } else {
                foreach ($file['error'] as $key => $value) {
                    $parsers[$field][] = new UploadedFile($file['tmp_name'][$key], $file['name'][$key], $file['type'][$key], $file['size'][$key], $file['error'][$key]);
                }
            }
        }
        return $parsers;
    }

    /**
     * @return bool
     */
    public function isXhr()
    {
        return $this->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';
    }

    /**
     * @return bool
     */
    public function isJson()
    {
        return strpos($this->getHeaderLine('Content-Type'), 'json') !== false;
    }
}