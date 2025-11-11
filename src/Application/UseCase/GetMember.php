<?php

declare(strict_types=1);

namespace Application\UseCase;

use Application\DTO\MemberDTO;
use Domain\Exception\MemberNotFoundException;
use Domain\Repository\MemberRepositoryInterface;
use Domain\ValueObject\MemberId;

final readonly class GetMember
{
    public function __construct(
        private MemberRepositoryInterface $memberRepository,
    ) {}

    public function execute(string $memberId): MemberDTO
    {
        $id = MemberId::fromString($memberId);
        $member = $this->memberRepository->findById($id);

        if (!$member) {
            throw MemberNotFoundException::withId($id);
        }

        return new MemberDTO(
            id: $member->id->toString(),
            firstName: $member->firstName,
            lastName: $member->lastName,
            email: $member->email->toString(),
            phone: $member->phone?->toString(),
            status: $member->status->toString(),
            studentId: $member->studentId,
            department: $member->department,
            verifiedAt: $member->verifiedAt?->format(\DateTimeInterface::ATOM),
            disabledAt: $member->disabledAt?->format(\DateTimeInterface::ATOM),
            createdAt: $member->createdAt->format(\DateTimeInterface::ATOM),
            updatedAt: $member->updatedAt->format(\DateTimeInterface::ATOM),
            fullName: $member->getFullName(),
            isVerified: $member->isVerified(),
            isActive: $member->isActive(),
        );
    }
}
