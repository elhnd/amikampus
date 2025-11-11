<?php

namespace Domain\ValueObject;

final class MemberStatus
{
    private const NON_VERIFIED = 'non_verified';
    private const VERIFIED_STUDENT = 'verified_student';
    private const VERIFIED_ALUMNI = 'verified_alumni';
    private const DISABLED = 'disabled';

    private const VALID_STATUSES = [
        self::NON_VERIFIED,
        self::VERIFIED_STUDENT,
        self::VERIFIED_ALUMNI,
        self::DISABLED,
    ];

    private string $value;

    private function __construct(string $value)
    {
        if (!in_array($value, self::VALID_STATUSES, true)) {
            throw new \InvalidArgumentException(
                sprintf('Invalid member status "%s". Valid statuses are: %s', $value, implode(', ', self::VALID_STATUSES))
            );
        }
        $this->value = $value;
    }

    public static function nonVerified(): self
    {
        return new self(self::NON_VERIFIED);
    }

    public static function verifiedStudent(): self
    {
        return new self(self::VERIFIED_STUDENT);
    }

    public static function verifiedAlumni(): self
    {
        return new self(self::VERIFIED_ALUMNI);
    }

    public static function disabled(): self
    {
        return new self(self::DISABLED);
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function isVerified(): bool
    {
        return in_array($this->value, [self::VERIFIED_STUDENT, self::VERIFIED_ALUMNI], true);
    }

    public function isDisabled(): bool
    {
        return $this->value === self::DISABLED;
    }

    public function equals(MemberStatus $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
