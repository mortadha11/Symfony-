<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;

final class AuthorController extends AbstractController
{
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }

    #[Route(path: '/authorshow/{name}', name: 'app_authorshow')]
    public function showAuthor($name): Response
    {
        return $this->render('author/showauthor.html.twig', [
            'name' => $name,
        ]);
    }

    #[Route('/authors/list', name: 'app_authors_list')]
    public function listAuthors(): Response
    {
        $authors = [
            ['id' => 1, 'picture' => '/images/Victor-Hugo.jpg', 'username' => 'Victor Hugo', 'email' => 'victor.hugo@gmail.com', 'nb_books' => 100],
            ['id' => 2, 'picture' => '/images/william-shakespeare.jpg', 'username' => 'William Shakespeare', 'email' => 'william.shakespeare@gmail.com', 'nb_books' => 200],
            ['id' => 3, 'picture' => '/images/Taha-Hussein.jpg', 'username' => 'Taha Hussein', 'email' => 'taha.hussein@gmail.com', 'nb_books' => 300],
        ];

        return $this->render('author/list.html.twig', [
            'authors' => $authors,
        ]);
    }

    #[Route('/author/details/{id}', name: 'app_author_details')]
    public function authorDetails($id): Response
    {
        $authors = [
            ['id' => 1, 'picture' => '/images/Victor-Hugo.jpg', 'username' => 'Victor Hugo', 'email' => 'victor.hugo@gmail.com', 'nb_books' => 100],
            ['id' => 2, 'picture' => '/images/william-shakespeare.jpg', 'username' => 'William Shakespeare', 'email' => 'william.shakespeare@gmail.com', 'nb_books' => 200],
            ['id' => 3, 'picture' => '/images/Taha_Hussein.jpg', 'username' => 'Taha Hussein', 'email' => 'taha.hussein@gmail.com', 'nb_books' => 300],
        ];

        $author = null;
        foreach ($authors as $a) {
            if ($a['id'] == $id) {
                $author = $a;
                break;
            }
        }

        if (!$author) {
            throw $this->createNotFoundException('Auteur non trouvé');
        }

        return $this->render('author/showAuthor.html.twig', [
            'author' => $author,
        ]);
    }

    #[Route('/authors/doctrine', name: 'app_authors_doctrine')]
    public function listAuthorsDoctrine(EntityManagerInterface $entityManager): Response
    {
        $authors = $entityManager->getRepository(Author::class)->findAll();

        return $this->render('author/list_doctrine.html.twig', [
            'authors' => $authors,
        ]);
    }

    #[Route('/author/addStatic', name: 'app_author_add_static')]
    public function addStatic(EntityManagerInterface $entityManager): Response
    {
        $author = new Author();
        $author->setUsername('New Author');
        $author->setEmail('new.author@gmail.com');
        $author->setPicture('/images/default.jpg');
        $author->setNbBooks(0);

        $entityManager->persist($author);
        $entityManager->flush();

        return new Response('Auteur ajouté avec succès !');
    }

    #[Route('/author/add', name: 'app_author_add')]
    public function add(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $author = new Author();
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('picture')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // gérer l'erreur si besoin
                }

                $author->setPicture('/images/'.$newFilename);
            } else {
                $author->setPicture('/images/default.jpg');
            }

            $entityManager->persist($author);
            $entityManager->flush();

            return $this->redirectToRoute('app_authors_doctrine');
        }

        return $this->render('author/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/author/edit/{id}', name: 'app_author_edit')]
    public function edit($id, Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $author = $entityManager->getRepository(Author::class)->find($id);
        if (!$author) throw $this->createNotFoundException('Auteur non trouvé');

        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('picture')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    
                }

                $author->setPicture('/images/'.$newFilename);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_authors_doctrine');
        }

      return $this->render('author/edit.html.twig', [
    'form' => $form->createView(),
    'author' => $author, 
]);

    }

    #[Route('/author/delete/{id}', name: 'app_author_delete')]
    public function delete($id, EntityManagerInterface $entityManager): Response
    {
        $author = $entityManager->getRepository(Author::class)->find($id);
        if (!$author) throw $this->createNotFoundException('Auteur non trouvé');

        $entityManager->remove($author);
        $entityManager->flush();

        return $this->redirectToRoute('app_authors_doctrine');
    }
}
