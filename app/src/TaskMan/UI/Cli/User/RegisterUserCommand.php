<?php

declare(strict_types=1);

namespace App\TaskMan\UI\Cli\User;

use App\TaskMan\Application\Command\User\CreateUser\CreateUserCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

use function strlen;

final class RegisterUserCommand extends Command
{

    use HandleTrait;

    public const MIN_PASSWORD_LENGTH = 6;

    public const MAX_USERNAME_LENGTH = 80;

    protected static $defaultName = 'app:register-user';

    public function __construct(MessageBusInterface $commandBus)
    {
        $this->messageBus = $commandBus;
        parent::__construct();
    }

    protected function execute(
      InputInterface $input,
      OutputInterface $output
    ): int {
        $helper = $this->getHelper('question');

        $question = new Question('Please enter the username [user]: ', 'user');
        $username = (string)$helper->ask($input, $output, $question);

        if ($username === '') {
            $output->writeln(
              '<error>Please provide an `username` to continue.</error>'
            );
        }

        $question = new Question(
          'Please enter the password (min 6 characters) : '
        );
        $question->setHidden(true);
        $password = (string)$helper->ask($input, $output, $question);

        if (strlen($password) < self::MIN_PASSWORD_LENGTH) {
            $output->writeln(
              '<error>The provided password is too short. Please try again informing 6 characters or more.</error>'
            );
        }

        $question = new Question('Please confirm your password: ');
        $question->setHidden(true);
        $passwordRepeat = (string)$helper->ask($input, $output, $question);

        if ($password !== $passwordRepeat) {
            $output->writeln(
              '<error>Passwords did not match. Please try again.</error>'
            );
        }

        $this->handle(new CreateUserCommand($username, $password));

        $output->writeln('<info>User created successfully.</info>');

        return 0;
    }

}