<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Author;
use App\Form\BookType;
use App\Repository\BookRepository;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/book')]
class BookController extends AbstractController
{
   
    #[Route('/add', name: 'add_book')]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           // $book->setPublished(true); // Par défaut publié

            
            $author = $book->getAuthor();
            if ($author) {
                $author->setNbBooks($author->getNbBooks() + 1);
            }

            $em->persist($book);
            $em->flush();

            $this->addFlash('success', 'Livre ajouté avec succès ✅');
            return $this->redirectToRoute('list_books');
        }

        return $this->render('book/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/list', name: 'list_books')]
    public function list(BookRepository $bookRepo): Response
    {
        $books = $bookRepo->findAll();

        $publishedCount = $bookRepo->count(['published' => true]);
        $unpublishedCount = $bookRepo->count(['published' => false]);

        return $this->render('book/list.html.twig', [
            'books' => $books,
            'publishedCount' => $publishedCount,
            'unpublishedCount' => $unpublishedCount,
        ]);
    }


    #[Route('/edit/{id}', name: 'edit_book')]
    public function edit(Book $book, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Livre modifié avec succès ✏️');
            return $this->redirectToRoute('list_books');
        }

        return $this->render('book/edit.html.twig', [
            'form' => $form->createView(),
            'book' => $book,
        ]);
    }

    
    #[Route('/delete/{id}', name: 'delete_book')]
    public function delete(Book $book, EntityManagerInterface $em): Response
    {
        $author = $book->getAuthor();

        
        if ($author) {
            $author->setNbBooks(max(0, $author->getNbBooks() - 1));
        }

        $em->remove($book);
        $em->flush();

        $this->addFlash('success', 'Livre supprimé 🗑️');
        return $this->redirectToRoute('list_books');
    }

    
    #[Route('/cleanup/authors', name: 'cleanup_authors')]
    public function cleanupAuthors(EntityManagerInterface $em, AuthorRepository $authorRepo): Response
    {
        $authors = $authorRepo->findBy(['nbBooks' => 0]);
        foreach ($authors as $author) {
            $em->remove($author);
        }

        $em->flush();
        $this->addFlash('info', 'Auteurs sans livres supprimés.');

        return $this->redirectToRoute('list_books');
    }

   
    #[Route('/show/{id}', name: 'show_book')]
    public function show(Book $book): Response
    {
        return $this->render('book/show.html.twig', [
            'book' => $book,
        ]);
    }
}
