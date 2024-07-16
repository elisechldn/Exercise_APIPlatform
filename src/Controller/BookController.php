<?php

namespace App\Controller;
use App\Entity\Book;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;


class BookController extends AbstractController
{
    #[Route('/books', name: 'book', methods: ['GET'])]
    public function getBookList(BookRepository $bookRepository, SerializerInterface $serializer): JsonResponse
    {
        $bookList = $bookRepository->findAll();
        $jsonBookList = $serializer->serialize($bookList, 'json');

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
            $jsonBook = $serializer->serialize($book,'json');
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
        $jsonBook = $serializer->serialize($book, 'json');
        //Headers needed to be included in the response are in json. True = already serialized in Json.
        return new JsonResponse($jsonBook, Response::HTTP_OK, ['accept' => 'json'], true);
    }
}
