<?php

declare(strict_types=1);

namespace App\Service\HandleBounced;

use App\Entity\HandleBounced\SuppressedClient;
use App\Service\HandleBounced\Parameter\Parameter;
use App\Service\HandleBounced\QualityFormula\Formula;

class Appraiser
{
    /**
     * @param Parameter[] $parameters
     */
    public function __construct(
        private readonly Formula $formula,
        private array $parameters
    ) {
        $this->processParametersWeights();
    }

    private function processParametersWeights(): void
    {
        $weights_sum = $this->getSumOfWeights();

        foreach ($this->parameters as $parameter) {
            $parameter->setImportanceCoefficient($weights_sum);
        }
    }

    private function getSumOfWeights(): int
    {
        $weights_sum = 0;

        foreach ($this->parameters as $parameter) {
            $weights_sum += $parameter->getWeight();
        }

        return $weights_sum;
    }

    public function assess(SuppressedClient $user): float
    {
        $quality_values = [];

        array_walk($this->parameters, static function ($parameter) use (&$quality_values, $user) {
            $quality_values[] = $parameter->getQuality($user);
        });

        return $this->formula::calc($quality_values);
    }
}