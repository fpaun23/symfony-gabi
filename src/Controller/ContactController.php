<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Validation\DataValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;

class ContactController extends AbstractController
{

    private DataValidatorInterface $dataValidation;
    private $log;

    public function __construct(LoggerInterface $log, DataValidatorInterface $dataValidation)
    {
        $this->dataValidation = $dataValidation;
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
        $contact->setName($request->request->get('name'));
        $contact->setEmail($request->request->get('email'));
        $contact->setDescription($request->request->get('descriere'));

        if($this->dataValidation->isValid($contact)) {

            $this->log->notice(
                "Submission Successful",
                [json_encode(['name' => $contact_name, 'email' => $contact_email, 'description' => $contact_description])]
            );

            return $this->redirectToRoute('contact');
        }
        else {

            return $this->render('contact/index.html.twig', [

                'errors' => $this->dataValidation->getErrors($contact)
            ]);
        }
    }
}
