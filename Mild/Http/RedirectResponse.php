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

use BadMethodCallException;
use Mild\Supports\MessageBag;
use Mild\Supports\Traits\Macroable;

class RedirectResponse extends Response
{
    use Macroable {
        Macroable::__call as macroCall;
    }
    /**
     * @var \Mild\Session\Flash
     */
    protected $flash;
    /**
     * @var Request
     */
    protected $request;

    /**
     * RedirectResponse constructor.
     *
     * @param $url
     * @param int $statusCode
     * @param array $headers
     */
    public function __construct($url, int $statusCode = 302, array $headers = [])
    {
        $headers['Location'] = [$url];
        parent::__construct($statusCode, $headers);
    }

    /**
     * @param $flash
     * @return $this
     */
    public function setFlash($flash)
    {
        $this->flash = $flash;
        return $this;
    }

    /**
     * @param $request
     * @return $this
     */
    public function setRequest($request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function with($key, $value)
    {
        $this->flash->set($key, $value);
        return $this;
    }

    /**
     * @param MessageBag $messageBag
     * @return RedirectResponse
     */
    public function withErrors($messageBag)
    {
        if ($messageBag instanceof  MessageBag === false) {
            $messageBag = new MessageBag($messageBag);
        }
        return $this->with('errors', $messageBag);
    }

    /**
     * @param array $excepts
     * @return RedirectResponse
     */
    public function withInputs($excepts = [])
    {
        $inputs = $this->request->getParsedBody();
        if ($excepts) {
            foreach ($inputs as $key => $value) {
                if (in_array($key, $excepts, true)) {
                    unset($inputs[$key]);
                }
            }
        }
        return $this->with('inputs', $inputs);
    }

    /**
     * @param $name
     * @param $arguments
     * @return $this
     */
    public function __call($name, $arguments)
    {
        if (static::hasMacro($name)) {
            return $this->macroCall($name, $arguments);
        }
        if (strpos($name, 'with') !== false) {
            return $this->with(lcfirst(substr($name,  4)), $arguments[0]);
        }
        throw new BadMethodCallException(sprintf(
            'Method %s::%s does not exists.', static::class, $name
        ));
    }
}