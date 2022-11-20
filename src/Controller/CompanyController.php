<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Company;
use App\Form\CompanyType;
use Psr\Log\LoggerInterface;

class CompanyController extends AbstractController
{
    private $log;

    public function __construct(LoggerInterface $log)
    {
        $this->log = $log;
    }

    /**
     * @return Response
     */
    public function loadTemplate(): Response{

        return $this->render('company/index.html.twig');
    }

    /**
     * @return Response
     */
    public function add(Request $request): Response
    {

        $company = new Company();
        $form = $this->createForm(CompanyType::class, $company);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $company = $form->getData();

            $company_name = $company->getName();
            $company_description = $company->getDescription();

            $this->log->notice(
                "Submission Successful",
                [json_encode(['name' => $company_name, 'description' => $company_description])]
            );

            return $this->redirectToRoute('company_add');
        }

        return $this->render('company/index.html.twig', [

            'form_company' => $form->createView()
        ]);
    }
}
