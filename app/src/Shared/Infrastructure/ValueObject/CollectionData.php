<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\ValueObject;

final class CollectionData
{

    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }

}