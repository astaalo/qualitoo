<?php

namespace App\Repository;

use App\Entity\Question;

/**
 * @method Question|null find($id, $lockMode = null, $lockVersion = null)
 * @method Question|null findOneBy(array $criteria, array $orderBy = null)
 * @method Question[]    findAll()
 * @method Question[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionRepository extends EntityRepository
{
    /*public function findAll() {
        return $this->findBy(array(), array('position' => 'ASC', 'id' => 'ASC'));
    }*/

    public function listAll($criteria = null) {
        return $this->createQueryBuilder('q')
            ->where('q.etat != :etat')
            ->setParameter('etat', $this->_states['entity']['supprime'])
            ->orderBy('q.position', 'ASC');
    }
}
