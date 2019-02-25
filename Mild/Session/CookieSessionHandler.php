<?php
/**
 * Mild Framework component
 *
 * @author Mochammad Riyadh Ilham Akbar Pasya
 * @link https://github.com/mildphp/mild
 * @copyright 2018
 * @license https://github.com/mildphp/mild/blob/master/LICENSE (MIT Licence)
 */
namespace Mild\Session;

use SessionHandlerInterface;

class CookieSessionHandler implements SessionHandlerInterface
{
    /**
     * @var string
     */
    protected $path;
    /**
     * @var string
     */
    protected $prefix;
    /**
     * @var string
     */
    protected $domain;
    /**
     * @var \Mild\Cookie\CookieJar
     */
    protected $cookie;
    /**
     * @var int
     */
    protected $expired;
    /**
     * @var string
     */
    protected $sameSite;
    /**
     * @var bool
     */
    protected $secure = false;
    /**
     * @var bool
     */
    protected $httpOnly = false;


    /**
     * CookieSessionHandler constructor.
     * @param \Mild\Cookie\CookieJar $cookie
     * @param array $config
     */
    public function __construct($cookie, $config = [])
    {
        $this->cookie = $cookie;
        if (isset($config['path'])) {
            $this->setPath($config['path']);
        }
        if (isset($config['prefix'])) {
            $this->setPrefix($config['prefix']);
        }
        if (isset($config['domain'])) {
            $this->setDomain($config['domain']);
        }
        if (isset($config['expired'])) {
            $this->setExpired($config['expired']);
        }
        if (isset($config['secure'])) {
            $this->setSecure($config['secure']);
        }
        if (isset($config['httpOnly'])) {
            $this->setHttpOnly($config['httpOnly']);
        }
        if (isset($config['sameSite'])) {
            $this->setSameSite($config['sameSite']);
        }
    }

    /**
     * @param $path
     * @return void
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @param $prefix
     * @return void
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * @param $domain
     * @return void
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * @param $expired
     * @return void
     */
    public function setExpired($expired)
    {
        $this->expired = $expired;
    }

    /**
     * @param $secure
     * @return void
     */
    public function setSecure($secure)
    {
        $this->secure = $secure;
    }

    /**
     * @param $httpOnly
     * @return void
     */
    public function setHttpOnly($httpOnly)
    {
        $this->httpOnly = $httpOnly;
    }

    /**
     * @param $sameSite
     * @return void
     */
    public function setSameSite($sameSite)
    {
        $this->sameSite = $sameSite;
    }

    /**
     * @return mixed|string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return mixed|string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @return mixed|string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @return \Mild\Cookie\CookieJar
     */
    public function getCookie()
    {
        return $this->cookie;
    }

    /**
     * @return int|mixed
     */
    public function getExpired()
    {
        return $this->expired;
    }

    /**
     * @return mixed|string
     */
    public function getSameSite()
    {
        return $this->sameSite;
    }

    /**
     * @return bool|mixed
     */
    public function isSecure()
    {
        return $this->secure;
    }

    /**
     * @return bool|mixed
     */
    public function isHttpOnly()
    {
        return $this->httpOnly;
    }

    /**
     * @return bool
     */
    public function close()
    {
        return true;
    }

    /**
     * @param string $session_id
     * @return bool
     * @throws \Exception
     */
    public function destroy($session_id)
    {
        return $this->cookie->put($this->getName($session_id));
    }

    /**
     * @param int $maxlifetime
     * @return bool
     */
    public function gc($maxlifetime)
    {
        return true;
    }

    /**
     * @param string $save_path
     * @param string $name
     * @return bool
     */
    public function open($save_path, $name)
    {
        return true;
    }

    /**
     * @param string $session_id
     * @return string
     */
    public function read($session_id)
    {
        return $this->cookie->get($this->getName($session_id)) ?: '';
    }

    /**
     * @param string $session_id
     * @param string $session_data
     * @return bool
     * @throws \Exception
     */
    public function write($session_id, $session_data)
    {
        return $this->cookie->set($this->getName($session_id), $session_data, $this->expired, $this->path, $this->domain, $this->secure, $this->httpOnly, $this->sameSite);
    }

    /**
     * @param $session_id
     * @return string
     */
    protected function getName($session_id)
    {
        return $this->prefix.$session_id;
    }
}