<?php

declare(strict_types=1);

namespace App\TaskMan\Application\Query\Task\GetTasks;

use App\Shared\Infrastructure\ValueObject\CollectionData;
use App\TaskMan\Application\Query\Task\DTO\TaskDTO;
use App\TaskMan\Domain\Model\Task\TaskRepositoryInterface;
use App\TaskMan\Domain\Model\User\UserFetcherInterface;

final class GetTasksQueryHandler
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

    public function __invoke(GetTasksQuery $query): CollectionData
    {
        $user = $this->userFetcher->fetchAuthenticatedUser();
        $executionDate = $query->getExecutionDate();

        $tasks = $this->taskRepository->findByUserAndExecutionDate(
          $user,
          $executionDate
        );

        $taskDTOs = [];

        foreach ($tasks as $task) {
            $taskDTOs[] = TaskDTO::fromArray($task);
        }

        return new CollectionData($taskDTOs);
    }

}