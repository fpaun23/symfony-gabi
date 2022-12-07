<?php

namespace App\Controller;

use App\Entity\Company;
use App\Repository\CompanyRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class CompanyController extends AbstractController
{
    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $managerRegistry;

    /**
     * @var CompanyRepository
     */
    private CompanyRepository $companyRepository;

    /**
     * @param CompanyRepository $companyRepository
     * @param ManagerRegistry $managerRegistry
     */
    public function __construct(CompanyRepository $companyRepository, ManagerRegistry $managerRegistry)
    {
        $this->companyRepository = $companyRepository;
        $this->managerRegistry = $managerRegistry;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function add(Request $request): JsonResponse
    {
        $companyName = $request->get('name');

        $company = new Company();
        $company->setName($companyName);

        $this->companyRepository->save($company);

        return new JsonResponse("Company with name $companyName was added!");
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
        $params = $request->query->all();

        return new JsonResponse([$this->companyRepository->update($id, $params)]);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        $removedCompany = $this->companyRepository->removeById($id);

        if ($removedCompany != null) {
            $this->companyRepository->remove($removedCompany);
        }

        return new JsonResponse(
            ($removedCompany != null ? "Company with id $id was removed" : "Company with id $id doesn t exist")
        );
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function companyId(int $id): JsonResponse
    {
        $queryBuilder = $this->managerRegistry->getManager()->createQueryBuilder();

        $query = $queryBuilder
            ->select('c.name')
            ->from(Company::class, 'c')
            ->where("c.id = $id")
            ->getQuery()
            ->getResult();

        return new JsonResponse($query);
    }

    /**
     * @param string $name
     * @return JsonResponse
     */
    public function companyName(string $name): JsonResponse
    {
        $queryBuilder = $this->managerRegistry->getManager()->createQueryBuilder();

        $query = $queryBuilder
            ->select('c.name')
            ->from(Company::class, 'c')
            ->where("c.name = $name")
            ->getQuery()
            ->getResult();

        return new JsonResponse($query);
    }

    /**
     * @param string $name
     * @return JsonResponse
     */
    public function likeCompanyName(string $name): JsonResponse
    {
        $queryBuilder = $this->managerRegistry->getManager()->createQueryBuilder();

        $query = $queryBuilder
            ->select('c.name')
            ->from(Company::class, 'c')
            ->where('c.name LIKE :name')
            ->setParameter('name', '%' . $name . '%')
            ->getQuery()
            ->getResult();

        return new JsonResponse($query);
    }
}
