<?php

namespace App\Controller;

use App\Entity\Contact;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface as SerializationSerializerInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class ContactController extends AbstractController
{
    /**
     * @Route("/contact/addContact", name="app_contact", methods={"post"})
     */
    public function addContact(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator): Response
    {
        $jsonRecu = $request->getContent();


        try{

            $contact = $serializer->deserialize($jsonRecu, Contact::class, "json");

            $errors = $validator->validate($contact);
            if(count($errors) != 0){
                return $this->json($errors, 400);
            }
            $em->persist($contact);
            $em->flush();
            return $this->json($contact, 201, []);
    
        }catch (NotEncodableValueException $exp){
            return $this->json([
                'status' => 400,
                'message' => $exp->getMessage()
            ], 400);
         }
       
    }
}
