<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ArticleController extends AbstractController
{

    /**
     * @Route("/article", name="article")
     */
    public function index()
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ArticleController.php',
        ]);
    }

    /**
     * @Route("/articles/{id}", name="article_show")
     */
    public function show($id)
    {    
        $article = $this->getDoctrine()
        ->getRepository(article::class)
        ->find($id);
 
        $data = $this->serializeArticle($article);

        $response =  new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');
        return new JsonResponse($data);        
    }

    /**
     * @Route("/articles", name="articles_list", methods="GET")
     */
    public function listAction(ArticleRepository $articleRepository){
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $articles = $articleRepository->findAll();

        //$data = $serializer->serialize($articles, 'json');
        $data = ['posts' => []];
        foreach($articles as $art){
            $data['posts'][] = $this->serializeArticle($art);
        }
        $response =  new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');
        return new JsonResponse($data);

    }

    private function serializeArticle(Article $article){
        return [
            'id' => $article->getId(),
            'title' => $article->getTitle(),
            'content' => $article->getContent(),
            'loveIts' => $article->getLoveIts(),
        ];
    }
}
