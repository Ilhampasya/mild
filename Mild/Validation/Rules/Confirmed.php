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

class Confirmed extends Rule
{

    /**
     * @param array $datas
     * @param $attribute
     * @param $value
     * @return bool
     */
    public function passes(array $datas, $attribute, $value)
    {
        if (isset($this->parameters[0])) {
            $attribute = $this->parameters[0];
        } else {
            $attribute .= '_confirmation';
        }
        return $datas[$attribute] === $value;
    }

    /**
     * @return string
     */
    public function message()
    {
        return 'The %s confirmation does not match.';
    }
}