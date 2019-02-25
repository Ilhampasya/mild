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

use RuntimeException;

class RouterException extends RuntimeException
{
    /**
     * RouterException constructor.
     * @param int $code
     * @param string $reasonPhrase
     */
    public function __construct($code = 404, $reasonPhrase = '')
    {
        parent::__construct($reasonPhrase, $code);
    }
}