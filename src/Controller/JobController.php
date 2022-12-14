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
        $jobActive = $request->get('active');
        $jobPriority = $request->get('priority');

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
                        "created_at" => $job->getCreatedAt(),
                        "company" => [

                            "id" => $job->getCompany()->getId(),
                            "name" => $job->getCompany()->getName()
                        ],
                        "active" => $job->getActive(),
                        "priority" => $job->getPriority()
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

    public function getResultOfJobs($listOfJobs): array
    {
        $jobs = [];

        foreach ($listOfJobs as $job) {
            $jobs[] = [

                "id" => $job->getId(),
                "name" => $job->getName(),
                "description" => $job->getDescription(),
                "created_at" => $job->getCreatedAt(),
                "company" => [

                    "id" => $job->getCompany()->getId(),
                    "name" => $job->getCompany()->getName()
                ],
                "active" => $job->getActive(),
                "priority" => $job->getPriority()
            ];
        }

        return $jobs;
    }

    /**
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        $listOfJobs = $this->jobsRepository->findAll();

        return new JsonResponse([
            'results' => [

                "error" => false,
                "jobs" => $this->getResultOfJobs($listOfJobs)
            ]
        ]);
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
            $this->jobValidator->paramsIsValid($params);
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
                        "description" => $params['description'],
                        "active" => $params['active'],
                        "priority" => $params['priority']
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

            $deletedJob = $this->jobsRepository->find($id);

            if ($deletedJob === null) {
                throw new InvalidArgumentException("Job with $id doesn t exist");
            }


            $jobId = $deletedJob->getId();
            $companyId = $deletedJob->getCompany()->getId();

            $this->jobsRepository->remove($deletedJob);

            return new JsonResponse([
                'results' => [

                    "error" => false,
                    "id" => $jobId,
                    "name" => $deletedJob->getName(),
                    "description" => $deletedJob->getDescription(),
                    "created_at" => $deletedJob->getCreatedAt(),
                    "company" => [

                        "id" => $companyId,
                        "name" => $deletedJob->getCompany()->getName()
                    ],
                    "active" => $deletedJob->getActive(),
                    "priority" => $deletedJob->getPriority()
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
    public function getJobById(int $id): JsonResponse
    {
        try {
            $this->jobValidator->idIsValid($id);
            $listOfJobs = $this->jobsRepository->getById($id);

            if (!sizeof($listOfJobs)) {
                throw new InvalidArgumentException("Job with $id doesn t exist");
            }

            return new JsonResponse([
                'results' => [

                    "error" => false,
                    "jobs" => $this->getResultOfJobs($listOfJobs)
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
    public function getJobByName(string $name): JsonResponse
    {
        try {
            $this->jobValidator->nameIsValid($name);
            $listOfJobs = $this->jobsRepository->getByName($name);

            if (!sizeof($listOfJobs)) {
                throw new InvalidArgumentException("Job with $name doesn t exist");
            }

            return new JsonResponse([
                'results' => [

                    "error" => false,
                    "jobs" => $this->getResultOfJobs($listOfJobs)
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
    public function getJobByLikeName(string $name): JsonResponse
    {
        try {
            $this->jobValidator->nameIsValid($name);
            $listOfJobs = $this->jobsRepository->getByLikeName($name);

            if (!sizeof($listOfJobs)) {
                throw new InvalidArgumentException("Job with have $name in name doesn t exist");
            }

            return new JsonResponse([
                'results' => [

                    "error" => false,
                    "jobs" => $this->getResultOfJobs($listOfJobs)
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
