<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Contact;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;



/**
 * @Route("/api", name="api_")
 */
class ContactController extends AbstractController
{


    /**
     * @Route("/contact", name="contact_index", methods={"GET"})
     */
    public function index(): Response
    {
        $contacts = $this->getDoctrine()
            ->getRepository(Contact::class)
            ->findAll();

        $data = [];

        foreach ($contacts as $contact){
            $data[] = [
                'id' => $contact->getId(),
                'nom' => $contact->getNom(),
                'prenom' => $contact->getPrenom(),
                'email' => $contact->getEmail(),
                'adresse' => $contact->getAdresse(),
                'telephone' => $contact->getTelephone(),
                'age' => $contact->getAge(),
                'activite' => $contact->getActivite(),
            ];
        }

        return $this->json($data);
    }

    function validating($telephone){
        
        
    }

    /**
     * @Route("/contact", name="contact_new", methods={"POST"})
     */
    public function create(Request $request): Response{
        $entityManager = $this->getDoctrine()->getManager();
        
        $contact = new Contact();
        $contact->setNom($request->request->get('nom'));
        $contact->setPrenom($request->request->get('prenom'));
        $contact->setEmail($request->request->get('email'));
        $contact->setAdresse($request->request->get('adresse'));
        $contact->setTelephone($request->request->get('telephone'));
        $contact->setAge($request->request->get('age'));
        $contact->setActivite($request->request->get('activite'));
        
        
        //$contact = $entityManager->getRepository(Contact::class)->findBy($contact->getAge());

        if($contact->getAge() < 18){
            return $this->json('The contact must be of legal age to be added' , 404);
        }

        $telephone = $contact->getTelephone();

        if(preg_match('/^[0-9]{10}+$/', $telephone)) {
        } 
        else {
                return $this->json('Please enter a correct phone number' , 404);
        } 
        
        $entityManager->persist($contact);
        $entityManager->flush();

        return $this->json('Created new contact successfully with id ' . $contact->getId());

    }


    /**
     * @Route("/contact/{id}", name="contact_show", methods={"GET"})
     */
    public function show(int $id): Response{
        $contact = $this->getDoctrine()
            ->getRepository(Contact::class)
            ->find($id);

        if (!$contact){
            return $this->json('No contact found for id ' . $id, 404);
        }

        $data = [
            'id' => $contact->getId(),
            'nom' => $contact->getNom(),
            'prenom' => $contact->getPrenom(),
            'email' => $contact->getEmail(),
            'adresse' => $contact->getAdresse(),
            'telephone' => $contact->getTelephone(),
            'age' => $contact->getAge(),
            'activite' => $contact->getActivite(),
        ];

        return $this->json($data);
        
    }

    /**
     * @Route("/contact/{id}", name="contact_edit", methods={"PUT"})
     */
    public function edit(Request $request, int $id): Response {
        $entityManager = $this->getDoctrine()->getManager();
        $contact = $entityManager->getRepository(Contact::class)->find($id);

        if (!$contact){
            return $this->json('No contact found for id ' . $id, 404);
        }

        $contact->setNom($request->request->get('nom'));
        $contact->setPrenom($request->request->get('prenom'));
        $contact->setEmail($request->request->get('email'));
        $contact->setAdresse($request->request->get('adresse'));
        $contact->setTelephone($request->request->get('telephone'));
        $contact->setAge($request->request->get('age'));
        $contact->setActivite($request->request->get('activite'));

        if($contact->getAge() < 18){
            return $this->json('The contact must be of legal age to be added or edited' , 404);
        }

        $entityManager->flush();

        $data = [
            'id' => $contact->getId(),
            'nom' => $contact->getNom(),
            'prenom' => $contact->getPrenom(),
            'email' => $contact->getEmail(),
            'adresse' => $contact->getAdresse(),
            'telephone' => $contact->getTelephone(),
            'age' => $contact->getAge(),
            'activite' => $contact->getActivite(),

        ];

        return $this->json($data);
    }

    /**
     * @Route("/contact/{id}", name="contact_delete", methods={"DELETE"})
     */
    public function delete(int $id): Response{
        $entityManager = $this->getDoctrine()->getManager();
        $contact= $entityManager->getRepository(Contact::class)->find($id);

        if (!$contact){
            return $this->json('No contact found for id ' . $id, 404);
        }

        $entityManager->remove($contact);
        $entityManager->flush();

        return $this->json('The contact ' . $id . ' was successfully deleted ' );
    }
}
