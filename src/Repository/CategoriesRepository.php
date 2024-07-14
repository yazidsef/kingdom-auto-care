<?php

namespace App\Repository;

use App\Entity\Categories;
use App\Entity\Products;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Categories>
 *
 * @method Categories|null find($id, $lockMode = null, $lockVersion = null)
 * @method Categories|null findOneBy(array $criteria, array $orderBy = null)
 * @method Categories[]    findAll()
 * @method Categories[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoriesRepository extends ServiceEntityRepository
{
    private $paginator; 
    public function __construct(ManagerRegistry $registry , PaginatorInterface $paginator)
    {
        parent::__construct($registry, Categories::class);
        $this->paginator = $paginator;
    }

    //    /**
    //     * @return Categories[] Returns an array of Categories objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Categories
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

            public function findWithProducts($slug)
            {
                return $this->createQueryBuilder('c')
                ->leftJoin('c.products','p')
                ->addSelect('p')
                ->where('c.slug = :slug')
                ->setParameter('slug',$slug)
                ->getQuery()
                ->getOneOrNullResult();
            }
            public function ProductsWithCategories(){
                $query = $this->createQueryBuilder('c')
                    ->innerJoin('c.products','p')
                    ->innerJoin('p.marques','m')
                    ->select('p','m' ,'c')    
                    ->getQuery()
                    ->getResult();
                    return $query;
                }

            
}
