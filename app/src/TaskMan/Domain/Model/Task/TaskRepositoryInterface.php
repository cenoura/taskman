<?php

declare(strict_types=1);

namespace App\TaskMan\Domain\Model\Task;

use App\TaskMan\Domain\Model\User\User;

interface TaskRepositoryInterface
{

    public function add(Task $task): void;

    public function remove(Task $task): void;

    public function findByUserAndExecutionDate(
      User $user,
      \DateTimeImmutable $executionDate
    ): array;

}