<?php

declare(strict_types=1);

namespace Application\DTO;

final readonly class CreateMemberDTO
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $email,
        public ?string $phone = null,
    ) {}
}
