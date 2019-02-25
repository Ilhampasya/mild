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

use Mild\App;

class Unique extends Rule
{
    /**
     * @var array
     */
    protected $options = [
      'param' => true,
      'count' => 1
    ];

    /**
     * @param array $datas
     * @param $attribute
     * @param $value
     * @return bool|void
     * @throws \ReflectionException
     */
    public function passes(array $datas, $attribute, $value)
    {
        $db = App::getInstance()->get('db')->table($this->parameters[0]);
        if (isset($this->parameters[1])) {
            $attribute = $this->parameters[1];
        }
        return $db->where($attribute, $value)->exists() === false;
    }

    /**
     * @return string
     */
    public function message()
    {
        return 'The %s has already been taken.';
    }
}