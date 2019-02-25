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

use Exception;
use Mild\Supports\MessageBag;
use Mild\Validation\Rules\Ip;
use Mild\Validation\Rules\Min;
use Mild\Validation\Rules\Max;
use Mild\Validation\Rules\Url;
use Mild\Validation\Rules\Uuid;
use Mild\Validation\Rules\Ipv4;
use Mild\Validation\Rules\Ipv6;
use Mild\Validation\Rules\Date;
use Mild\Validation\Rules\Json;
use Mild\Validation\Rules\Mime;
use Mild\Validation\Rules\Email;
use Mild\Validation\Rules\Image;
use Mild\Validation\Rules\Unique;
use Mild\Validation\Rules\Numeric;
use Mild\Validation\Rules\Between;
use Mild\Validation\Rules\Boolean;
use Mild\Validation\Rules\Nullable;
use Mild\Validation\Rules\Required;
use Mild\Validation\Rules\Confirmed;
use Mild\Validation\Rules\Different;

class Factory
{
    /**
     * @var MessageBag
     */
    protected $messageBag;
    /**
     * @var array
     */
    protected $extensions = [
        'ip' => Ip::class,
        'min' => Min::class,
        'max' => Max::class,
        'url' => Url::class,
        'uuid' => Uuid::class,
        'ipv4' => Ipv4::class,
        'ipv6' => Ipv6::class,
        'date' => Date::class,
        'json' => Json::class,
        'mimes' => Mime::class,
        'int' => Numeric::class,
        'image' => Image::class,
        'email' => Email::class,
        'bool' => Boolean::class,
        'null' => Nullable::class,
        'unique' => Unique::class,
        'empty' => Nullable::class,
        'numeric' => Numeric::class,
        'between' => Between::class,
        'boolean' => Boolean::class,
        'nullable' => Nullable::class,
        'required' => Required::class,
        'confirmed' => Confirmed::class,
        'different' => Different::class
    ];

    /**
     * Factory constructor.
     * @param MessageBag $messageBag
     */
    public function __construct($messageBag)
    {
        $this->messageBag = $messageBag;
    }

    /**
     * @param $datas
     * @param $rules
     * @param array $customMessages
     * @return Validator
     * @throws Exception
     */
    public function make($datas, $rules, $customMessages = [])
    {
        return new Validator($this, $this->messageBag, $datas, $rules, $customMessages);
    }

    /**
     * @param $datas
     * @param $rules
     * @param array $customMessages
     * @throws ValidationException
     */
    public function validate($datas, $rules, $customMessages = [])
    {
        $this->make($datas, $rules, $customMessages)->validate();
    }

    /**
     * @return array
     */
    public function getExtensions()
    {
        return $this->extensions;
    }

    /**
     * @param $name
     * @param $rule
     */
    public function addExtension($name, $rule)
    {
        $this->extensions[$name] = $rule;
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasExtension($name)
    {
        return isset($this->extensions[$name]);
    }

    /**
     * @param $name
     * @return mixed
     * @throws Exception
     */
    public function getExtension($name)
    {
        if ($this->hasExtension($name)) {
            return $this->extensions[$name];
        }
        throw new Exception('Extension '.$name.' does not exist.');
    }
}