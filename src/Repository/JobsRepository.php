<?php

namespace App\Repository;

use App\Entity\Jobs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use http\Exception\InvalidArgumentException;

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
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Jobs::class);
    }

    /**
     * @param Jobs $entity
     * @return void
     */
    public function save(Jobs $entity): void
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * @param Jobs $entity
     * @return void
     */
    public function remove(Jobs $entity): void
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * @param int $id
     * @param array $params
     * @return int
     */
    public function update(int $id, array $params): int
    {
        $queryBuilder = $this->createQueryBuilder('j');

        $nbUpdatedRows = $queryBuilder->update()
            ->set('j.name', ':jobName')
            ->set('j.description', ':jobDescription')
            ->set('j.company', ':jobCompanyId')
            ->set('j.active', ':jobActive')
            ->set('j.priority', ':jobPriority')
            ->where('j.id = :jobId')
            ->setParameter('jobName', $params['name'])
            ->setParameter('jobDescription', $params['description'])
            ->setParameter('jobCompanyId', $params['company_id'])
            ->setParameter('jobActive', $params['active'])
            ->setParameter('jobPriority', $params['priority'])
            ->setParameter('jobId', $id)
            ->getQuery()
            ->execute();

        return $nbUpdatedRows;
    }

    /**
     * @param int $id
     * @return array
     */
    public function getById(int $id): array
    {
        $queryBuilder = $this->createQueryBuilder('j');

        $job = $queryBuilder
            ->where("j.id = :jobId")
            ->setParameter('jobId', $id)
            ->getQuery()
            ->getResult();

        return $job;
    }

    /**
     * @param string $name
     * @return array
     */
    public function getByName(string $name): array
    {
        $queryBuilder = $this->createQueryBuilder('j');

        $job = $queryBuilder
            ->where("j.name = :jobName")
            ->setParameter('jobName', $name)
            ->getQuery()
            ->getResult();

        return $job;
    }

    /**
     * @param string $name
     * @return array
     */
    public function getByLikeName(string $name): array
    {
        $queryBuilder = $this->createQueryBuilder('j');

        $job = $queryBuilder
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
