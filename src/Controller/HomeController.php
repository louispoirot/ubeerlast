<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class HomeController extends AbstractController
{
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $response = $this->httpClient->request(
            'GET',
            'https://ubeer-production.up.railway.app/beers'
        );

        $beers = $response->toArray();

        return $this->render('home/index.html.twig', [
            'beers' => $beers
        ]);
    }
}
