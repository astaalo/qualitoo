<?php

namespace App\Repository;

use App\Entity\DocumentHasUtilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DocumentHasUtilisateur|null find($id, $lockMode = null, $lockVersion = null)
 * @method DocumentHasUtilisateur|null findOneBy(array $criteria, array $orderBy = null)
 * @method DocumentHasUtilisateur[]    findAll()
 * @method DocumentHasUtilisateur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentHasUtilisateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocumentHasUtilisateur::class);
    }

    // /**
    //  * @return DocumentHasUtilisateur[] Returns an array of DocumentHasUtilisateur objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DocumentHasUtilisateur
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
