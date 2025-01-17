<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TestBeerController extends AbstractController
{
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    #[Route('/beer/{id}/edit', name: 'app_beer_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        // Récupérer la bière actuelle
        $response = $this->httpClient->request(
            'GET',
            'https://ubeer-production.up.railway.app/beers/' . $id
        );

        $beer = $response->toArray();
        
        // Créer le formulaire
        $form = $this->createForm(BeerType::class, $beer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            
            // Préparer les données modifiées
            $updatedData = [];
            
            // Vérifier chaque champ et ne l'ajouter que s'il est différent
            if ($formData['price'] != $beer['price']) {
                $updatedData['price'] = (float) $formData['price'];
            }
            if ($formData['beer'] !== $beer['beer']) {
                $updatedData['beer'] = $formData['beer'];
            }
            if ($formData['brewery_id'] != $beer['brewery_id']) {
                $updatedData['brewery_id'] = (int) $formData['brewery_id'];
            }
            if ($formData['imageUrl'] !== $beer['imageUrl']) {
                $updatedData['imageUrl'] = $formData['imageUrl'];
            }
            
            // Ne faire la requête que s'il y a des modifications
            if (!empty($updatedData)) {
                try {
                    dump('Envoi à l\'API:', $updatedData); // Debug
                    
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
                    
                    dump('Réponse de l\'API:', $response->getContent()); // Debug
                    
                    if ($response->getStatusCode() === 200) {
                        $this->addFlash('success', 'La bière a été mise à jour avec succès.');
                    } else {
                        $this->addFlash('error', 'Erreur lors de la mise à jour. Code: ' . $response->getStatusCode());
                    }
                } catch (\Exception $e) {
                    dump('Erreur:', $e->getMessage()); // Debug
                    $this->addFlash('error', 'Erreur lors de la mise à jour : ' . $e->getMessage());
                }
            }
            
            return $this->redirectToRoute('app_beer_show', ['id' => $id]);
        }

        return $this->render('beer/edit.html.twig', [
            'beer' => $beer,
            'form' => $form->createView()
        ]);
    }
}
