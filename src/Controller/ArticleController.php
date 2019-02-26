<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
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
                        ->getRepository(Article::class)
                        ->find($id);
        if($article){
                $data = $this->serializeArticle($article);
                $response =  new Response(json_encode($data));
                $response->headers->set('Content-Type', 'application/json');
                return new JsonResponse($data);  
        }else{
            return  $this->json([
                'message' => 'article not found',
            ]);
        }
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

    /**
	 * @Route("/articles", name="post_post", methods="POST")
	 */
    public function newAction(Request $request)
    {
        $body = $request->getContent();
        $data = json_decode($body, true);

        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->submit($data);

        $em = $this->getDoctrine()->getManager();
        $em->persist($article);
        $em->flush();

        return  $this->json([
            'message' => 'enrigestered very well!',
        ]);

    }

    /**
	 * @Route("/articles/{id}/{lv}", name="put_post", methods="PUT")
	 */
    public function updateAction(Request $request, $id, $lv){

        $article = $this->getDoctrine()
                        ->getRepository(Article::class)
                         ->find($id);

        if($article){
            $body = $request->getContent();
            $data = json_decode($body, true);
    
            if($data){
                $newArticle = new Article();
                $form = $this->createForm(ArticleType::class, $newArticle);
                $form->submit($data);


                $article->setTitle($newArticle->getTitle());
                $article->setContent($newArticle->getContent());
                $article->setLoveIts($newArticle->getLoveIts());

                
            }
            else{
                $article->setLoveIts($lv);
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();
    
            return  $this->json([
                'message' => 'updated very well',
            ]);
        }
        else{
                 return new Response(json_encode([ 'message' => 'article not found',]), 404);
        }

    }

    /**
	 * @Route("/articles/article/{id}", name="delete_post", methods="DELETE")
	 */
    public function deleteAction(Request $req, $id){
        $article = $this->getDoctrine()
                        ->getRepository(Article::class)
                        ->find($id);

        if(!$article){
            return new Response(json_encode([ 'message' => 'article not found',]), 404);
         }

        $em = $this->getDoctrine()->getManager();
        $em->remove($article);
        $em->flush();

        return  $this->json(['message' => 'deleted very well!',]);     
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
