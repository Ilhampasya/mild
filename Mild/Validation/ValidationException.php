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

class ValidationException extends Exception
{
    /**
     * @var Validator
     */
    protected $validator;

    /**
     * ValidationException constructor.
     * @param Validator $validator
     */
    public function __construct($validator)
    {
        $this->validator = $validator;
        parent::__construct('The given data was invalid.');
    }

    /**
     * @return Validator
     */
    public function getValidator()
    {
        return $this->validator;
    }
}