<?php

declare(strict_types=1);

namespace Domain\Exception;

class InvalidMemberOperationException extends \DomainException
{
    public static function cannotModifyVerifiedMember(): self
    {
        return new self('Cannot modify name of a verified member.');
    }

    public static function cannotVerifyAlreadyVerified(): self
    {
        return new self('Member is already verified.');
    }

    public static function cannotDeactivateAlreadyDisabled(): self
    {
        return new self('Member is already deactivated.');
    }
}
