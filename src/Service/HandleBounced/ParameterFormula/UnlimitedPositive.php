<?php

declare(strict_types=1);

namespace App\Service\HandleBounced\ParameterFormula;

use RuntimeException;

class UnlimitedPositive implements Formula
{
    /**
     * @throws RuntimeException
     */
    public function calc(float|int $value, float $base_value, float $coefficient = 1): float
    {
        if ($base_value === 0) {
            throw new RuntimeException("Base value can't be zero for the Unlimited Positive formula");
        }

        return (($value - $base_value) / $base_value) * $coefficient + 1;
    }
}