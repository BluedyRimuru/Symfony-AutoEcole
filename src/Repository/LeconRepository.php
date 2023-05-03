<?php

namespace App\Repository;

use App\Entity\Lecon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Lecon>
 *
 * @method Lecon|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lecon|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lecon[]    findAll()
 * @method Lecon[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LeconRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lecon::class);
    }

    public function getNombreDeLeconsParUser($id) {
        $query = $this->createQueryBuilder('l')
            ->select("COUNT(l.id)")
            ->innerJoin('l.equipe', 'e')
            ->where('e.id = :id')
            ->setParameter(':id', $id)
        ;
        return $query->getQuery()->getSingleScalarResult();
    }

    public function getLeconInfo($id): float|int|array|string
    {
        $query = $this->createQueryBuilder('l')
            ->select("l.date, l.heure, l.reglee")
            ->innerJoin('l.equipe', 'e')
            ->where('e.id = :id')
            ->orderBy('l.date')
            ->setMaxResults('5')
            ->setParameter(':id', $id)
        ;
        return $query->getQuery()->getScalarResult();
    }

    public function getLastOrders() {

        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT e.nom, l.date, l.reglee
                FROM App\Entity\Lecon l
                JOIN l.equipe e
                WHERE e.roles LIKE :role
                ORDER BY l.id DESC           
               ')->setParameter('role', '%'.'ROLE_USER'.'%')->setMaxResults(5);

        // returns an array of Product objects
        return $query->getScalarResult();
    }

    public function getMoniteurLesPlusSollicites() {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT COUNT(u.nom) as nbMoniteur, u.nom FROM App\Entity\Lecon l
            JOIN l.equipe u
            WHERE u.roles LIKE :roles
            GROUP BY u.nom')->setParameter(":roles", '%'.'ROLE_MONITEUR'.'%');
        return $query->getScalarResult();
    }

    public function getNombreDeLecons() {
        $query = $this->createQueryBuilder('l')
            ->select("COUNT(e.id) / 2")
            ->innerJoin('l.equipe', 'e')
        ;
        return $query->getQuery()->getSingleScalarResult();
    }
    public function getLeconByEmail(){
        $query = $this->createQueryBuilder('l')
            ->select("l.heure, l.date")
            ;
        return $query->getQuery()->getResult();
    }

    public function getPrixAllLecon() {
        $query = $this->createQueryBuilder('l')
            ->select("SUM(c.prix)")
            ->innerJoin('l.codevehicule', 'v')
            ->innerJoin('v.codecategorie', 'c')
//            ->innerJoin('')
        ;
        return $query->getQuery()->getSingleScalarResult();
    }

    public function getLeconEleveForVehicule($idVehicule, $dateLecon, $heure) {
        $query = $this->createQueryBuilder('l')
            ->where('l.codevehicule = :id')
            ->andWhere('l.heure = :heure')
            ->andWhere('l.date = :date')

            ->setParameter(':id' ,$idVehicule)
            ->setParameter(':heure',$heure)
            ->setParameter(':date', $dateLecon)
        ;
        return $query->getQuery()->getResult();
    }

    public function getLeconEleveForMoniteur($idMoniteur, $dateLecon, $heure) {
        $query = $this->createQueryBuilder('l')
            ->innerJoin('l.equipe', 'e')
            ->where('e.id = :idMoniteur')
            ->andWhere('l.heure = :heure')
            ->andWhere('l.date = :dateLecon')
            ->setParameter('idMoniteur', $idMoniteur)
            ->setParameter('dateLecon', $dateLecon)
            ->setParameter('heure', $heure)
        ;
        return $query->getQuery()->getResult();
    }

    public function getLeconEleveForEleve($idEleve, $dateLecon, $heure) {
        $query = $this->createQueryBuilder('l')
            ->innerJoin('l.equipe', 'e')
            ->where('e.id = :idEleve')
            ->andWhere('l.heure = :heure')
            ->andWhere('l.date = :dateLecon')
            ->setParameter('idEleve', $idEleve)
            ->setParameter('dateLecon', $dateLecon)
            ->setParameter('heure', $heure)
        ;
        return $query->getQuery()->getResult();
    }

    public function getLeconMoniteurByIDAndDate($id, $heure, $dateLecon){
        $query = $this->createQueryBuilder('l')
            ->where('l.codemoniteur = :id')
            ->andWhere('l.heure = :heure')
            ->andWhere('l.date = :date')

            ->setParameter(':id' ,$id)
            ->setParameter(':heure',$heure)
            ->setParameter(':date', $dateLecon)
            ;
        return $query->getQuery()->getResult();
    }

    public function getLeconEleveByIDAndDate($id, $heure, $dateLecon){
        $query = $this->createQueryBuilder('l')
            ->where('l.codeeleve = :id')
            ->andWhere('l.heure = :heure')
            ->andWhere('l.date = :date')

            ->setParameter(':id' ,$id)
            ->setParameter(':heure',$heure)
            ->setParameter(':date', $dateLecon)
        ;
        return $query->getQuery()->getResult();
    }

    public function getLeconVehiculeByIDAndDate($id, $heure, $dateLecon){
        $query = $this->createQueryBuilder('l')
            ->where('l.codevehicule = :id')
            ->andWhere('l.heure = :heure')
            ->andWhere('l.date = :date')

            ->setParameter(':id' ,$id)
            ->setParameter(':heure',$heure)
            ->setParameter(':date', $dateLecon)
        ;
        return $query->getQuery()->getResult();
    }


    public function save(Lecon $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Lecon $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getPlanning(int $id){
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT l
            FROM App\Entity\Lecon l
            JOIN l.equipe e
            WHERE e.id = :id'
        )->setParameter('id', $id);

        return $query->getResult();
    }

//    /**
//     * @return Lecon[] Returns an array of Lecon objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Lecon
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
