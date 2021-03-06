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

    public function getAllPages($recherche, $langue)
    {
        $qb = $this->createQueryBuilder('p');

        /**
         * recherche via le titre
         */
        if(!empty($recherche)){
            $qb->andWhere('p.titre = :titre')
               ->setParameter('titre', '%'.$recherche.'%');
        }

        /**
         * recherche via la langue
         */
        if(!empty($langue)){
            $qb->andWhere('p.langue = :langue')
               ->setParameter('langue', $langue);
        }

        $qb->orderBy('p.id', 'DESC');

        return $query = $qb->getQuery()->getResult();
    }

}
