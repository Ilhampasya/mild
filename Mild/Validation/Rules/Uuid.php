<?php
/**
 * Mild Framework component
 *
 * @author Mochammad Riyadh Ilham Akbar Pasya
 * @link https://github.com/mildphp/mild
 * @copyright 2018
 * @license https://github.com/mildphp/mild/blob/master/LICENSE (MIT Licence)
 */
namespace Mild\Validation\Rules;

class Uuid extends Rule
{

    /**
     * @param array $datas
     * @param $attribute
     * @param $value
     * @return bool
     */
    public function passes(array $datas, $attribute, $value)
    {
        return preg_match('/^[\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12}$/iD', $value) > 0;
    }

    /**
     * @return string
     */
    public function message()
    {
        return 'The %s must be a valid UUID.';
    }
}