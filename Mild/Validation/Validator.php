<?php
/**
 * Mild Framework component
 *
 * @author Mochammad Riyadh Ilham Akbar Pasya
 * @link https://github.com/mildphp/mild
 * @copyright 2018
 * @license https://github.com/mildphp/mild/blob/master/LICENSE (MIT Licence)
 */
namespace Mild\Validation;

use UnexpectedValueException;
use Mild\Supports\MessageBag;
use Mild\Validation\Rules\Rule;

class Validator
{
    /**
     * @var MessageBag
     */
    protected $messageBag;

    /**
     * Validator constructor.
     * @param Factory $factory
     * @param MessageBag $messageBag
     * @param array $datas
     * @param array $rules
     * @param array $customMessages
     * @throws \Exception
     */
    public function __construct($factory, $messageBag, array $datas, array $rules, array $customMessages = [])
    {
        $messages = [];
        foreach ($rules as $key => $rule) {
            $oldKey = $key;
            if (is_string($rule)) {
                $rule = explode('|', $rule);
            }
            if (!is_array($rule)) {
                $rule = [$rule];
            }
            $index = null;
            $ruleName = null;
            foreach ($rule as $r) {
                $options = [];
                if (is_string($r)) {
                    if (strpos($r, ':')) {
                        [$r, $options] = explode(':', $r);
                        $options = explode(',', $options);
                    }
                    $ruleName = $r;
                    $r = $factory->getExtension($r);
                    $r = new $r($options);
                }
                if (strpos($key, '.')) {
                    [$key, $index] = explode('.', $key);
                }
                if (!is_null($index)) {
                    if ($index === '*') {
                        foreach ($datas[$key] as $k => $v) {
                            if (!isset($exceptsIfOtherRule[$key][$k]) && $r->passes($datas, $key, $v) === false) {
                                $messages[$key.'.'.$k][] = ($ruleName && isset($customMessages[$key.'.'.$k.'.'.$ruleName]))? $customMessages[$key.'.'.$k.'.'.$ruleName] : (($ruleName && isset($customMessages[$oldKey.'.'.$ruleName])) ? $customMessages[$oldKey.'.'.$ruleName] : sprintf($r->message(), $key, ...$options));
                            }
                        }
                        $removeIfHasOtherRule = true;
                    }
                    if(isset($datas[$key][$index])) {
                        if (isset($removeIfHasOtherRule) && $removeIfHasOtherRule === true) {
                            $messages[$key.'.'.$index] = [];
                        }
                        if (!$r->passes($datas, $key, $datas[$key][$index])) {
                            $messages[$key.'.'.$index][] = isset($customMessages[$oldKey.'.'.$ruleName]) ? $customMessages[$oldKey.'.'.$ruleName] : sprintf($r->message(), $key.'.'.$index, ...$options);
                        }
                        $exceptsIfOtherRule[$key][$index] = true;
                    }
                } else {
                    if (isset($datas[$key]) && $r->passes($datas, $key, $datas[$key]) === false) {
                        if (isset($customMessages[$oldKey.'.'.$ruleName])) {
                            $messages[$key][] = $customMessages[$oldKey.'.'.$ruleName];
                        } else {
                            $messages[$key][] = sprintf($r->message(), $key, ...$options);
                        }
                    }
                }
            }
        }
        ksort($messages);
        $messageBag->add($messages);
        $this->messageBag = $messageBag;
    }

    /**
     * @param $datas
     * @return array
     */
    protected function parseData($datas)
    {
        $newDatas = [];
        foreach ($datas as $key => $value) {
            if (is_array($value)) {
                $value = $this->parseData($value);
            }
            if (strpos($key, '.')) {
                $key = str_replace('.', '->', $key);
            }
            $newDatas[$key] = $value;
        }
        return $newDatas;
    }

    /**
     * @return bool
     */
    public function fails()
    {
        return !$this->passes();
    }

    /**
     * @throws ValidationException
     */
    public function validate()
    {
        if ($this->fails()) {
            throw new ValidationException($this);
        }
    }

    /**
     * @return bool
     */
    public function passes()
    {
        return $this->messageBag->isEmpty();
    }

    /**
     * @return MessageBag
     */
    public function getMessageBag()
    {
        return $this->messageBag;
    }
}