<?php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\ProjectTask;
use App\Enum\ProjectTaskCondition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProjectTaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectTask::class);
    }

    public function findLatestPendingTaskByProject(Project $project): ?ProjectTask
    {
        return $this->createQueryBuilder('pt')
            ->andWhere('pt.condition = :condition')
            ->andWhere('pt.project = :project')
            ->setParameter('project', $project)
            ->setParameter('condition', ProjectTaskCondition::PENDING)
            ->orderBy('pt.createdAt', 'asc')
            ->addOrderBy('pt.id', 'asc')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findLatestActiveTaskByProject(Project $project): ?ProjectTask
    {
        return $this->createQueryBuilder('pt')
            ->andWhere('pt.condition = :condition')
            ->andWhere('pt.project = :project')
            ->setParameter('project', $project)
            ->setParameter('condition', ProjectTaskCondition::ACTIVE)
            ->orderBy('pt.createdAt', 'asc')
            ->addOrderBy('pt.id', 'asc')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param Project $project
     * @return ProjectTask[]
     */
    public function findAllCompletedByProject(Project $project): array
    {
        return $this->createQueryBuilder('pt')
            ->andWhere('pt.condition = :condition')
            ->andWhere('pt.project = :project')
            ->setParameter('project', $project)
            ->setParameter('condition', ProjectTaskCondition::COMPLETED)
            ->orderBy('pt.createdAt', 'asc')
            ->addOrderBy('pt.id', 'asc')
            ->getQuery()
            ->getResult();
    }
}
