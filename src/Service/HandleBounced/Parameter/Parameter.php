<?php

declare(strict_types=1);

namespace App\Service\HandleBounced\Parameter;

use App\Entity\HandleBounced\SuppressedClient;

interface Parameter
{
    public function getWeight(): int;

    public function getQuality(SuppressedClient $client): float;

    public function setImportanceCoefficient(int $weights_sum): void;
}