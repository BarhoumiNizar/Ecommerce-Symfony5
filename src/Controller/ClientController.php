<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientType;
use App\Repository\ClientRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;

#[Route('/client')]

class ClientController extends AbstractController
{
    #[Route('/inscription', name: 'inscriptionClient')]

    public function index(PersistenceManagerRegistry $managerRegistry, Request $request): Response
    {
        $client = new Client();
        $label = 'S\'enregistrer';
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $image = $form->get('photo')->getData();
            $photoName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $image->guessExtension();
            $photo = $photoName . '.' . $extension;
            $image->move($this->getParameter('UploadsImgClient'), $photo);
            $cnx = $managerRegistry->getManager();
            $client->setPhoto($photo);
            $cnx->persist($client);
            $cnx->flush();
            return $this->redirectToRoute('home');
        }
        return $this->render('client/inscription.html.twig', [
            'controller_name' => 'ClientController',
            'form' => $form->createView(),
            'label' => $label
        ]);
    }
}
