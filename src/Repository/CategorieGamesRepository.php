<?php

namespace App\Repository;

use App\Entity\CategorieGames;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CategorieGames|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategorieGames|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategorieGames[]    findAll()
 * @method CategorieGames[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategorieGamesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategorieGames::class);
    }

    // /**
    //  * @return CategorieGames[] Returns an array of CategorieGames objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CategorieGames
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
