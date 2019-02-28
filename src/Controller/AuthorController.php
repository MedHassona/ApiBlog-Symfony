<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AuthorRepository;


class AuthorController extends AbstractController
{

    /**
     * @Route("/author", name="author")
     */
    public function index()
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/AuthorController.php',
        ]);
    }

    /**
     * @Route("/authors/{id}", name="author_show")
     */
    public function show($id)
    {    
        $author = $this->getDoctrine()
                        ->getRepository(Author::class)
                        ->find($id);
        $data = $this->serializeAuthor($author);

        return new JsonResponse($data);        
    }

    /**
     * @Route("/authors", name="authors_list", methods="GET")
     */
    public function listAction(AuthorRepository $authorRepository){
        $authors = $authorRepository->findAll();

        $data = ['authors' => []];
        foreach($authors as $author){
            $data['authors'][] = $this->serializeAuthor($author);
        }

        return new JsonResponse($data);
    }

    /**
	 * @Route("/authors", methods="POST")
	 */
    public function newAction(Request $request)
    {
        $body = $request->getContent();
        $data = json_decode($body, true); 

        $author = new Author();
        $form = $this->createForm(AuthorType::class, $author);
        $form->submit($data);
        if (false === $form->isValid()) {
            return new JsonResponse(
                [
                    'status' => 'error',
                    'errors' => $this->formErrorSerializer->convertFormToArray($form),
                ],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($author);
        $em->flush();

        return $this->json([
            'message' => 'registered very well!',
        ]);
    }

    /**
	 * @Route("/authors/author/{id}", name="delete_action", methods="DELETE")
	 */
    public function deleteAction($id){
        $author = $this->getDoctrine()
                        ->getRepository(Author::class)
                        ->find($id);

        $data = $this->serializeAuthor($author);

        $em = $this->getDoctrine()->getManager();
        $em->remove($author);
        $em->flush();

        return $this->json([
            'message' => 'deleted very well!',
        ]);
    }

    private function serializeAuthor(Author $author){
        return [
            'id' => $author->getId(),
            'fullname' => $author->getFullname(),
            'biography' => $author->getBiography(),
        ];
    }
}
