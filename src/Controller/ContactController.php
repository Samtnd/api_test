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


     /**
     * @Route("/contact/actif", name="contact_showActif", methods={"GET"})
     */
    public function showActif(): Response{  // Cette méthode va permettre de filtrer tous les contacts actif c.a.d un contact avec la valeur activite à true
        $contacts = $this->getDoctrine()
            ->getRepository(Contact::class)
            ->findAll();

        $data = [];

        foreach ($contacts as $contact){
            if ($contact->getActivite() == true){

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
        }

        return $this->json($data);
    }


     /**
     * @Route("/contact/inactif", name="contact_showInactif", methods={"GET"})
     */
    public function showInactif(): Response{ //Cette méthode va permettre de filtrer tous les contacts actif c.a.d un contact avec la valeur activite à true
        $contacts = $this->getDoctrine()
            ->getRepository(Contact::class)
            ->findAll();

        $data = [];

        foreach ($contacts as $contact){
            if ($contact->getActivite() == false){

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
        }

        return $this->json($data);
    }
   

    /**
     * @Route("/contact", name="contact_new", methods={"POST"})
     */
    public function create(Request $request): Response{ // Cette méthode va permettre de créer un contact en initialisant le son activité à true
        $entityManager = $this->getDoctrine()->getManager();
        
        $contact = new Contact();
        $contact->setNom($request->request->get('nom'));
        $contact->setPrenom($request->request->get('prenom'));
        $contact->setEmail($request->request->get('email'));
        $contact->setAdresse($request->request->get('adresse'));
        $contact->setTelephone($request->request->get('telephone'));
        $contact->setAge($request->request->get('age'));
        $contact->setActivite(true);
        
        
        
        
        // Ici je fais une vérification si le contact ajouté est un majeur
        if($contact->getAge() < 18){
            return $this->json('The contact must be of legal age to be added' , 404);
        }

        $telephone = $contact->getTelephone();

        // Ici je vérifie que le numéro téléphone fait bien 10 chiffres
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
    public function show(int $id): Response{ // Cette méthode affiche tous les contacts 
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
    public function edit(Request $request, int $id): Response { // Cette méthode va permettre de modifier toutes les informations d'un contact unique passé en paramètre
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

        $telephone = $contact->getTelephone();
        $valid_number = filter_var($telephone, FILTER_SANITIZE_NUMBER_INT);
        if(preg_match('/^[0-9]{10}+$/', $telephone)) {
        } 
        else {
                return $this->json('Please enter a correct phone number' , 404);
        } 

        $activate = $contact->getActivite();

        // Ici je fais une condition qui permer de récupérer ce que l'utilisateur entre et changer l'état de l'activité du contact en actif si il était désactivé
        if($activate == 'actif'){
            $contact->setActivite(true);
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
     * @Route("/contact/deactivate/{id}", name="contact_deactivate", methods={"PUT"})
     */
    public function deactivate(Request $request, int $id): Response{ // Cette méthode permet de désactiver un contact donc changer l'état d'un contact en inactif(false)
        $entityManager = $this->getDoctrine()->getManager();
        $contact = $entityManager->getRepository(Contact::class)->find($id);

        if (!$contact){
            return $this->json('No contact found for id ' . $id, 404);
        }

        $contact->setActivite($request->request->get('activite'));


        $activate = $contact->getActivite();

        if($activate == 'inactif'){
           $contact->setActivite(false);
           
        }
        
        $entityManager->flush();
        
        return $this->json('The contact with id ' . $id . ' was successfully deactivated ' );
       
    }
    
    /**
     * @Route("/contact/{id}", name="contact_delete", methods={"DELETE"})
     */
    public function delete(int $id): Response{ // Cette méthode permet de supprimer un contact avec l'id passé en paramètre
        $entityManager = $this->getDoctrine()->getManager();
        $contact= $entityManager->getRepository(Contact::class)->find($id);

        if (!$contact){
            return $this->json('No contact found for id ' . $id, 404);
        }

        $entityManager->remove($contact);
        $entityManager->flush();

        return $this->json('The contact with id ' . $id . ' was successfully deleted ' );
    }
}
