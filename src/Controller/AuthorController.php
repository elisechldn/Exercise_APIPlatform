<?php

namespace App\Controller;

use App\Entity\Author;
use App\Repository\AuthorRepository;
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

class AuthorController extends AbstractController
{
    #[Route('/authors', name: 'detailAuthor', methods: ['GET'])]
    public function getAuthorList(AuthorRepository $authorRepository,SerializerInterface $serializer): JsonResponse
    {
        $authorList = $authorRepository->findAll();
        $jsonAuthorList = $serializer->serialize($authorList, 'json');
        /*Serialized datas, code status by default, [empty headers], true because already serialized. 
        Default value is false, so careful.*/
        return new JsonResponse($jsonAuthorList, Response::HTTP_OK, ['accept' => 'json'], true);
    }

    #[Route('/authors', name: 'createAuthor', methods: ['POST'])]
    public function addAuthor(Request $request, SerializerInterface $serializer, 
    EntityManager $em, UrlGenerator $urlGenerator, Author $author): JsonResponse
    {
        $author = $serializer->deserialize($request->getContent(), Author::class, 'json', [], true);
        $em->persist($author);
        $em->flush();

        $location = $urlGenerator->generate('detailAuthor', ['id' => $author->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($author, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/authors/{id}', name: 'updateAuthor', methods: ['PUT'])]
    public function updateAuthor(Author $currentAuthor, Request $request, SerializerInterface $serializer, EntityManager $em):JsonResponse
    {
        $updatedAuthor = $serializer->deserialize($request->getContent(),
            Author::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $currentAuthor]);

            $em->persist($updatedAuthor);
            $em->flush();
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    #[Route('/authors/{id}', name: 'deleteAuthor', methods: ['DELETE'])]
    public function deleteAuthor(Author $author, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($author);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
