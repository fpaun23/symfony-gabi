<?php

namespace App\Repository;

use App\Entity\Company;
use App\Entity\Jobs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Jobs>
 *
 * @method Jobs|null find($id, $lockMode = null, $lockVersion = null)
 * @method Jobs|null findOneBy(array $criteria, array $orderBy = null)
 * @method Jobs[]    findAll()
 * @method Jobs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Jobs::class);
    }

    public function save(Jobs $entity): void
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    public function remove(Jobs $entity): void
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }

    public function removeById(int $id): string
    {
        $listOfJobs = $this->findAll();

        foreach ($listOfJobs as $job) {
            if ($job->getId() == $id) {
                $this->remove($job);
                return "Job with $id was deleted!";
            }
        }

        return "Job with $id doesn t exist!";
    }

    public function update(int $id, array $params): int
    {
        $queryBuilder = $this->createQueryBuilder('j');

        $nbUpdatedRows = $queryBuilder->update()
            ->set('j.name', ':jobName')
            ->set('j.description', ':jobDescription')
            ->where('j.id = :jobId')
            ->setParameter('jobName', $params['name'])
            ->setParameter('jobDescription', $params['description'])
            ->setParameter('jobId', $id)
            ->getQuery()
            ->execute();

        return $nbUpdatedRows;
    }

    public function getById(int $id): array
    {
        $queryBuilder = $this->createQueryBuilder('j');

        $job = $queryBuilder
            ->select('j.name', 'j.description')
            ->where("j.id = $id")
            ->getQuery()
            ->getResult();

        return $job;
    }

    public function getByName(string $name): array
    {
        $queryBuilder = $this->createQueryBuilder('j');

        $job = $queryBuilder
            ->select('j.name', 'j.description')
            ->where("j.name = $name")
            ->getQuery()
            ->getResult();

        return $job;
    }

    public function getByLikeName(string $name): array
    {
        $queryBuilder = $this->createQueryBuilder('j');

        $job = $queryBuilder
            ->select('j.name', 'j.description')
            ->where('j.name LIKE :name')
            ->setParameter('name', '%' . $name . '%')
            ->getQuery()
            ->getResult();

        return $job;
    }

//    /**
//     * @return Jobs[] Returns an array of Jobs objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('j')
//            ->andWhere('j.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('j.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Jobs
//    {
//        return $this->createQueryBuilder('j')
//            ->andWhere('j.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
