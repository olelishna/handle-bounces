<?php

declare(strict_types=1);

namespace App\Service\HandleBounced\ParameterFormula;

interface Formula
{
    public function calc(float|int $value, float $base_value, float $coefficient = 1): float;
}