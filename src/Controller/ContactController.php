<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Number;
use App\Entity\Person;
use App\Entity\PhoneNumber;
use App\Entity\Tag;
use App\Entity\Task;
use App\Form\ContactType;
use App\Form\PersonType;
use App\Form\TaskType;
use App\Repository\ContactRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\EntityManagerInterface;
use App\Services\FileUploader;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;

class ContactController extends AbstractController
{
    /**
     * @Route("/", name="app_contact")
     */
    public function index(ContactRepository $contactRepository): Response
    {
        $contacts = $contactRepository->findAll();
        return $this->render('contact/index.html.twig', [
            'contacts' => $contacts,
            'favorite' => 'no'
        ]);
    }

    /**
     * @Route("/favorites", name="favorites")
     */
    public function favorites(ContactRepository $contactRepository, EntityManagerInterface $entityManager): Response
    {
        $contacts = $entityManager->getRepository('App:Contact')->findBy(['favorite' => 1]);
        return $this->render('contact/index.html.twig', [
            'controller_name' => 'ContactController',
            'favorite' => 'yes',
            'contacts' => $contacts
        ]);
    }

    /**
     * @Route("/details/{id}", name="details")
     */
    public function details($id, EntityManagerInterface $entityManager): Response
    {
        $contact = $entityManager->getRepository('App:Contact')->findBy(['favorite' => 1]);
        return $this->render('contact/details.html.twig', [
            'controller_name' => 'ContactController',
            'contact' => $contact
        ]);
    }

    /**
     * @Route("/delete/{id}/{favorite}", name="delete")
     */
    public function delete($id, $favorite, ContactRepository $contactRepository, EntityManagerInterface $entityManager): Response
    {
        $contacts = $entityManager->getRepository('App:Contact')->findBy(['favorite' => $favorite]);
        $contact = $contactRepository->find($id);

        $contactRepository->remove($contact);
        return $this->render('contact/index.html.twig', [
            'controller_name' => 'ContactController',
            'contacts' => $contacts,
            'favorite' => $favorite
        ]);
    }

    /**
     * @Route("/addEdit/{id?}", name="addEdit")
     * @param FileUploader $fileUploader
     * @param Request $request
     */
    public function addEdit($id, Request $request, ContactRepository $contactRepository, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        if ($id) {
            $contact = $contactRepository->find($id);
        } else {
            $contact = new Contact();

            $phoneNumber1 = new PhoneNumber();
            $contact->getPhoneNumber()->add($phoneNumber1);
            $phoneNumber2 = new PhoneNumber();
            $contact->getPhoneNumber()->add($phoneNumber2);
            $phoneNumber1->setContact($contact);
            $phoneNumber2->setContact($contact);
        }

        // save the records that are in the database first to compare them with the new one the user sent
        // make sure this line comes before the $form->handleRequest();
        // $orignalExp = new ArrayCollection();
        // foreach ($user->getExp() as $exp) {
        //     $orignalExp->add($exp);
        // }

        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);
        $form->getErrors();

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($contact->getPhoneNumber() as $number) {
                dump($number);
                if (!$number->getPhone()) {
                    $contact->getPhoneNumber()->removeElement($number);
                }
            }
            $file = $request->files->get('contact')['image'];
            if ($file) {
                $filename = $fileUploader->uploadFile($file);
                $contact->setImage($filename);
            }
            $entityManager->persist($contact);
            $entityManager->flush();
            return $this->redirect($this->generateUrl('app_contact'));
        }
        return $this->render('contact/createContact.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/find", name="find_contacts")
     */
    public function find(Request $request, EntityManagerInterface $em, ContactRepository $contactRepository)
    {
        $input = $request->get('input');
        $favorite = $request->get('favorite');

        $contacts = $contactRepository->findAllByField($input, $favorite);

        $arrayCollection = array();

        foreach ($contacts as $contact) {
            dump($contact);
            $arrayCollection[] = array(
                'id' => $contact->getId(),
                'fullname' => $contact->getFullname(),
                'email' => $contact->getEmail(),
                'favorite' => $contact->getFavorite(),
                'image' => $contact->getImage()
            );
        }
        return new JsonResponse($arrayCollection);
    }
}
