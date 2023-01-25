<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('')]
class ProduitController extends AbstractController
{

    #[Route('', name: 'app_produit_index', methods: ['GET','POST'])]
        public function index(ProduitRepository $produitRepository, Request $request): Response
        {
            //? 2) Afficher le formulaire 
            return $this->render('produit/index.html.twig', [
                'produits' => $produitRepository->findAll(),
            ]);
        }


    #[Route('/api/produit', name: 'app_api_produit_index', methods: ['POST'])]
        public function jsonProduit(ProduitRepository $produitRepository, Request $request): JsonResponse
        {
            $query = $request->request->get("search");
            
            $resultatId = $produitRepository->chercheProduit($query);

            $jsonContent = json_encode($resultatId);

            return new JsonResponse( $jsonContent, JsonResponse::HTTP_OK, [], true) ;        
        
        }



















    #[Route('/produit/new', name: 'app_produit_new', methods: ['GET', 'POST'])]
        public function new(Request $request, ProduitRepository $produitRepository): Response
        {
            $produit = new Produit();
            $form = $this->createForm(ProduitType::class, $produit);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $produitRepository->save($produit, true);

                return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('produit/new.html.twig', [
                'produit' => $produit,
                'form' => $form,
            ]);
        }

    #[Route('/produit/{id}', name: 'app_produit_show', methods: ['GET'])]
        public function show(Produit $produit): Response
        {
            return $this->render('produit/show.html.twig', [
                'produit' => $produit,
            ]);
        }

    #[Route('/produit/{id}/edit', name: 'app_produit_edit', methods: ['GET', 'POST'])]
        public function edit(Request $request, Produit $produit, ProduitRepository $produitRepository): Response
        {
            $form = $this->createForm(ProduitType::class, $produit);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $produitRepository->save($produit, true);

                return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('produit/edit.html.twig', [
                'produit' => $produit,
                'form' => $form,
            ]);
        }

    #[Route('/produit/{id}', name: 'app_produit_delete', methods: ['POST'])]
        public function delete(Request $request, Produit $produit, ProduitRepository $produitRepository): Response
        {
            if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token'))) {
                $produitRepository->remove($produit, true);
            }

            return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
        }
}
