<?php

declare(strict_types=1);

namespace App\Tests\Unit\TaskMan\Domain\Model\Task;

use App\TaskMan\Domain\Model\Task\Task;
use App\TaskMan\Domain\Model\Task\TaskCreatedEvent;
use App\TaskMan\Domain\Model\User\UniqueUsernameSpecificationInterface;
use App\TaskMan\Domain\Model\User\User;
use PHPUnit\Framework\TestCase;

final class TaskTest extends TestCase
{

    public function testItThrowsExceptionWhenTaskCreatedWithShortTitle(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Title should contain at least/');

        $shortTitle = str_repeat('a', Task::MIN_TITLE_LENGTH - 1);
        new Task($shortTitle, new \DateTimeImmutable(), $this->getUser());
    }

    public function testItThrowsExceptionWhenTaskCreatedWithLongTitle(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Title should contain at most/');

        $longTitle = str_repeat('a', Task::MAX_TITLE_LENGTH + 1);

        new Task($longTitle, new \DateTimeImmutable(), $this->getUser());
    }

    public function testItThrowsExceptionWhenExecutionDateInThePast(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
          'Execution date should not be in the past'
        );

        new Task(
          'Task 1',
          (new \DateTimeImmutable())->modify('-1 day'),
          $this->getUser()
        );
    }

    public function testItPassesWhenValidValuesAreSet(): void
    {
        $task = $this->createValidTask();

        self::assertInstanceOf(Task::class, $task);
    }

    public function testItRaisesTaskCreatedEventWhenTaskCreated(): void
    {
        $task = $this->createValidTask();
        $events = $task->popEvents();

        self::assertContainsEquals(new TaskCreatedEvent($task), $events);
    }

    private function createValidTask(): Task
    {
        return new Task(
          "Task 1",
          new \DateTimeImmutable(),
          $this->getUser()
        );
    }

    private function getUser(): User
    {
        $specification = $this->createMock(
          UniqueUsernameSpecificationInterface::class
        );
        $specification->method('isSatisfiedBy')->willReturn(true);

        return new User('user1', 'password1', $specification);
    }

}