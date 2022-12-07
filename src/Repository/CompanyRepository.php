<?php

namespace App\Repository;

use App\Entity\Company;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Company>
 *
 * @method Company|null find($id, $lockMode = null, $lockVersion = null)
 * @method Company|null findOneBy(array $criteria, array $orderBy = null)
 * @method Company[]    findAll()
 * @method Company[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Company::class);
    }

    /**
     * @param Company $entity
     * @return void
     */
    public function save(Company $entity): void
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * @param Company $entity
     * @return void
     */
    public function remove(Company $entity): void
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * @param int $id
     * @return Company|null
     */
    public function removeById(int $id): string
    {
        $listOfCompanies = $this->findAll();

        foreach ($listOfCompanies as $company) {
            if ($company->getId() == $id) {
                $this->remove($company);
                return "Company with $id was deleted!";
            }
        }

        return "Company with $id doesn t exist!";
    }

    /**
     * @param int $id
     * @param array $params
     * @return int
     */
    public function update(int $id, array $params): int
    {
        $queryBuilder = $this->createQueryBuilder('c');

        $nbUpdatedRows = $queryBuilder->update()
            ->set('c.name', ':companyName')
            ->where('c.id = :companyId')
            ->setParameter('companyName', $params['name'])
            ->setParameter('companyId', $id)
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
        $queryBuilder = $this->createQueryBuilder('c');

        $query = $queryBuilder
            ->select('c.name')
            ->where("c.id = $id")
            ->getQuery()
            ->getResult();

        return $query;
    }

    /**
     * @param string $name
     * @return array
     */
    public function getByName(string $name): array
    {
        $queryBuilder = $this->createQueryBuilder('c');

        $query = $queryBuilder
            ->select('c.name')
            ->where("c.name = $name")
            ->getQuery()
            ->getResult();

        return $query;
    }

    /**
     * @param string $name
     * @return array
     */
    public function getByLikeName(string $name): array
    {
        $queryBuilder = $this->createQueryBuilder('c');

        $query = $queryBuilder
            ->select('c.name')
            ->where('c.name LIKE :name')
            ->setParameter('name', '%' . $name . '%')
            ->getQuery()
            ->getResult();

        return $query;
    }

//    /**
//     * @return Company[] Returns an array of Company objects
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

//    public function findOneBySomeField($value): ?Company
//    {
//        return $this->createQueryBuilder('j')
//            ->andWhere('j.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
