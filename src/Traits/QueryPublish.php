<?php

namespace App\Traits;


trait QueryPublish
{
    /**
     * @param int $id
     * @return int|mixed|string
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findActiveOneById(int $id, ?string $alias = 't')
    {
        $query = $this->getQueryForPublish($alias);

        $query->andWhere($alias . '.id = :id')
            ->setParameter('id', $id);

        return $query->getQuery()->getSingleResult();
    }

}