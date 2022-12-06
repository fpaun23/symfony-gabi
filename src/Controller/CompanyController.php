<?php

namespace App\Controller;

use App\Entity\Company;
use App\Repository\CompanyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;

class CompanyController extends AbstractController
{
    public function add(ManagerRegistry $doctrine): Response
    {
        $company = new Company();
        $company->setName('Endava');

/*        $companyRepository = new CompanyRepository($doctrine);
        $companyRepository->save($company, true);*/

        $companyRepository = $doctrine->getRepository(Company::class);
        $companyRepository->

        return new Response('Saved new company with id ' . $company->getId());
    }


}
