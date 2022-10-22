<?php

declare(strict_types=1);

namespace App\Service\HandleBounced\QualityFormula;

interface Formula
{
    public static function calc(array $values): float;
}