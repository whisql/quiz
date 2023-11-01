<?php

namespace App\Repository;

use App\Entity\Questionnaire;
use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function findRandomTasksByQuestionnaire(Questionnaire $questionnaire): array
    {
        $entityManager = $this->getEntityManager();
        $query         = $entityManager->createQuery("SELECT t FROM App\Entity\Task t where t.questionnaire = :questionnaire ORDER BY RAND()");
        $query->setParameter('questionnaire', $questionnaire);

        return $query->getResult();
    }

    public function findByQuestionnaireAndPriorityAsc(Questionnaire $questionnaire): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.questionnaire = :questionnaire')
            ->setParameter('questionnaire', $questionnaire)
            ->orderBy('t.priority', 'asc')
            ->getQuery()
            ->getResult();
    }
}
