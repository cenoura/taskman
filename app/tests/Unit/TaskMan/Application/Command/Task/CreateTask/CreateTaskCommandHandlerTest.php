<?php

declare(strict_types=1);

namespace App\Tests\Unit\TaskMan\Application\Command\Task\CreateTask;

use App\TaskMan\Application\Command\Task\CreateTask\CreateTaskCommand;
use App\TaskMan\Application\Command\Task\CreateTask\CreateTaskCommandHandler;
use App\TaskMan\Domain\Model\Task\Task;
use App\TaskMan\Domain\Model\Task\TaskRepositoryInterface;
use App\TaskMan\Domain\Model\User\UniqueUsernameSpecificationInterface;
use App\TaskMan\Domain\Model\User\User;
use App\TaskMan\Domain\Model\User\UserFetcherInterface;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

final class CreateTaskCommandHandlerTest extends TestCase
{

    public function testItCreatesTaskWhenCommandInvoked(): void
    {
        $executionDate = (new \DateTimeImmutable())->setTime(0, 0);
        $title = 'Task 1';

        $taskRepository = $this->createMock(TaskRepositoryInterface::class);
        $taskRepository->expects(self::once())
          ->method('add')
          ->with(
            Assert::callback(
              fn(Task $task): bool => $task->getTitle() === $title
                && $task->getExecutionDate() == $executionDate
            )
          );

        $userFetcher = $this->createMock(UserFetcherInterface::class);
        $userFetcher->method('fetchAuthenticatedUser')->willReturn(
          new User(
            'user1', 'password1', $this->getUniqueUsernameSpecification()
          )
        );

        $command = new CreateTaskCommand($title, $executionDate);
        $handler = new CreateTaskCommandHandler($taskRepository, $userFetcher);

        try {
            $handler($command);
        } catch (\Error $e) {
            if (strpos(
                $e->getMessage(),
                'id must not be accessed before initialization'
              ) === false) {
                throw $e;
            }
        }
    }

    private function getUniqueUsernameSpecification(
    ): UniqueUsernameSpecificationInterface
    {
        $specification = $this->createMock(
          UniqueUsernameSpecificationInterface::class
        );
        $specification->method('isSatisfiedBy')->willReturn(true);

        return $specification;
    }

}