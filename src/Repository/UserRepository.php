<?php

namespace App\Repository;

use App\Entity\Token;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @implements PasswordUpgraderInterface<User>
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

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * @return User[] Returns an array of Customer objects
     */
    public function searchAndPaginate(?int $limit, ?int $offset, string $routeName = null, string $search = null): array
    {
        $qb = $this->createQueryBuilder('u');

        $qb->where('u.id != :id')
            ->setParameter('id', 1);

        // Recherche
        if ($search !== null) {
            $qb->andWhere('LOWER(u.username) LIKE :search OR LOWER(u.email) LIKE :search')
                ->setParameter('search', '%'.strtolower($search).'%');
        }
        $qb->orderBy('u.createdAt', 'DESC');
        if ($limit !== null) {
            $qb->setMaxResults($limit);
        }
        if ($offset !== null) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }

    public function createActivationToken(User $user, Token $token): void
    {
        $user->setActivationToken($token);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function activate(User $user): void
    {
        $user->setActivated(true);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }
}
