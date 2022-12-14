<?php

namespace App\Controller;

use App\Entity\Company;
use App\Repository\CompanyRepository;
use App\Validators\CompanyValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\Exception\InvalidArgumentException;

class CompanyController extends AbstractController
{
    /**
     * @var CompanyRepository
     */
    private CompanyRepository $companyRepository;

    /**
     * @var CompanyValidator
     */
    private CompanyValidator $companyValidator;

    /**
     * @param CompanyRepository $companyRepository
     * @param CompanyValidator $companyValidator
     */
    public function __construct(CompanyRepository $companyRepository, CompanyValidator $companyValidator)
    {
        $this->companyRepository = $companyRepository;
        $this->companyValidator = $companyValidator;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function add(Request $request): JsonResponse
    {
        $companyName = $request->get('name');

        try {
            $this->companyValidator->nameIsValid($companyName);
            $company = new Company();
            $company->setName($companyName);

            $this->companyRepository->save($company);

            return new JsonResponse([
                'results' => [

                    "error" => false,
                    "company" => [

                        "id" => $company->getId(),
                        "name" => $company->getName()
                    ]
                ]
            ]);
        } catch (InvalidArgumentException $exception) {
            return new JsonResponse([
                'results' => [

                    "error" => true,
                    "message" => $exception->getMessage()
                ]
            ]);
        }
    }

    /**
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        $listOfCompanies = $this->companyRepository->findAll();

        $listOfNamesCompanies = [];

        foreach ($listOfCompanies as $company) {
            $listOfNamesCompanies[] = $company->getName();
        }

        return new JsonResponse(['list_of_companies' => $listOfNamesCompanies]);
    }

    /**
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    public function update(int $id, Request $request): JsonResponse
    {
        try {
            $params = $request->query->all();

            $this->companyValidator->idIsValid($id);
            $this->companyValidator->nameIsValid($params['name']);
            $this->companyRepository->update($id, $params);

            return new JsonResponse([
                'results' => [

                    "error" => false,
                    "company" => [

                        "id" => $id,
                        "name" => $params['name']
                    ]
                ]
            ]);
        } catch (\InvalidArgumentException $exception) {
            return new JsonResponse([
                'results' => [

                    "error" => true,
                    "message" => $exception->getMessage()
                ]
            ]);
        }
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        try {
            $this->companyValidator->idIsValid($id);

            $deletedCompany = $this->companyRepository->find($id);

            if ($deletedCompany == null) {
                throw new InvalidArgumentException("Company with $id doesn t exist");
            }

            $companyId = $deletedCompany->getId();

            $this->companyRepository->remove($deletedCompany);

            return new JsonResponse([
                'results' => [

                    "error" => false,
                    "company" => [

                        "id" => $companyId,
                        "name" => $deletedCompany->getName()
                    ]
                ]
            ]);
        } catch (\InvalidArgumentException $exception) {
            return new JsonResponse([
                'results' => [

                    "error" => true,
                    "message" => $exception->getMessage()
                ]
            ]);
        }
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function getCompanyById(int $id): JsonResponse
    {
        try {
            $this->companyValidator->idIsValid($id);

            return new JsonResponse([
                'results' => [

                    "error" => false,
                    "company_name" => $this->companyRepository->getById($id)
                ]
            ]);
        } catch (InvalidArgumentException $exception) {
            return new JsonResponse([
                'results' => [

                    "error" => true,
                    "message" => $exception->getMessage()
                ]
            ]);
        }
    }

    /**
     * @param string $name
     * @return JsonResponse
     */
    public function getCompanyByName(string $name): JsonResponse
    {
        try {
            $this->companyValidator->nameIsValid($name);

            return new JsonResponse([
                'results' => [

                    "error" => false,
                    "company_name" => $this->companyRepository->getByName($name)
                ]
            ]);
        } catch (InvalidArgumentException $exception) {
            return new JsonResponse([
                'results' => [

                    "error" => true,
                    "message" => $exception->getMessage()
                ]
            ]);
        }
    }

    /**
     * @param string $name
     * @return JsonResponse
     */
    public function getCompanyByLikeName(string $name): JsonResponse
    {
        try {
            $this->companyValidator->nameIsValid($name);

            return new JsonResponse([
                'results' => [

                    "error" => false,
                    "company_name" => $this->companyRepository->getByLikeName($name)
                ]
            ]);
        } catch (InvalidArgumentException $exception) {
            return new JsonResponse([
                'results' => [

                    "error" => true,
                    "message" => $exception->getMessage()
                ]
            ]);
        }
    }
}
