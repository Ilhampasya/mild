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

use InvalidArgumentException;
use Psr\Http\Message\UploadedFileInterface;

abstract class Rule
{
    /**
     * The default options on rule
     * Set true on param if the rule should with a parameter
     * Set num count of parameter in count key, if the num of parameter more than 1, just set 1
     * 
     * @var array
     */
    protected $options = [
        'param' => false,
        'count' => 0
    ];
    /**
     * Set options on param true to get parameters on the rule,
     * And set minimum count of parameters needed
     * 
     * @var array
     */
    protected $parameters = [];

    /**
     * Rule constructor.
     * 
     * @param array $parameters
     */
    public function __construct(array $parameters = [])
    {
        if ($this->shouldWithParam()) {
            if (empty($parameters)) {
                throw new InvalidArgumentException('Missing parameters in '.static::class.'');
            }
            if (count($parameters) < $this->getCountOfParam()) {
                throw new InvalidArgumentException('Num of parameters in '.static::class.' does not match with parameters needle.');
            }
        }
        $this->parameters = $parameters;
    }

    /**
     * @param $value
     * @return int
     */
    public function getSize($value)
    {
        if (is_numeric($value)) {
            return $value;
        } elseif (is_array($value)) {
            return count($value);
        } elseif ($this->isUploadFile($value)) {
            return $value->getSize() / 1024;
        }
        return mb_strlen($value);;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param $value
     * @return bool
     */
    public function isUploadFile($value)
    {
        return $value instanceof UploadedFileInterface;
    }

    /**
     * @return bool
     */
    public function shouldWithParam()
    {
        return $this->options['param'];
    }

    /**
     * @return int
     */
    public function getCountOfParam()
    {
        return $this->options['count'];
    }

    /**
     * @param array $datas
     * @param $attribute
     * @param $value
     * @return bool
     */
    public function passes(array $datas, $attribute, $value)
    {
        return false;
    }

    /**
     * @return string
     */
    public function message()
    {
        return 'The rule must return a message';
    }
}