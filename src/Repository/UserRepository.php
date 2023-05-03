<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findByRole(string $role)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.roles LIKE :role')
            ->setParameter('role', '%'.$role.'%')
            ->getQuery()
            ->getResult()
            ;
    }

    public function getNombreDeEleve() {
        $query = $this->createQueryBuilder('u')
            ->select("count(u.nom)")
            ->where("u.roles LIKE '%ROLE_USER%'")
        ;
        return $query->getQuery()->getSingleScalarResult();
    }

    public function getEleveByPage(int $page) {
        $entityManager = $this->getEntityManager();

        if ($page > 1) {
            $page = $page . 0;
        } else {
            $page = 0;
        }

        $query = $entityManager->createQuery(
            'SELECT u.id, u.prenom, u.nom, u.roles, u.email, u.telephone, u.isVerified, img.path FROM App\Entity\User u 
            INNER JOIN App\Entity\Image img WITH u.image = img.id
            WHERE u.roles LIKE :role')->setParameter('role', '%'.'ROLE_USER'.'%')
            ->setFirstResult($page)
            ->setMaxResults(10);
        return $query->getScalarResult();
    }

    public function checkMoniteur(int $id) {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT u.id, u.email FROM App\Entity\User u WHERE u.roles LIKE :role AND u.id = :id'
        )->setParameters([
            'role' => '%'.'ROLE_MONITEUR'.'%',
            'id' => $id
        ]);
        return $query->getScalarResult();
    }

    public function getUserById($email){
        $query = $this->createQueryBuilder('u')
            ->where('u.email = :email')

            ->setParameter(':email' ,$email)
        ;
        return $query->getQuery()->getResult();
    }
    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getUserByRole($roles){
        $query = $this->createQueryBuilder('r')
            ->where('r.roles = :roles')

            ->setParameter(':roles' ,$roles)
        ;
        return $query->getQuery()->getResult();
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->save($user, true);
    }

//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
