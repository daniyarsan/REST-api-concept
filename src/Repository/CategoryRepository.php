<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

/**
 * @extends ServiceEntityRepository<Category>
 *
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function add(Category $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Category $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param Request $request
     * @return QueryBuilder
     */
    public function getIndexQuery(Request $request): QueryBuilder
    {
        $filters = $request->get('filter', []);

        $query = $this->createQueryBuilder('c');

        if (isset($filters['id']) && $filters['id']) {
            $query->andWhere('p.id = :id')->setParameter('id', $filters['id']);
        }

        if (isset($filters['name']) && $filters['name']) {
            $query->andWhere('LOWER(p.name) LIKE :name')->setParameter('name', '%'.mb_strtolower($filters['name']).'%');
        }

        return $query;
    }

    /**
     * @return int|mixed|string
     */
    public function findFirstLevelCategories(): mixed
    {
        $query = $this->createQueryBuilder('c');
        $query->andWhere('c.parentId is null');

        return $query->getQuery()->getResult();
    }


    /**
     * @param int $id
     * @return int|mixed|string
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findSubcategoriesByCategory(int $id)
    {
        $query = $this->createQueryBuilder('c');

        $query->andWhere('c.parentId = :id')->setParameter('id', $id);

        return $query->getQuery()->getResult();
    }

    public function getPlainMenu()
    {
        return $this->createQueryBuilder('c')->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }

}
