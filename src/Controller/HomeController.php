<?php

namespace App\Controller;

use App\Form\ClientType;
use App\Repository\ClientRepository;
use App\Repository\CommandeRepository;
use App\Repository\FavorieRepository;
use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home(Request $request, ClientRepository $clientRepository, ProduitRepository $produitRepository, FavorieRepository $favorieRepository, CommandeRepository $commandeRepository): Response
    {
        $produits = $produitRepository->findAll();
        $form = $this->createForm(ClientType::class);
        $form->handleRequest($request);
        if($request->isMethod('post')) {
            $this->login($request, $clientRepository);
        }
        if(!empty($request->getSession()->get('client'))) {
            $idClient = $request->getSession()->get('client')->getId();
            // on recupere les favoris du client
            $favorisClient = $favorieRepository->findOneBy(['client' => $idClient]);
            $favoris = $favorisClient->getFavories();
            // on recupere la cmmande du client pour afficher le panier
            $commandeClient = $commandeRepository->findOneBy(['client' => $idClient]);
            if(!empty($commandeClient)) {
                $panierClient = [];
                foreach($commandeClient->getProduit() as $produit => $valeur) {
                    $id_produit = $valeur['id_produit'];
                    $produit = $produitRepository->find($id_produit);
                    array_push($panierClient , $produit);
                }
            }
        }
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
            'form' => $form->createView(),
            'label' => 'Se connecter',
            'produits' => $produits,
            'favoris' => @$favoris,
            'panierClient' => @$panierClient
        ]);
    }

    private function login(Request $request, ClientRepository $clientRepository) {
        $email = $request->get('client')['email'];
        $password = $request->get('client')['password'];
        $client = $clientRepository->findOneBy(['email' => $email, 'password' => $password]);
        if(!empty($client)) {
            $session = new Session();
            $session->set('client', $client);
            return $this->redirectToRoute('home');
        } else {
            var_dump('resultat : ' . $client);
        }
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout() {
        $session = new Session();
        $session->clear();
        return $this->redirectToRoute('home');
    }

}
