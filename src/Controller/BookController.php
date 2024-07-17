<?php

namespace App\Controller;
use App\Entity\Book;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class BookController extends AbstractController
{
    #[Route('/books', name: 'book', methods: ['GET'])]
    public function getBookList(BookRepository $bookRepository, SerializerInterface $serializer): JsonResponse
    {
        $bookList = $bookRepository->findAll();
        $jsonBookList = $serializer->serialize($bookList, 'json', ['groups' => 'getBooks']);

        /*Serialized datas, code status by default, [empty headers], true because already serialized. 
        Default value is false, so careful.*/
        return new jsonResponse($jsonBookList, Response::HTTP_OK, [], true);
    }

    /*#[Route('/books/{id}', name: 'detailBook', methods: ['GET'])]
    public function getDetailBook(int $id, SerializerInterface $serializer, BookRepository $bookRepository):JsonResponse
    {
        //Looking for a particular book
        $book = $bookRepository->find($id);
        //If it exists, the object will be serialized inside a json string. 
        if ($book) {
            $jsonBook = $serializer->serialize($book,'json', ['groups' => 'getBooks']);
            return new JsonResponse($jsonBook, Response::HTTP_OK, [], true);
        }
        //If it does not exist, the 404 not found will be sent
        return new JsonResponse(null, RESPONSE::HTTP_NOT_FOUND);
    }*/

    //Same as before but while using ParamConverter
    #[Route('/books/{id}', name: 'detailBook', methods: ['GET'])]
    public function getDetailBook(Book $book, SerializerInterface $serializer): JsonResponse
    {
        //ParamConverter = look for the corresponding id inside the entity book and is returned as a Json string.
        $jsonBook = $serializer->serialize($book, 'json', ['groups' => 'getBooks']);
        //Headers needed to be included in the response are in json. True = already serialized in Json.
        return new JsonResponse($jsonBook, Response::HTTP_OK, ['accept' => 'json'], true);
    }

    #[Route('/books', name: 'createBook', methods: ['POST'])]
    public function createBook(Request $request, SerializerInterface $serializer, 
    EntityManager $em, UrlGenerator $urlGenerator, AuthorRepository $authorRepository): JsonResponse
    {
        $book = $serializer->deserialize($request->getContent(), Book::class, 'json');

        //We put all the collected data inside an array
        $content = $request->toArray();

        //Collecting author_id if existing. If not, -1
        $idAuthor = $content['idAuthor'] ?? -1;

        /*We add the corresponding author in the DB and we are assigning it to the good book.
        If the method does not find any author, null is returned*/
        $book->setAuthor($authorRepository->find($idAuthor));
        $em->persist($book);
        $em->flush();

        $jsonBook = $serializer->serialize($book, 'json', ['groups' => 'getBooks']);
        $location = $urlGenerator->generate('detailBook', ['id' => $book->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonBook, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('books/{id}', name: 'updateBook', methods:['PUT'])]
    public function updateBook(Request $request, SerializerInterface $serializer, 
    Book $currentBook, AuthorRepository $authorRepository, EntityManager $em):JsonResponse
    {
        //We turn the data into an object in order to get the content and to add a few more infos  
        $updatedBook = $serializer->deserialize($request->getContent(),
            Book::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $currentBook]);
        $content = $request->toArray();
        $idAuthor = $content['idAuthor'] ?? -1;
        $updatedBook->setAuthor($authorRepository->find($idAuthor));
        
        $em->persist($updatedBook);
        $em->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    #[Route('/books/{id}', name:'deleteBook', methods: ['DELETE'])]
    public function deleteBook(Book $book, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($book);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
