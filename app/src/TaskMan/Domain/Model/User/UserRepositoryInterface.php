<?php

declare(strict_types=1);

namespace App\TaskMan\Domain\Model\User;

interface UserRepositoryInterface
{

    public function find(int $id): ?User;

    public function findByUsername(string $username): ?User;

    public function add(User $user): void;

    public function remove(User $user): void;

}