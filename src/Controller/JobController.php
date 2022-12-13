<?php

namespace App\Controller;

use App\Entity\Jobs;
use App\Repository\CompanyRepository;
use App\Repository\JobsRepository;
use App\Validators\JobValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class JobController extends AbstractController
{
    /**
     * @var JobsRepository
     */
    private JobsRepository $jobsRepository;
    /**
     * @var CompanyRepository
     */
    private CompanyRepository $companyRepository;
    /**
     * @var JobValidator
     */
    private JobValidator $jobValidator;

    /**
     * @param JobsRepository $jobsRepository
     * @param CompanyRepository $companyRepository
     * @param JobValidator $jobValidator
     */
    public function __construct(
        JobsRepository $jobsRepository,
        CompanyRepository $companyRepository,
        JobValidator $jobValidator
    ) {
        $this->jobsRepository = $jobsRepository;
        $this->companyRepository = $companyRepository;
        $this->jobValidator = $jobValidator;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function add(Request $request): JsonResponse
    {
        $companyId = $request->get('company_id');
        $jobName = $request->get('name');
        $jobDescription = $request->get('description');
        $jobCreatedAt = new \DateTime("now");
        $jobActive = 0;
        $jobPriority = 0;

        $company = $this->companyRepository->find($companyId);

        try {
            $this->jobValidator->nameIsValid($jobName);
            $this->jobValidator->descriptionIsValid($jobDescription);
            $this->jobValidator->companyIsValid($company);
            $job = new Jobs();
            $job->setName($jobName);
            $job->setDescription($jobDescription);
            $job->setCreatedAt($jobCreatedAt);
            $job->setActive($jobActive);
            $job->setPriority($jobPriority);
            $job->setCompany($company);

            $this->jobsRepository->save($job);

            return new JsonResponse([
                'results' => [

                    "error" => false,
                    "job" => [

                        "id" => $job->getId(),
                        "name" => $job->getName(),
                        "description" => $job->getDescription(),
                        "created_at" => $job->getCreatedAt()
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
        $listOfJobs = $this->jobsRepository->findAll();

        $listOfNamesJobs = [];

        foreach ($listOfJobs as $job) {
            $listOfNamesJobs[] = $job->getName();
        }

        return new JsonResponse(['list_of_jobs' => $listOfNamesJobs]);
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

            $this->jobValidator->idIsValid($id);
            $this->jobValidator->companyIsValid($this->companyRepository->find($params['company_id']));
            $this->jobValidator->nameIsValid($params['name']);
            $this->jobValidator->descriptionIsValid($params['description']);
            $this->jobsRepository->update($id, $params);

            return new JsonResponse([
                'results' => [

                    "error" => false,
                    "job" => [

                        "id" => $id,
                        "name" => $params['name'],
                        "description" => $params['description']
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
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        try {
            $this->jobValidator->idIsValid($id);

            return new JsonResponse([
                'results' => [

                    "error" => false,
                    "message" => $this->jobsRepository->removeById($id)
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
     * @param int $id
     * @return JsonResponse
     */
    public function jobId(int $id): JsonResponse
    {
        try {
            $this->jobValidator->idIsValid($id);

            return new JsonResponse([
                'results' => [

                    "error" => false,
                    "job" => $this->jobsRepository->getById($id)
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
    public function jobName(string $name): JsonResponse
    {
        try {
            $this->jobValidator->nameIsValid($name);

            return new JsonResponse([
                'results' => [

                    "error" => false,
                    "job" => $this->jobsRepository->getByName($name)
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
    public function likejobName(string $name): JsonResponse
    {
        try {
            $this->jobValidator->nameIsValid($name);

            return new JsonResponse([
                'results' => [

                    "error" => false,
                    "job" => $this->jobsRepository->getByLikeName($name)
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
