<?php

declare(strict_types=1);

namespace App\TaskMan\Domain\Model\Task;

use App\Shared\Domain\Model\Aggregate;
use App\TaskMan\Domain\Model\User\User;
use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Entity()
 */
class Task extends Aggregate
{

    public const MIN_TITLE_LENGTH = 5;

    public const MAX_TITLE_LENGTH = 100;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"unsigned"=true})
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    private string $title;

    /**
     * @ORM\ManyToOne(targetEntity="App\TaskMan\Domain\Model\User\User")
     * @ORM\JoinColumn(onDelete="cascade", nullable=false)
     */
    private User $user;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private \DateTimeImmutable $executionDate;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private \DateTimeImmutable $createdAt;

    public function __construct(
      string $title,
      \DateTimeImmutable $executionDate,
      User $user
    ) {
        $this->setTitle($title);
        $this->setExecutionDate($executionDate);
        $this->setUser($user);
        $this->setCreatedAt(new \DateTimeImmutable());

        $this->raise(new TaskCreatedEvent($this));
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getExecutionDate(): \DateTimeImmutable
    {
        return $this->executionDate;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    private function setTitle(string $title): void
    {
        Assert::minLength(
          $title,
          self::MIN_TITLE_LENGTH,
          'Title should contain at least %2$s characters. Got: %s'
        );
        Assert::maxLength(
          $title,
          self::MAX_TITLE_LENGTH,
          'Title should contain at most %2$s characters. Got: %s'
        );
        $this->title = $title;
    }

    private function setUser(User $user): void
    {
        $this->user = $user;
    }

    private function setExecutionDate(\DateTimeImmutable $executionDate): void
    {
        $executionDateNormalized = $executionDate->setTime(0, 0);
        $now = (new \DateTimeImmutable())->setTime(0, 0);

        Assert::greaterThanEq(
          $executionDateNormalized,
          $now,
          'Execution date should not be in the past'
        );

        $this->executionDate = $executionDateNormalized;
    }

    private function setCreatedAt(\DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

}