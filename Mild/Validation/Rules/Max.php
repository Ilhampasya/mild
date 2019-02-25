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

class Max extends Rule
{
    protected $options = [
      'param' => true,
      'count' => 1
    ];

    /**
     * @param array $datas
     * @param $attribute
     * @param $value
     * @return bool
     */
    public function passes(array $datas, $attribute, $value)
    {
        return $this->getSize($value) <= $this->parameters[0];
    }

    /**
     * @return string
     */
    public function message()
    {
        return 'The %s may not be greater than %s.';
    }
}