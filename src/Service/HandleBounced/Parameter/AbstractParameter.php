<?php

declare(strict_types=1);

namespace App\Service\HandleBounced\Parameter;

use App\Service\HandleBounced\ParameterFormula\Formula;
use RuntimeException;

abstract class AbstractParameter implements Parameter
{
    protected float $importance_coefficient;

    /**
     * @throws RuntimeException
     */
    public function __construct(
        protected readonly int $weight,
        protected readonly float $base_value,
        protected readonly float $coefficient_of_quality,
        protected readonly Formula $formula
    ) {
        if ($this->weight < 1) {
            throw new RuntimeException('Weight value for parameter can not be zero or less.');
        }
    }

    public function getWeight(): int
    {
        return $this->weight;
    }

    public function setImportanceCoefficient(int $weights_sum): void
    {
        $this->importance_coefficient = $this->weight / $weights_sum;
    }
}