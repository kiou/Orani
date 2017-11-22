<?php

namespace PageBundle\Repository;

/**
 * PageRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PageRepository extends \Doctrine\ORM\EntityRepository
{

    public function getAllPages($recherche)
    {
        $qb = $this->createQueryBuilder('p');

        /**
         * recherche via le username
         */
        if(!is_null($recherche)){
            $qb->andWhere('p.titre LIKE :titre')
                ->setParameter('titre', '%'.$recherche.'%');
        }

        $qb->orderBy('p.id', 'DESC');

        return $query = $qb->getQuery()->getResult();
    }

}
