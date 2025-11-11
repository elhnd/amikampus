<?php

namespace Domain\ValueObject;

final class Phone
{
    private string $value;

    private function __construct(string $value)
    {
        $normalized = $this->normalize($value);
        
        if (!$this->isValid($normalized)) {
            throw new \InvalidArgumentException('Invalid phone number format. Expected E.164 format (e.g., +33612345678)');
        }
        
        $this->value = $normalized;
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function equals(Phone $other): bool
    {
        return $this->value === $other->value;
    }

    private function normalize(string $phone): string
    {
        // Remove spaces, dashes, parentheses
        $cleaned = preg_replace('/[\s\-\(\)]/', '', $phone);
        
        // Ensure it starts with +
        if (!str_starts_with($cleaned, '+')) {
            $cleaned = '+' . $cleaned;
        }
        
        return $cleaned;
    }

    private function isValid(string $phone): bool
    {
        // E.164 format: + followed by 1-15 digits
        return preg_match('/^\+[1-9]\d{1,14}$/', $phone) === 1;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
