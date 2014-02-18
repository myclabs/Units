<?php

namespace MyCLabs\UnitBundle\Service;

use JMS\Parser\AbstractParser;
use JMS\Parser\SyntaxErrorException;
use MyCLabs\UnitAPI\Exception\UnknownUnitException;
use MyCLabs\UnitBundle\Entity\Unit\ComposedUnit;
use MyCLabs\UnitBundle\Entity\Unit\Unit;
use MyCLabs\UnitBundle\Entity\Unit\UnitComponent;
use MyCLabs\UnitBundle\Entity\Unit\UnitRepository;
use MyCLabs\UnitBundle\Service\UnitExpressionParser\UnitExpressionLexer;

/**
 * Parses an expression representing a unit.
 *
 * @author matthieu.napoli
 */
class UnitExpressionParser extends AbstractParser
{
    const T_UNKNOWN = 0;
    const T_UNIT = 1;
    const T_EXPONENT = 2;
    const T_MULTIPLIER = 3;

    /**
     * @var UnitRepository
     */
    private $unitRepository;

    public function __construct(UnitExpressionLexer $lexer, UnitRepository $unitRepository)
    {
        parent::__construct($lexer);

        $this->unitRepository = $unitRepository;
    }

    /**
     * Parses an expression describing a unit and returns a Unit entity.
     *
     * @param string $expression
     * @param null   $context
     *
     * @throws UnknownUnitException
     * @return Unit
     */
    public function parse($expression, $context = null)
    {
        try {
            return parent::parse($expression, $context);
        } catch (SyntaxErrorException $e) {
            throw new UnknownUnitException(
                sprintf('Invalid unit expression "%s": %s', $expression, $e->getMessage()),
                $expression,
                $e
            );
        }
    }

    /**
     * @return Unit
     */
    protected function parseInternal()
    {
        $unitId = $this->match(self::T_UNIT);

        // Simple unit (standard or discrete)
        if (! $this->lexer->isNextAny([self::T_MULTIPLIER, self::T_EXPONENT])) {
            return $this->unitRepository->find($unitId);
        }

        // From here, we have a discrete unit
        $components = [];

        if ($this->lexer->isNext(self::T_EXPONENT)) {
            $exponent = ltrim($this->match(self::T_EXPONENT), '^');
        } else {
            $exponent = 1;
        }
        $components[] = new UnitComponent($this->unitRepository->find($unitId), $exponent);

        while ($this->lexer->isNextAny([self::T_MULTIPLIER])) {
            // Ignore "."
            $this->lexer->moveNext();

            $unitId = $this->match(self::T_UNIT);

            if ($this->lexer->isNext(self::T_EXPONENT)) {
                $exponent = ltrim($this->match(self::T_EXPONENT), '^');
            } else {
                $exponent = 1;
            }

            $components[] = new UnitComponent($this->unitRepository->find($unitId), $exponent);
        }

        return new ComposedUnit($components);
    }
}
