<?php

declare(strict_types=1);

namespace App\TaskMan\Application\Query\Task\DTO;

final class TaskDTO
{

    private int $id;

    private string $title;

    private \DateTimeImmutable $executionDate;

    private \DateTimeImmutable $createdAt;

    public static function fromArray(array $data): TaskDTO
    {
        if (!isset($data['id'], $data['title'], $data['execution_date'], $data['created_at'])) {
            throw new \InvalidArgumentException(
              'Error creating TaskDTO. Not all required keys are set.'
            );
        }

        $dto = new static();
        $dto->setId((int)$data['id']);
        $dto->setTitle($data['title']);
        $dto->setExecutionDate(new \DateTimeImmutable($data['execution_date']));
        $dto->setCreatedAt(new \DateTimeImmutable($data['created_at']));

        return $dto;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getExecutionDate(): \DateTimeImmutable
    {
        return $this->executionDate;
    }

    public function setExecutionDate(\DateTimeImmutable $executionDate): void
    {
        $this->executionDate = $executionDate;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

}