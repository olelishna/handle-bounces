<?php

declare(strict_types=1);

namespace App\Service\HandleBounced\QualityFormula;

class GeometricMeanFormula implements Formula
{
    public static function calc(array $values): float
    {
        return array_product($values);
    }
}