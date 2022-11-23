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
    private LoggerInterface $log;

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
        $contactName = $request->request->get('name');
        $contactEmail = $request->request->get('email');
        $contactDescription = $request->request->get('descriere');

        $contact = new Contact();
        $contact->setName($contactName);
        $contact->setEmail($contactEmail);
        $contact->setDescription($contactDescription);

        if ($this->dataValidation->isValid($contact)) {
            $this->log->notice(
                "Submission Successful",
                [
                    json_encode(
                        ['name' => $contactName, 'email' => $contactEmail, 'description' => $contactDescription]
                    )
                ]
            );

            return $this->redirectToRoute('contact');
        } else {
            return $this->render('contact/index.html.twig', [

                'errors' => $this->dataValidation->getErrors($contact),
                'name' => $contactName,
                'email' => $contactEmail,
                'descriere' => $contactDescription
            ]);
        }
    }
}
