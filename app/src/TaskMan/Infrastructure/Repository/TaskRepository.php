<?php

declare(strict_types=1);

namespace App\TaskMan\Infrastructure\Repository;

use App\TaskMan\Domain\Model\Task\Task;
use App\TaskMan\Domain\Model\Task\TaskRepositoryInterface;
use App\TaskMan\Domain\Model\User\User;
use Doctrine\ORM\EntityManagerInterface;
use PDO;

class TaskRepository implements TaskRepositoryInterface
{

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function findByUserAndExecutionDate(
      User $user,
      \DateTimeImmutable $executionDate
    ): array {
        $taskTable = $this->em->getClassMetadata(Task::class)->getTableName();

        $qb = $this->em->getConnection()->createQueryBuilder()
          ->select('t.*')
          ->from($taskTable, 't')
          ->where('t.user_id = :userId')
          ->andWhere('DATE(t.execution_date) = :executionDate')
          ->orderBy('t.title')
          ->setParameters(
            [
              ':userId' => $user->getId(),
              ':executionDate' => $executionDate->format('Y-m-d'),
            ]
          );

        return $this->em->getConnection()->executeQuery(
          $qb->getSQL(),
          $qb->getParameters()
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function add(Task $task): void
    {
        $this->em->persist($task);
        $this->em->flush();
    }

    public function remove(Task $task): void
    {
        $this->em->remove($task);
        $this->em->flush();
    }

}