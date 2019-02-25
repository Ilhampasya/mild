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

class Uri implements UriInterface
{
    /**
     * @var string
     */
    protected $host;
    /**
     * @var string
     */
    protected $path;
    /**
     * @var int
     */
    protected $port;
    /**
     * @var string
     */
    protected $query;
    /**
     * @var string
     */
    protected $scheme;
    /**
     * @var string
     */
    protected $fragment;
    /**
     * @var string
     */
    protected $userInfo;
    /**
     * @var
     */
    protected $password;

    /**
     * Uri constructor.
     * @param $scheme
     * @param $host
     * @param null $port
     * @param string $path
     * @param string $query
     * @param string $fragment
     * @param string $userInfo
     * @param string $password
     */
    public function __construct($scheme, $host = 'localhost', $port = null, $path = '/', $query = '', $fragment = '', $userInfo = '', $password = '')
    {
        $this->host = $host;
        $this->port = $port;
        $this->path = $path;
        $this->query = $query;
        $this->scheme = $scheme;
        $this->fragment = $fragment;
        $this->userInfo = $userInfo;
        $this->password = $password;

    }

    /**
     * @return string
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * @return string
     */
    public function getAuthority()
    {
        $authority = '';
        if (!empty($this->userInfo)) {
            $authority .= $this->userInfo.'@';
        }
        $authority .= $this->host;
        if (!empty($this->port)) {
            $authority .= ':'.$this->port;
        }
        return $authority;
    }

    /**
     * @return string
     */
    public function getUserInfo()
    {
        return $this->userInfo;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return int|null
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return string
     */
    public function getFragment()
    {
        return $this->fragment;
    }

    /**
     * @param string $scheme
     * @return Uri|UriInterface
     */
    public function withScheme($scheme)
    {
        if (!is_string($scheme)) {
            throw new InvalidArgumentException('The scheme type must be an string');
        }
        $uri = clone $this;
        $uri->scheme = $scheme;
        return $uri;
    }

    /**
     * @param string $user
     * @param null $password
     * @return Uri|UriInterface
     */
    public function withUserInfo($user, $password = null)
    {
        $uri = clone $this;
        $uri->userInfo = $user;
        if ($password) {
            $uri->password = $password;
        }
        return $uri;
    }

    /**
     * @param string $host
     * @return Uri|UriInterface
     */
    public function withHost($host)
    {
        if (!is_string($host)) {
            throw new InvalidArgumentException('The host type must be an string.');
        }
        $uri = clone $this;
        $uri->host = $host;
        return $uri;
    }

    /**
     * @param int|null $port
     * @return Uri|UriInterface
     */
    public function withPort($port)
    {
        $uri = clone $this;
        $uri->port = (int) $port;
        return $uri;
    }

    /**
     * @param string $path
     * @return Uri|UriInterface
     */
    public function withPath($path)
    {
        $uri = clone $this;
        $uri->path = $path;
        return $uri;
    }

    /**
     * @param string $query
     * @return UriInterface|string
     */
    public function withQuery($query)
    {
        if (!is_string($query)) {
            throw new InvalidArgumentException('The query type must be an string.');
        }
        $uri = clone $this;
        $uri->query = $query;
        return $uri;
    }

    /**
     * @param string $fragment
     * @return Uri|UriInterface
     */
    public function withFragment($fragment)
    {
        $uri = clone $this;
        $uri->fragment = $fragment;
        return $uri;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $query = $this->query;
        $scheme = $this->scheme;
        $fragment = $this->fragment;
        $authority = $this->getAuthority();
        if ($scheme !== '') {
            $scheme .= ':';
        }
        if ($authority !== '') {
            $authority = '//'.$authority;
        }
        if ($query !== '') {
            $query = '?'.$query;
        }
        if ($fragment !== '') {
            $fragment = '#'.$fragment;
        }
        return $scheme.$authority.$this->path.$query.$fragment;
    }

    /**
     * @return Uri
     */
    public static function capture()
    {
        $query = '';
        $port = null;
        $scheme = 'http';
        $requestUri = '';
        $host = 'localhost';
        if (isset($_SERVER['HTTP_HOST'])) {
            $host = $_SERVER['HTTP_HOST'];
        } 
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            $scheme = 'https';
        } 
        if (isset($_SERVER['REQUEST_URI'])) {
            $requestUri = $_SERVER['REQUEST_URI'];
        } 
        $partsHost = explode(':', $host);
        $host = $partsHost[0];
        if (isset($partsHost[1])) {
            $port = $partsHost[1];
        } 
        $partsUri = explode('?', $requestUri);
        $path = $partsUri[0];
        if (isset($partsUri[1])) {
            $query = $partsUri[1];
        }
        return new static($scheme, $host, $port, $path, $query);

    }
}