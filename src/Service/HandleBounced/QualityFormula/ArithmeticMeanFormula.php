<?php

declare(strict_types=1);

namespace App\Service\HandleBounced\QualityFormula;

class ArithmeticMeanFormula implements Formula
{
    public static function calc(array $values): float
    {
        return array_sum($values);
    }
}