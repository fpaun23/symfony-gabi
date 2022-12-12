<?php

namespace App\Controller;

use App\Entity\Jobs;
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
     * @var JobValidator
     */
    private JobValidator $jobValidator;

    /**
     * @param JobsRepository $jobsRepository
     * @param JobValidator $jobValidator
     */
    public function __construct(JobsRepository $jobsRepository, JobValidator $jobValidator)
    {
        $this->jobsRepository = $jobsRepository;
        $this->jobValidator = $jobValidator;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function add(Request $request): JsonResponse
    {
        $jobName = $request->get('name');
        $jobDescription = $request->get('description');

        try {
            $this->jobValidator->nameIsValid($jobName);
            $this->jobValidator->descriptionIsValid($jobDescription);
            $job = new Jobs();
            $job->setName($jobName);
            $job->setDescription($jobDescription);

            $this->jobsRepository->save($job);

            return new JsonResponse([
                'results' => [

                    "error" => false,
                    "job" => [

                        "id" => $job->getId(),
                        "name" => $job->getName(),
                        "description" => $job->getDescription()
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
            $this->jobValidator->idIsValid($id);

            return new JsonResponse([
                'results' => [

                    "error" => false,
                    "message" => $this->jobsRepository->removeById($id)
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
