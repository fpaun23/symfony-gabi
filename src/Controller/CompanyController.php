<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class CompanyController extends AbstractController
{
    /**
     * @return Response
     */
    public function show(): Response
    {
        return $this->render('company/index.html.twig');
    }
}
