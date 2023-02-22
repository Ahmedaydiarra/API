<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface as SerializationSerializerInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    /**
     * @Route("/api/listpost", name="app_home_list", methods={"get"})
     */

     public function listPost(PostRepository $postRepository,SerializerInterface $serializer) : Response
     {
        $posts = $postRepository->findAll(); // recupere tout les post

        return $this->json($posts, 200, []);
     }

    /**
     * @Route("/api/addPost", name="app_home_add", methods={"post"})
     */
     public function addPost(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator) : Response
     {
        $jsonRecu = $request->getContent();


        try{

            $post = $serializer->deserialize($jsonRecu, Post::class, "json");

            $errors = $validator->validate($post);
            if(count($errors) != 0){
                return $this->json($errors, 400);
            }
            $em->persist($post);
            $em->flush();
            return $this->json($post, 201, []);
    
        }catch (NotEncodableValueException $exp){
            return $this->json([
                'status' => 400,
                'message' => $exp->getMessage()
            ], 400);
         }
       
     }



}
