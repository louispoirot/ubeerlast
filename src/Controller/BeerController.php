<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Form\BeerType;

class BeerController extends AbstractController
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

    #[Route('/beer/{id}/show', name: 'app_beer_show')]
    public function show(int $id): Response
    {
        $response = $this->httpClient->request(
            'GET',
            'https://ubeer-production.up.railway.app/beers/' . $id
        );

        $beer = $response->toArray();

        return $this->render('beer/show.html.twig', [
            'beer' => $beer
        ]);
    }

    #[Route('/beer/{id}/details', name: 'app_beer_details')]
    public function details(int $id): Response
    {
        $response = $this->httpClient->request(
            'GET',
            'https://ubeer-production.up.railway.app/beers/' . $id
        );

        $beer = $response->toArray();

        return $this->render('beer/details.html.twig', [
            'beer' => $beer
        ]);
    }

    #[Route('/beer/{id}/edit', name: 'app_beer_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        // Récupérer la bière
        $response = $this->httpClient->request(
            'GET',
            'https://ubeer-production.up.railway.app/beers/' . $id
        );

        $beer = $response->toArray();

        // Si c'est une requête POST, traiter la modification
        if ($request->isMethod('POST')) {
            $updatedData = [
                'beer' => $request->request->get('beer'),
                'price' => (float) $request->request->get('price'),
                'brewery_id' => (int) $request->request->get('brewery_id'),
                'imageUrl' => $request->request->get('imageUrl')
            ];

            try {
                $response = $this->httpClient->request(
                    'PATCH',
                    'https://ubeer-production.up.railway.app/beers/' . $id,
                    [
                        'json' => $updatedData,
                        'headers' => [
                            'Content-Type' => 'application/merge-patch+json'
                        ]
                    ]
                );

                if ($response->getStatusCode() === 200) {
                    $this->addFlash('success', 'La bière a été mise à jour avec succès.');
                    return $this->redirectToRoute('app_beer_show', ['id' => $id]);
                }
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la mise à jour : ' . $e->getMessage());
            }
        }

        return $this->render('beer/edit.html.twig', [
            'beer' => $beer
        ]);
    }

    #[Route('/beer/new', name: 'app_beer_new')]
    public function new(Request $request): Response
    {
        $form = $this->createForm(BeerType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newBeer = $form->getData();
            
            // Ajouter via l'API
            $response = $this->httpClient->request(
                'POST',
                'https://ubeer-production.up.railway.app/beers',
                ['json' => $newBeer]
            );

            if ($response->getStatusCode() === 201) {
                $this->addFlash('success', 'La bière a été ajoutée avec succès.');
                return $this->redirectToRoute('app_home');
            }
        }

        return $this->render('beer/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/beer/{id}/delete', name: 'app_beer_delete', methods: ['POST'])]
    public function delete(int $id): Response
    {
        // Supprimer via l'API
        $response = $this->httpClient->request(
            'DELETE',
            'https://ubeer-production.up.railway.app/beers/' . $id
        );

        if ($response->getStatusCode() === 204) {
            $this->addFlash('success', 'La bière a été supprimée avec succès.');
        } else {
            $this->addFlash('error', 'Une erreur est survenue lors de la suppression.');
        }

        return $this->redirectToRoute('app_home');
    }
}
