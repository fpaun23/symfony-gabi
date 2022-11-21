<?php

namespace App\Controller;

use App\Entity\Contact;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Validation\DataValidatorContact;
use Psr\Log\LoggerInterface;

class ContactController extends AbstractController
{

    private DataValidatorContact $dataValidation;
    private $log;

    public function __construct(LoggerInterface $log)
    {
        $this->dataValidation = new DataValidatorContact();
        $this->log = $log;
    }

    /**
     * @return Response
     */
    public function loadTemplate(): Response
    {
        return $this->render('contact/index.html.twig');
    }

    /**
     * @param Request $request
     * @return Response
     */

    public function add(Request $request): Response
    {
        $contact_name = $request->request->get('name');
        $contact_email = $request->request->get('email');
        $contact_description = $request->request->get('descriere');

        $contact = new Contact();

        $contact->setName($contact_name);
        $contact->setEmail($contact_email);
        $contact->setDescription($contact_description);

        $errors = $this->dataValidation->entityValidation($contact);

        if (strlen($errors) > 0)
        {
            return $this->render('contact/index.html.twig', [

                'errors' => $errors
            ]);
        }
        else
        {
            $this->log->notice(
                "Submission Successful",
                [json_encode(['name' => $contact_name, 'email' => $contact_email, 'description' => $contact_description])]
            );

            return $this->redirectToRoute('contact');
        }
    }
}
