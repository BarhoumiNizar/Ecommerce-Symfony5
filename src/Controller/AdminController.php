<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

    /**
     * @Route("/admin")
     */

class AdminController extends AbstractController
{
    /**
     * @Route("/home", name="adminHome")
     */
    public function index(ProduitRepository $produitRepository): Response
    {
        $produits = $produitRepository->findAll();
        return $this->render('admin/produit/affichage.html.twig', [
            'produits' => $produits
        ]);
    }

    /**
     * @Route("/", name="loginAdmin")
     */

    public function login ()
    {
        return $this->render('admin/login.html.twig');
    }

}
