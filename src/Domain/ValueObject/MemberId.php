<?php

namespace Domain\ValueObject;

final class MemberId
{
    private string $value;

    private function __construct(string $value)
    {
        if (!$this->isValidUuid($value)) {
            throw new \InvalidArgumentException('Invalid UUID format for MemberId');
        }
        $this->value = $value;
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public static function generate(): self
    {
        return new self(self::generateUuid());
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function equals(MemberId $other): bool
    {
        return $this->value === $other->value;
    }

    private function isValidUuid(string $uuid): bool
    {
        return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $uuid) === 1;
    }

    private static function generateUuid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
