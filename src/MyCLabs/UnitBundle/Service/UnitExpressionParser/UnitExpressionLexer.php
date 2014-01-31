<?php

namespace MyCLabs\UnitBundle\Service\UnitExpressionParser;

use JMS\Parser\AbstractLexer;
use MyCLabs\UnitBundle\Service\UnitExpressionParser;

/**
 * @author matthieu.napoli
 */
class UnitExpressionLexer extends AbstractLexer
{
    /**
     * {@inheritdoc}
     */
    protected function getRegex()
    {
        return '/
            # Single unit id
            ([a-zA-Z0-9\/_]+)

            # Exponent
            |(\^-?[0-9]+)

            # Multiplier sign
            |(\.)

            # Ignore whitespaces in the expression
            |\s+
        /x'; // The x modifier tells PCRE to ignore whitespace in the regex above.
    }

    /**
     * {@inheritdoc}
     */
    protected function determineTypeAndValue($value)
    {
        if ($value === '.') {
            return array(UnitExpressionParser::T_MULTIPLIER, $value);
        }
        if (strpos($value, '^') === 0) {
            return array(UnitExpressionParser::T_EXPONENT, $value);
        }
        if (preg_match('/^[a-zA-Z0-9\/_]+$/', $value) === 1) {
            return array(UnitExpressionParser::T_UNIT, $value);
        }

        return array(UnitExpressionParser::T_UNKNOWN, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getName($type)
    {
        $tokenNames = array(
            UnitExpressionParser::T_UNKNOWN => 'UNKNOWN',
            UnitExpressionParser::T_UNIT => 'UNIT_ID',
            UnitExpressionParser::T_EXPONENT => 'EXPONENT',
            UnitExpressionParser::T_MULTIPLIER => 'MULTIPLIER',
        );

        if (! isset($tokenNames[$type])) {
            throw new \InvalidArgumentException(sprintf('There is no token with type %s.', json_encode($type)));
        }

        return $tokenNames[$type];
    }
}
