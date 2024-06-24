<?php

namespace App\Repository;

use App\Entity\Products;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Products>
 *
 * @method Products|null find($id, $lockMode = null, $lockVersion = null)
 * @method Products|null findOneBy(array $criteria, array $orderBy = null)
 * @method Products[]    findAll()
 * @method Products[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductsRepository extends ServiceEntityRepository
{
    private $paginator; 

    public function __construct(ManagerRegistry $registry , PaginatorInterface $paginator)
    {
        parent::__construct($registry, Products::class);
        $this->paginator = $paginator;
    }

    //    /**
    //     * @return Products[] Returns an array of Products objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Products
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
        public function findWithCategoryAndImages($slug)
        {
            return $this->createQueryBuilder('p')
            ->leftJoin('p.categories','c')
            ->addSelect('c')
            ->leftJoin('p.images','i')
            ->addSelect('i')
            ->where('p.slug = :slug')
            ->setParameter('slug',$slug)
            ->getQuery()
            ->getOneOrNullResult();
        }
        public function findProductsPaginated(int $page , string $slug , int $limit = 6 ) :array
        {
            $limit = abs($limit);
            $result = [];
            $query = $this->getEntityManager()->createQueryBuilder()
            
            ->from('app\Entity\Products','p')
            ->join('p.categories','c')
            ->select('c','p')
            ->where("c.slug = '$slug'")
            ->setMaxResults($limit)
            ->setFirstResult(($page * $limit) - $limit);
            $paginator = new Paginator($query);
            $data = $paginator->getQuery()->getResult();

            //on verifie quon a des données 
            if(empty($data)){
             return $result;    
            }
            //on vas calculer le nombre de page 
            $pages = ceil($paginator->count()/ $limit);
            //on vas remplir le tableau 

            $result ['data'] = $data;
            $result['pages']= $pages;
            $result['page'] = $page;
            $result['limit']= $limit;            
            return $result;
        }
        public function testTwo(string $slug){
            $query = $this->createQueryBuilder('p');
            $query->innerJoin('p.categories','c' );
            $query->innerJoin('p.marques','m');
            $query->select('p','c','m');
            $query->where("c.slug = '$slug'");
            
            $query->distinct(true); // pour eviter les doublons
            return $query->getQuery()->getResult();
        
        }



        public function testQuery(){
            $query = $this->createQueryBuilder('p')
            ->where('p.prix > 0');

            //Return the queryBuilder
            return $query;
        }
        
        
    
}
