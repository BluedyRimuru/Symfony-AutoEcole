<?php

namespace App\Repository;

use App\Entity\Categorie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Categorie>
 *
 * @method Categorie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Categorie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Categorie[]    findAll()
 * @method Categorie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategorieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Categorie::class);
    }

    public function getLocomotionLaPlusUtilise() {
        $query = $this->createQueryBuilder('c')
            ->select('c.libelle as count')
            ->innerJoin("App\Entity\Vehicule", "v", "WITH","c.id = v.codecategorie")
            ->innerJoin("App\Entity\Lecon", "l", "WITH","v.id = l.codevehicule")
            ->groupBy('c.libelle')
            ->orderBy('count', 'DESC')
            ->setMaxResults(1)
        ;
        return $query->getQuery()->getSingleScalarResult();
    }

    public function getCAByDay($id, $day) {
        $query = $this->createQueryBuilder('c')
            ->select('SUM(c.prix)')
            ->innerJoin("App\Entity\Vehicule", "v", "WITH","c.id = v.codecategorie")
            ->innerJoin("App\Entity\Lecon", "l", "WITH","v.id = l.codevehicule")
            ->innerJoin('l.equipe', 'e', "WITH", "e.id = :id")
            ->where('l.date = :day')
            ->setParameter(':id' , $id)
            ->setParameter(':day', $day)
        ;
        return $query->getQuery()->getSingleScalarResult();
    }

    public function getCATotalUser($id) {
        $query = $this->createQueryBuilder('c')
            ->select('SUM(c.prix)')
            ->innerJoin("App\Entity\Vehicule", "v", "WITH","c.id = v.codecategorie")
            ->innerJoin("App\Entity\Lecon", "l", "WITH","v.id = l.codevehicule")
            ->innerJoin('l.equipe', 'e', "WITH", "e.id = :id")
            ->setParameter(':id' , $id)
        ;
        return $query->getQuery()->getSingleScalarResult();
    }

    public function getCASemaineMoniteur($id, $year) {
        $query = $this->createQueryBuilder('c')
            ->select('SUM(c.prix)')
            ->innerJoin("App\Entity\Vehicule", "v", "WITH","c.id = v.codecategorie")
            ->innerJoin("App\Entity\Lecon", "l", "WITH","v.id = l.codevehicule")
            ->innerJoin('l.equipe', 'e', "WITH", "e.id = :id")
            ->where('l.date < CURRENT_DATE() AND l.date > :year - 7')
            ->setParameter(':id' , $id)
            ->setParameter(':year', $year)
        ;
        return $query->getQuery()->getSingleScalarResult();
    }

    public function getPrixTotalDuPermis($id) {
        $query = $this->createQueryBuilder('c')
            ->select("SUM(c.prix)")
            ->innerJoin("App\Entity\Vehicule", "v", "WITH","c.id = v.codecategorie")
            ->innerJoin("App\Entity\Lecon", "l", "WITH","v.id = l.codevehicule")
            ->innerJoin('l.equipe', 'e', "WITH", "e.id = :id")
            ->setParameter(':id' ,$id)
        ;
        return $query->getQuery()->getSingleScalarResult();
    }

    public function getPrixRestantAPayer($id) {
        $query = $this->createQueryBuilder('c')
            ->select("SUM(c.prix)")
            ->innerJoin("App\Entity\Vehicule", "v", "WITH","c.id = v.codecategorie")
            ->innerJoin("App\Entity\Lecon", "l", "WITH","v.id = l.codevehicule")
            ->innerJoin('l.equipe', 'e', "WITH", "e.id = :id")
            ->where('l.reglee = 0')
            ->setParameter(':id' ,$id)
        ;
        return $query->getQuery()->getSingleScalarResult();
    }

    public function save(Categorie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Categorie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Categorie[] Returns an array of Categorie objects
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

//    public function findOneBySomeField($value): ?Categorie
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
