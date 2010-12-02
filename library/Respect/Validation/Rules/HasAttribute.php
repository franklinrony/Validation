<?php

namespace Respect\Validation\Rules;

use Respect\Validation\Exceptions\HasAttributeException;
use Respect\Validation\Rules\AllOf;
use Respect\Validation\Exceptions\ComponentException;
use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validatable;
use \ReflectionProperty;
use \ReflectionException;

class HasAttribute extends AllOf
{

    protected $attribute = '';

    public function __construct($attribute, Validatable $attributeValidator=null)
    {
        if (!is_string($attribute))
            throw new ComponentException(
                'Invalid attribute name'
            );
        $this->attribute = $attribute;
        if (!is_null($attributeValidator))
            $this->addRule($attributeValidator);
    }

    public function createException()
    {
        return new HasAttributeException(HasAttributeException::INVALID_HAS_ATTRIBUTE);
    }

    protected function getAttributeValue($input)
    {
        $propertyMirror = new ReflectionProperty($input, $this->attribute);
        $propertyMirror->setAccessible(true);
        return $propertyMirror->getValue($input);
    }

    public function validate($input)
    {
        try {
            return parent::validate(
                $this->getAttributeValue($input)
            );
        } catch (ReflectionException $e) {
            return false;
        }
    }

    public function assert($input)
    {
        try {
            parent::assert(
                    $this->getAttributeValue($input)
            );
        } catch (ReflectionException $e) {
            throw $this
                ->getException()
                ->setParams($this->attribute);
        } catch (ValidationException $e) {
            throw $this
                ->getException()
                ->setMessageTemplateFromCode(HasAttributeException::INVALID_HAS_ATTRIBUTE_RELATED)
                ->setParams($this->attribute);
        }
        return true;
    }

    public function check($input)
    {
        return $this->assert($input);
    }

}