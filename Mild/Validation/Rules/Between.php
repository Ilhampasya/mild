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

class Between extends Rule
{
    /**
     * @var array
     */
    protected $options = [
      'param' => true,
      'count' => 2
    ];

    /**
     * @param array $datas
     * @param $attribute
     * @param $value
     * @return bool
     */
    public function passes(array $datas, $attribute, $value)
    {
        $size = $this->getSize($value);
        return $size >= $this->parameters[0] && $size <= $this->parameters[1];
    }

    /**
     * @return string
     */
    public function message()
    {
        return 'The %s must be between %s and %s.';
    }
}