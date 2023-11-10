<?php

namespace App\Repository;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @extends ServiceEntityRepository<Task>
 *
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private readonly Security $security)
    {
        parent::__construct($registry, Task::class);
    }

    /**
     * @return Task[] Returns an array of Customer objects
     */
    public function searchAndPaginate(?int $limit, ?int $offset, string $routeName = null): array
    {
        $isDone = true;
        if ($routeName === 'task_list_todo') {
            $isDone = false;
        }
        $qb = $this->createQueryBuilder('t');
        $qb->where('t.owner = :user');
        if (in_array(User::ROLE_ADMIN, $this->security->getUser()->getRoles())) {
            $qb->orWhere('t.owner = :anonyme')
                ->setParameter('anonyme', 1);
        }
        $qb->setParameter('user', $this->security->getUser());
        if ($routeName) {
            $qb->andWhere('t.isDone = :isDone')
                ->setParameter('isDone', $isDone);
        }
        $qb->orderBy('t.createdAt', 'DESC');
        if ($limit !== null) {
            $qb->setMaxResults($limit);
        }
        if ($offset !== null) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }
}
