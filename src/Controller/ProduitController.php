<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/produit')]

class ProduitController extends AbstractController
{

    #[Route('/ajout/{id}', name: 'ajoutProduit')]
    
    public function ajoutProduit(PersistenceManagerRegistry $managerRegistry, Request $request, $id, ProduitRepository $produitRepository): Response
    {
        if($id == 0) {
            $produit = new Produit();
            $label = 'Ajouter un produit';
            $images = [];
        } else {
            $produit = $produitRepository->find($id);
            $label = 'Modifier le produit';
            $images = $produit->getPhotos();
        }
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $files = $form->get('photos')->getData();
            foreach ($files as $file) {
                $photoName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->guessExtension();
                $photo = $photoName . '.' . $extension;
                array_push($images, $photo);
                $file->move($this->getParameter('UploadsImgProduit'), $photo);
            }
            $cnx = $managerRegistry->getManager();
            $produit->setPhotos($images);
            $cnx->persist($produit);
            $cnx->flush();
            return $this->redirectToRoute('adminHome');
        }
        return $this->render('admin/produit/ajout.html.twig', [
            'form' => $form->createView(),
            'label' => $label
        ]);
    }

    #[Route('/supprimer/{id}', name: 'supprimerProduit')]
    public function supprimerProduit(PersistenceManagerRegistry $managerRegistry, ProduitRepository $produitRepository, $id): Response
    {
        $produit = $produitRepository->find($id);
        $cnx = $managerRegistry->getManager();
        $cnx->remove($produit);
        $cnx->flush();
        return $this->redirectToRoute('adminHome');
    }
}
