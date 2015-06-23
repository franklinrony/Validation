<?php

/*
 * This file is part of Respect\Validation.
 *
 * For the full copyright and license information, please view the "LICENSE.md"
 * file that was distributed with this source code.
 */

namespace Respect\Validation\Rules;

use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Helpers\FactoryAwareHelper;
use Respect\Validation\Validator;

/**
 * Negates any rule.
 */
final class Not extends AbstractProxy implements FactoryAware
{
    use FactoryAwareHelper;

    /**
     * {@inheritDoc}
     */
    public function assert($input)
    {
        try {
            $this->getRule()->assert($input);
        } catch (ValidationException $exception) {
            return;
        }

        $context = ['input' => $input, 'mode' => ValidationException::MODE_NEGATIVE];
        $rule = $this->filterRule($this->getRule());

        throw $this->getFactory()->createException($rule, $context);
    }

    /**
     * @return Assertable
     */
    private function filterRule(Assertable $rule)
    {
        if (!$rule instanceof AllOf) {
            return $rule;
        }

        foreach ($rule->getRules() as $childRule) {
            if ($childRule instanceof AllOf) {
                return $this->filterRule($childRule);
            }

            return $childRule;
        }

        return $rule;
    }
}