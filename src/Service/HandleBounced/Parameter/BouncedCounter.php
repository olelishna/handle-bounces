<?php

declare(strict_types=1);

namespace App\Service\HandleBounced\Parameter;

use App\Entity\HandleBounced\SuppressedClient;

class BouncedCounter extends AbstractParameter
{
    public function getQuality(SuppressedClient $client): float
    {
        return $client->getBounced()->count();
    }
}