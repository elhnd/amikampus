<?php

declare(strict_types=1);

namespace Application\DTO;

final readonly class UpdateMemberDTO
{
    public function __construct(
        public string $memberId,
        public ?string $firstName = null,
        public ?string $lastName = null,
        public ?string $phone = null,
        public ?string $studentId = null,
        public ?string $department = null,
    ) {}
}
