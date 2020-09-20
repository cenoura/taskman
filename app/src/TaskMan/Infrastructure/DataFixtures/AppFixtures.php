<?php

declare(strict_types=1);

namespace App\TaskMan\Infrastructure\DataFixtures;

use App\TaskMan\Domain\Model\Task\Task;
use App\TaskMan\Domain\Model\User\User;
use App\TaskMan\Infrastructure\Specification\User\UniqueUsernameSpecification;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class AppFixtures extends Fixture
{

    private EncoderFactoryInterface $encoderFactory;

    private UniqueUsernameSpecification $uniqueUsernameSpecification;

    public function __construct(
      EncoderFactoryInterface $encoderFactory,
      UniqueUsernameSpecification $uniqueUsernameSpecification
    ) {
        $this->encoderFactory = $encoderFactory;
        $this->uniqueUsernameSpecification = $uniqueUsernameSpecification;
    }

    private function createTask(
      int $count,
      \DateTimeImmutable $executionDate,
      User $user
    ): Task {
        return new Task(
          "Task #{$count} - {$executionDate->format("Y-m-d")} - {$user->getUsername()}",
          $executionDate,
          $user
        );
    }

    public function load(ObjectManager $manager): void
    {
        $today = new \DateTimeImmutable();
        $tomorrow = (new \DateTimeImmutable())->modify("+1 day");
        $encoder = $this->encoderFactory->getEncoder(User::class);

        $user1 = new User(
          "user1",
          $encoder->encodePassword("password1", null),
          $this->uniqueUsernameSpecification
        );

        $manager->persist($user1);

        $user2 = new User(
          "user2",
          $encoder->encodePassword("password2", null),
          $this->uniqueUsernameSpecification
        );

        $manager->persist($user2);

        for ($i = 1; $i < 4; $i++) {
            $manager->persist($this->createTask($i, $today, $user1));
        }

        $manager->persist($this->createTask(1, $tomorrow, $user1));

        for ($i = 1; $i < 3; $i++) {
            $manager->persist($this->createTask($i, $today, $user2));
        }

        $manager->persist($this->createTask(1, $tomorrow, $user2));

        $manager->flush();
    }

}
