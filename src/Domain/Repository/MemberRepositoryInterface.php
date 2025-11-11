<?php

declare(strict_types=1);

namespace Domain\Repository;

use Domain\Entity\Member;
use Domain\ValueObject\MemberId;
use Domain\ValueObject\Email;

interface MemberRepositoryInterface
{
    public function save(Member $member): void;

    public function findById(MemberId $id): ?Member;

    public function findByEmail(Email $email): ?Member;

    public function findAll(): array;

    public function delete(Member $member): void;

    public function existsByEmail(Email $email): bool;
}
