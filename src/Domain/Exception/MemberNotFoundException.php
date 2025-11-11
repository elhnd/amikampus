<?php

declare(strict_types=1);

namespace Domain\Exception;

use Domain\ValueObject\MemberId;

class MemberNotFoundException extends \DomainException
{
    public static function withId(MemberId $id): self
    {
        return new self(sprintf('Member with ID "%s" not found.', $id->toString()));
    }

    public static function withEmail(string $email): self
    {
        return new self(sprintf('Member with email "%s" not found.', $email));
    }
}
