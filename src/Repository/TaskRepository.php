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
    public function searchAndPaginate(?int $limit, ?int $offset, string $routeName = null, string $search = null): array
    {
        $parameters = [];
        // Utilisateur
        $exprUser = 't.owner = :user';
        $parameters['user'] = $this->security->getUser();
        if (in_array(User::ROLE_ADMIN, $this->security->getUser()->getRoles())) {
            $exprUser = 't.owner = :user OR t.owner = :anonyme';
            $parameters['anonyme'] = 1;
        }
        $qb = $this->createQueryBuilder('t')->where($exprUser);
        // Recherche
        if ($search !== null) {
            $qb->andWhere('LOWER(t.title) LIKE :search OR LOWER(t.content) LIKE :search');
            $parameters['search'] = '%'.strtolower($search).'%';
        }
        // État de la tâche faite/non faite
        if ($routeName) {
            $isDone = true;
            if ($routeName === 'task_list_todo') {
                $isDone = false;
            }
            $qb->andWhere('t.isDone = :isDone');
            $parameters['isDone'] = $isDone;
        }
        $qb->setParameters($parameters);
        // Pagination
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
