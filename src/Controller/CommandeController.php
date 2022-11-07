<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Repository\ClientRepository;
use App\Repository\ProduitRepository;
use App\Repository\CommandeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Symfony\Component\HttpFoundation\Response;

#[Route('/commande')]

class CommandeController extends AbstractController
{
    #[Route('/affichageCommande', name: 'affichageCommande')]
    public function index(Request $request, CommandeRepository $commandeRepository, ProduitRepository $produitRepository)
    {        
        $idClient = $request->getSession()->get('client')->getId();
        $commandeClient = $commandeRepository->findOneBy(['client' => $idClient]);
        $produitsCommande = $commandeClient->getProduit();
        $panierClient = [];
        foreach($produitsCommande as $produit => $valeur) {
            $id_produit = $valeur['id_produit'];
            $qte_produit = $valeur['qte'];
            $produit = $produitRepository->find($id_produit);
            array_push($panierClient , ['produit' => $produit, 'qte' => $qte_produit]);
        }
        return $this->render('commande/affichage-commande.html.twig', [
            'panierClient' => $panierClient,
        ]);
    }

    #[Route('/gestionCommande/{id}', name: 'gestionCommande')]
    public function gestionCommande(PersistenceManagerRegistry $managerRegistry, Request $request, CommandeRepository $commandeRepository, ClientRepository $clientRepository, $id)
    {
        $cnx = $managerRegistry->getManager();

        $idClient = $request->getSession()->get('client')->getId();

        $commandeClient = $commandeRepository->findOneBy(['client' => $idClient]);

        if(!empty($commandeClient)) {
            // on creer un tableau a partir du panier client de la bdd
            $produitsCommande = $commandeClient->getProduit();

            // on boucle poour savoir s'il existe => si oui redirect sinon on ajoute
            foreach($produitsCommande as $produit => $valeur) {
                if(in_array($id, $valeur)) {
                    // alert => ce produt est deja dans votre panier
                    return $this->redirectToRoute('affichageCommande');
                }
            }
            // ajout produit dans le panier
            $quantitesParProduit = ['id_produit' => $id, 'qte' => 1];
            array_push($produitsCommande, $quantitesParProduit);
            $commandeClient->setProduit($produitsCommande);
            $commandeClient->setDateCommande(date('Y-m-d'));
            $cnx->persist($commandeClient);
            $cnx->flush();
            // alert ce produit a été ajouter dans votre panier
            return $this->redirectToRoute('home');

        } else {
            // si le client n'aas pas de panier dans la bdd
            $quantitesParProduit = ['id_produit' => $id, 'qte' => 1];
            $produitsCommande = [];
            array_push($produitsCommande, $quantitesParProduit);
            $commande = new Commande();
            $client = $clientRepository->find($idClient);
            $commande->setClient($client);
            $commande->setProduit($produitsCommande);
            $commande->setDateCommande(date('Y-m-d'));
            $cnx->persist($commande);
            $cnx->flush();
            return $this->redirectToRoute('home');
        }
    }

    #[Route('/supprimerArticleCommande/{id}/{page}', name: 'supprimerArticleCommande')]

    public function supprimerArticleCommande(PersistenceManagerRegistry $managerRegistry, Request $request, CommandeRepository $commandeRepository, $id, $page)
    {

        $cnx = $managerRegistry->getManager();
        $idClient = $request->getSession()->get('client')->getId();
        $commandeClient = $commandeRepository->findOneBy(['client' => $idClient]);
        $produitsCommande = $commandeClient->getProduit();
        foreach($produitsCommande as $produit => $valeur) {
            $id_produit = $valeur['id_produit'];
            if($id_produit == $id) {
                unset($produitsCommande[$produit]);
            }
        }
        $commandeClient->setProduit($produitsCommande);
        $cnx->persist($commandeClient);
        $cnx->flush();
        if($page == 'panier') {
            return $this->redirectToRoute('affichageCommande');
        } else {
            return $this->redirectToRoute('home');
        }
    }

    #[Route('/setQuantiteProduit/{qtes}/{id}', name: 'setQuantiteProduit')]

    public function setQuantiteProduit(Request $request, $qtes, $id) {
        var_dump($_POST['qtes']);
        die();
        echo $qtes . ' ' . $id;
        echo $request;
        return new Response('coucou');
    }
}
