<?php

declare(strict_types=1);

namespace Application\DTO;

final readonly class MemberDTO
{
    public function __construct(
        public string $id,
        public string $firstName,
        public string $lastName,
        public string $email,
        public ?string $phone,
        public string $status,
        public ?string $studentId,
        public ?string $department,
        public ?string $verifiedAt,
        public ?string $disabledAt,
        public string $createdAt,
        public string $updatedAt,
        public string $fullName,
        public bool $isVerified,
        public bool $isActive,
    ) {}
}
