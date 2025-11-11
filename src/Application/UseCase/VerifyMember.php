<?php

declare(strict_types=1);

namespace Application\UseCase;

use Domain\Exception\MemberNotFoundException;
use Domain\Repository\MemberRepositoryInterface;
use Domain\ValueObject\MemberId;

final readonly class VerifyMember
{
    public function __construct(
        private MemberRepositoryInterface $memberRepository,
    ) {}

    public function execute(string $memberId): void
    {
        $id = MemberId::fromString($memberId);
        $member = $this->memberRepository->findById($id);

        if (!$member) {
            throw MemberNotFoundException::withId($id);
        }

        $member->verify(new \DateTimeImmutable);

        $this->memberRepository->save($member);
    }
}
