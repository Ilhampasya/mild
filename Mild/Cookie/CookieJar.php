<?php
/**
 * Mild Framework component
 *
 * @author Mochammad Riyadh Ilham Akbar Pasya
 * @link https://github.com/mildphp/mild
 * @copyright 2018
 * @license https://github.com/mildphp/mild/blob/master/LICENSE (MIT Licence)
 */
namespace Mild\Cookie;

use DateTime;
use DateTimeInterface;
use Mild\Encryption\EncryptionException;

class CookieJar
{
    /**
     * @var \Mild\Encryption\Encryption
     */
    protected $encryption;

    /**
     * CookieJar constructor.
     * @param \Mild\Encryption\Encryption $encryption
     */
    public function __construct($encryption)
    {
        $this->encryption = $encryption;
    }

    /**
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return isset($_COOKIE[$name]);
    }

    /**
     * @param $name
     * @param null $default
     * @return string|null
     */
    public function get($name, $default = null)
    {
        if (!$this->has($name)) {
            return $default;
        }
        $value = $_COOKIE[$name];
        try {
            return $this->encryption->decrypt($value);
        } catch (EncryptionException $e) {
            return $value;
        }
    }

    /**
     * @return mixed
     */
    public function all()
    {
        $items = [];
        foreach ($_COOKIE as $key => $value) {
            $items[$key] = $this->get($key);
        }
        return $items;
    }

    /**
     * @param $name
     * @param $value
     * @param $expired
     * @param string $path
     * @param null $domain
     * @param bool $secure
     * @param bool $httpOnly
     * @param null $sameSite
     * @return bool
     * @throws \Exception
     */
    public function set($name, $value, $expired, $path = '/', $domain = null, $secure = false, $httpOnly = true, $sameSite = null)
    {
        if (is_string($expired)) {
            $expired = new DateTime($expired);
        }
        if ($expired instanceof DateTimeInterface) {
            $expired = $expired->format('U');
        }
        $value = $this->encryption->encrypt($value);
        if (!empty($sameSite)) {
            $domain .= ';samesite='.$sameSite;
        }
        return setcookie($name, $value, $expired, $path, $domain, $secure, $httpOnly);
    }

    /**
     * @param $name
     * @param $value
     * @param string $path
     * @param null $domain
     * @param bool $secure
     * @param bool $httpOnly
     * @param null $sameSite
     * @return bool
     * @throws \Exception
     */
    public function setForever($name, $value, $path = '/', $domain = null, $secure = false, $httpOnly = true, $sameSite = null)
    {
        return $this->set($name, $value, time() + 315360000, $path, $domain, $secure, $httpOnly, $sameSite);
    }

    /**
     * @param $name
     * @return bool
     * @throws \Exception
     */
    public function put($name)
    {
        return $this->set($name, '', time() - 3600);
    }
}