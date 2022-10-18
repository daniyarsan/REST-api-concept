<?php

namespace App\Repository;

use App\Entity\Page;
use App\Traits\QueryPublish;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

/**
 * @extends ServiceEntityRepository<Page>
 *
 * @method Page|null find($id, $lockMode = null, $lockVersion = null)
 * @method Page|null findOneBy(array $criteria, array $orderBy = null)
 * @method Page[]    findAll()
 * @method Page[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageRepository extends ServiceEntityRepository
{
    use QueryPublish;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Page::class);
    }

    public function add(Page $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Page $entity, bool $flush = false): void
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
    public function getPageQuery(Request $request, $showRemoved = false): QueryBuilder
    {
        if ($showRemoved) {
            $this->getEntityManager()->getFilters()->disable('softdeleteable');
        }

        $filters = $request->get('filter', []);

        $query = $this->createQueryBuilder('p')
            ->leftJoin('p.category', 'pc')
            ->leftJoin('p.author', 'pa');

        if (isset($filters['id']) && $filters['id']) {
            $query->andWhere('p.id = :id')
                ->setParameter('id', $filters['id']);
        }
        if (isset($filters['title']) && $filters['title']) {
            $query->andWhere('LOWER(p.title) LIKE :title')
                ->setParameter('title', '%'.mb_strtolower($filters['title']).'%');
        }

        if (isset($filters['category']) && $filters['category']) {
            $query->andWhere('pc.id = :id')
                ->setParameter('id', $filters['category']);
            $query->orWhere('pc.parentId = :parentId')
                ->setParameter('parentId', $filters['category']);
        }

        $query->orderBy('p.id', 'desc');

        return $query;
    }

    public function getMenuStatusByCategoryId(int $categoryId) {
        $query = $this->createQueryBuilder('p')
            ->leftJoin('p.category', 'pc')
            ->select( 'p.status')
            ->groupBy('p.status')
            ->where('pc.id = :id')->setParameter('id', $categoryId);

        return $query->getQuery()->getSingleColumnResult();
    }
}
