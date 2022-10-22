<?php

namespace App\Service\Keap;

use App\Repository\VariableRepository;
use Infusionsoft\Infusionsoft;

class InfusionsoftGenerator
{
    private Infusionsoft $infusionsoft;

    public function __construct(
        string $infusionsoft_clientId,
        string $infusionsoft_clientSecret,
        string $infusionsoft_redirectUri,
        VariableRepository $variableRepository
    ) {
        $this->infusionsoft = new Infusionsoft(
            [
                'clientId' => $infusionsoft_clientId,
                'clientSecret' => $infusionsoft_clientSecret,
                'redirectUri' => $infusionsoft_redirectUri,
            ]
        );

        $token = $variableRepository->get('infusionsoft_token');

        if ($token !== null) {
            $this->infusionsoft->setToken($token);
        }
    }

    public function getInfObject(): Infusionsoft
    {
        return $this->infusionsoft;
    }
}