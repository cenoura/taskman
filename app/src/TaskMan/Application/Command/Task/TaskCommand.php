<?php

declare(strict_types=1);

namespace App\TaskMan\Application\Command\Task;

abstract class TaskCommand
{

    protected string $title;

    protected \DateTimeImmutable $executionDate;

    public function __construct(
      string $title,
      \DateTimeImmutable $executionDate
    ) {
        $this->title = $title;
        $this->executionDate = $executionDate;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getExecutionDate(): \DateTimeImmutable
    {
        return $this->executionDate;
    }

}