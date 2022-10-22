<?php

declare(strict_types=1);

namespace App\Service\HandleBounced\Parameter;

use App\Entity\HandleBounced\SuppressedClient;
use App\Service\HandleBounced\ParameterFormula\Formula;
use App\Service\Keap\InfusionsoftApi;

class CompanyUserParameter extends AbstractParameter
{
    public function __construct(
        int $weight,
        float $base_value,
        float $coefficient_of_quality,
        Formula $formula,
        private readonly InfusionsoftApi $infusionsoftApi
    ) {
        parent::__construct($weight, $base_value, $coefficient_of_quality, $formula);
    }

    public function getQuality(SuppressedClient $client): float
    {
        $is_company_exists = $this->infusionsoftApi->getContactCompanyExists($client->getEmail());

        $single_quality = $this->formula->calc(
            $is_company_exists,
            $this->base_value,
            $this->coefficient_of_quality
        );

        return $this->importance_coefficient * $single_quality;
    }
}