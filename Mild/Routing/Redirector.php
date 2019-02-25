<?php
/**
 * Mild Framework component
 *
 * @author Mochammad Riyadh Ilham Akbar Pasya
 * @link https://github.com/mildphp/mild
 * @copyright 2018
 * @license https://github.com/mildphp/mild/blob/master/LICENSE (MIT Licence)
 */
namespace Mild\Routing;

use Mild\Http\RedirectResponse;

class Redirector
{
    /**
     * @var \Mild\Session\Flash
     */
    protected $flash;
    /**
     * @var Router
     */
    protected $router;
    /**
     * @var \Mild\Http\Request
     */
    protected $request;

    /**
     * @param \Mild\Session\Flash
     * @param Router
     * @param \Mild\Http\Request
     */
    public function __construct($flash, $router, $request)
    {
        $this->flash = $flash;
        $this->router = $router;
        $this->request = $request;
    }

    /**
     * @param $url
     * @param int $status
     * @param array $headers
     * @return RedirectResponse
     */
    public function to($url, $status = 302, $headers = [])
    {
        return (new RedirectResponse($url, $status, $headers))->setFlash($this->flash)->setRequest($this->request);
    }

    /**
     * @param int $status
     * @param array $headers
     * @return RedirectResponse
     */
    public function back($status = 302, $headers= [])
    {
        return $this->to(($this->request->getServerParam('HTTP_REFERER') ?: $this->request->getUri()->getPath()), $status, $headers);
    }

    /**
     * @param $name
     * @param int $status
     * @param array $headers
     * @return RedirectResponse
     */
    public function route($name, $status = 302, $headers = [])
    {
        return $this->to($this->router->getName($name), $status, $headers);
    }
}