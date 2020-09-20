<?php

declare(strict_types=1);

namespace App\TaskMan\Application\Command\Task\CreateTask;

use App\TaskMan\Domain\Model\Task\Task;
use App\TaskMan\Domain\Model\Task\TaskRepositoryInterface;
use App\TaskMan\Domain\Model\User\UserFetcherInterface;

final class CreateTaskCommandHandler
{

    private TaskRepositoryInterface $taskRepository;

    private UserFetcherInterface $userFetcher;

    public function __construct(
      TaskRepositoryInterface $taskRepository,
      UserFetcherInterface $userFetcher
    ) {
        $this->taskRepository = $taskRepository;
        $this->userFetcher = $userFetcher;
    }

    public function __invoke(CreateTaskCommand $command): int
    {
        $user = $this->userFetcher->fetchAuthenticatedUser();

        $task = new Task(
          $command->getTitle(),
          $command->getExecutionDate(),
          $user
        );

        $this->taskRepository->add($task);

        return $task->getId();
    }

}