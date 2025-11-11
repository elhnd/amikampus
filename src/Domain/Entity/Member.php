<?php

declare(strict_types=1);

namespace Domain\Entity;

use Domain\ValueObject\MemberId;
use Domain\ValueObject\Email;
use Domain\ValueObject\Phone;
use Domain\ValueObject\MemberStatus;

class Member
{
    public function __construct(
        public MemberId $id,
        public string $firstName,
        public string $lastName,
        public Email $email,
        public ?Phone $phone,
        public MemberStatus $status,
        public ?string $studentId = null,
        public ?string $department = null,
        public ?\DateTimeImmutable $verifiedAt = null,
        public ?\DateTimeImmutable $disabledAt = null,
        public ?\DateTimeImmutable $createdAt = null,
        public ?\DateTimeImmutable $updatedAt = null,
    ) {
        $this->createdAt ??= new \DateTimeImmutable;
        $this->updatedAt ??= new \DateTimeImmutable;
    }

    public static function register(
        MemberId $id,
        string $firstName,
        string $lastName,
        Email $email,
        ?Phone $phone = null,
    ): self {
        return new self(
            id: $id,
            firstName: trim($firstName),
            lastName: trim($lastName),
            email: $email,
            phone: $phone,
            status: MemberStatus::nonVerified(),
        );
    }

    public function updateProfile(
        ?string $firstName = null,
        ?string $lastName = null,
        ?Phone $phone = null,
        ?string $studentId = null,
        ?string $department = null,
    ): void {
        if (!$this->status->isVerified()) {
            if ($firstName !== null) {
                $this->firstName = trim($firstName);
            }
            if ($lastName !== null) {
                $this->lastName = trim($lastName);
            }
        }
        
        if ($phone !== null) {
            $this->phone = $phone;
        }
        
        if ($studentId !== null) {
            $this->studentId = trim($studentId);
        }
        
        if ($department !== null) {
            $this->department = trim($department);
        }
        
        $this->updatedAt = new \DateTimeImmutable;
    }

    public function verify(\DateTimeImmutable $when): void
    {
        $this->status = MemberStatus::verifiedStudent();
        $this->verifiedAt = $when;
        $this->updatedAt = new \DateTimeImmutable;
    }

    public function deactivate(\DateTimeImmutable $when): void
    {
        $this->status = MemberStatus::disabled();
        $this->disabledAt = $when;
        $this->updatedAt = new \DateTimeImmutable;
    }

    public function getFullName(): string
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    public function isVerified(): bool
    {
        return $this->status->isVerified();
    }

    public function isActive(): bool
    {
        return !$this->status->isDisabled();
    }
}