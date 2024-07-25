<?php

namespace App\Controller\Admin;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;

#[Route('/admin/recette', name: 'admin_recipe_')]
class RecipeController extends AbstractController
{
    #
    #[Route('/', name: 'index')]
    public function index( RecipeRepository $recetteRepository): Response
    {
        return $this->render('admin/recipe/index.html.twig', [
            'recipe' => $recetteRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $recette = new Recipe();
        $form = $this->createForm(RecipeType::class, $recette);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slugger = new AsciiSlugger();
            $recette->setSlug($slugger->slug($recette->getName()));
            $entityManager->persist($recette);
            $entityManager->flush();
            $this->addFlash('succes', 'Une nouvelle recette a été créer');
            return $this->redirectToRoute('admin_recipe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/recipe/new.html.twig', [
            'recipe' => $recette,
            'form' => $form
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Recipe $recette): Response
    {
        return $this->render('admin/recipe/show.html.twig', [
            'recipe' => $recette,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Recipe $recette, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RecipeType::class, $recette);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute('admin_recipe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/recipe/edit.html.twig', [
            'recipe' => $recette,
            'form' => $form,
        ]);
    }
    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Recipe $recette, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $recette->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($recette);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_recipe_index', [], Response::HTTP_SEE_OTHER);
    }

}

