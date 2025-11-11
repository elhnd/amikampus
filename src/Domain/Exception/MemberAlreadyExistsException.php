<?php

declare(strict_types=1);

namespace Domain\Exception;

class MemberAlreadyExistsException extends \DomainException
{
    public static function withEmail(string $email): self
    {
        return new self(sprintf('A member with email "%s" already exists.', $email));
    }
}
