<?php

namespace App\Controller;

use App\Entity\Favorie;
use App\Repository\ClientRepository;
use App\Repository\FavorieRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;

#[Route('/favori')]

class FavoriController extends AbstractController
{
    #[Route('/affichage', name: 'affichageFavori')]
    public function index(): Response
    {
        return $this->render('favori/index.html.twig', [
            'controller_name' => 'FavoriController',
        ]);
    }

    #[Route('/gestion/{id}', name: 'gestionFavori')]

    public function gestionFavori(PersistenceManagerRegistry $managerRegistry, $id, Request $request, FavorieRepository $favorieRepository, ClientRepository $clientRepository)
    {
        $cnx = $managerRegistry->getManager();

        $idClient = $request->getSession()->get('client')->getId();

        $favorisClient = $favorieRepository->findOneBy(['client' => $idClient]);

        $favoris = $favorisClient->getFavories();
        
        if(in_array($id, $favoris)) {
            unset($favoris[array_search($id, $favoris)]);
        } else {
            array_push($favoris, $id);
        };
        if(!empty($favorisClient)) {
            $favorisClient->setFavories($favoris);
            $cnx->persist($favorisClient);
        } else {
            $favori = new Favorie();
            $client = $clientRepository->find($idClient);
            $favori->setClient($client);
            $favori->setFavories($favoris);
            $cnx->persist($favori);
        }
        $cnx->flush();

        return $this->redirectToRoute('home');
    }
}
