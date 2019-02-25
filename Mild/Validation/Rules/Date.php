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

class Date extends Rule
{

    /**
     * @param array $datas
     * @param $attribute
     * @param $value
     * @return bool
     */
    public function passes(array $datas, $attribute, $value)
    {
        if (strtotime($value) === false) {
            return false;
        }
        $parsers = date_parse($value);
        return checkdate($parsers['month'], $parsers['day'], $parsers['year']);
    }

    /**
     * @return string
     */
    public function message()
    {
        return 'The %s is not a valid date.';
    }
}