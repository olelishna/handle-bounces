<?php

namespace App\Controller;

use App\Entity\HandleBounced\SuppressedClient;
use App\Repository\HandleBounced\SuppressedClientRepository;
use App\Utils\Pager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/suppressed')]
class SuppressedController extends AbstractController
{
    #[Route('/', name: 'app_suppressed')]
    public function index(Request $request, Pager $pager, SuppressedClientRepository $clientRepository): Response
    {
        $offset = filter_var(
            $request->get('offset', 0),
            FILTER_VALIDATE_INT,
            ["options" => ["min_range" => 0, "default" => 0]]
        );

        $limit = filter_var(
            $request->get('limit', $pager->default_limit),
            FILTER_VALIDATE_INT,
            ["options" => ["min_range" => 1, "default" => $pager->default_limit]]
        );

        $clients = $clientRepository->findAllView($offset, $limit);
        $count_clients = $clientRepository->findAllViewCount();

        $path_data = $request->query->all();

        $pager->load($offset, $limit, $count_clients, $request->attributes->get('_route'), $path_data);

        return $this->render('suppressed/index.html.twig', [
            'clients' => $clients,
            'pager' => $pager,
        ]);
    }

    #[Route('/{email}', name: 'app_suppressed_delete', methods: ["DELETE", "POST"])]
    public function delete(
        Request $request,
        SuppressedClient $client,
        SuppressedClientRepository $clientRepository
    ): Response {

        if ($this->isCsrfTokenValid('delete'.$client->getEmail(), $request->request->get('_token'))) {
            $clientRepository->remove($client, true);
        }

        return $this->redirectToRoute('app_suppressed');
    }
}
