<?php

namespace Respect\Validation\Rules;

use Respect\Validation\Rules\AbstractRule;
use Respect\Validation\Exceptions\AlphaException;
use Respect\Validation\Exceptions\ComponentException;

class Alpha extends AbstractRule
{

    protected $additionalChars = '';

    public function __construct($additionalChars='')
    {
        if (!is_string($additionalChars))
            throw new ComponentException(
                'Invalid list of additional characters to be loaded'
            );
        $this->additionalChars = $additionalChars;
    }

    public function createException()
    {
        return new AlphaException;
    }

    public function validate($input)
    {
        return is_string($input) && preg_match(
            "#^[a-zA-Z]+$#",
            str_replace(str_split($this->additionalChars), '', $input)
        );
    }

    public function assert($input)
    {
        $templateCode = empty($this->additionalChars) ? AlphaException::INVALID_ALPHA : AlnumException::INVALID_ALPHA_CHARS;
        if (!$this->validate($input))
            throw $this
                ->getException()
                ->setMessageTemplateFromCode($templateCode)
                ->setParams($input, $this->additionalChars);
        return true;
    }

    public function check($input)
    {
        return $this->assert($input);
    }

}