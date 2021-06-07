<?php

namespace App\Repository;

use App\Entity\Equipement;
use App\Entity\Processus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Equipement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Equipement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Equipement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EquipementRepository extends BaseRepository
{
    /**
     * @param Equipement $equipement
     * @return QueryBuilder
     */
    public function listAll(Equipement $equipement = null) {
        $queryBuilder = $this->createQueryBuilder('e')
        ;
        return $this->filterBySociete($queryBuilder, 'e');
    }

    /* (non-PHPdoc)
     * @see \Doctrine\ORM\EntityRepository::findAll()
     */
    public function findAll() {
        $queryBuilder = $this->createQueryBuilder('a')
            ->innerJoin('a.processus', 'p')
            ->innerJoin('p.structure', 's');
        return $this->filterBySociete($queryBuilder, 's')->getQuery()->execute();
    }

    /**
     * @param Processus $processus
     * @return integer
     */
    public function getLastNumero($processus) {
        $data = $this->createQueryBuilder('r')
            ->select('MAX(r.numero) as number')
            ->where('r.processus = :processus')
            ->setParameter('processus', $processus)
            ->getQuery()->getOneOrNullResult();
        return $data['number'];
    }
}
