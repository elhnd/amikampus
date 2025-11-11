<?php

declare(strict_types=1);

namespace Application\UseCase;

use Application\DTO\MemberDTO;
use Domain\Repository\MemberRepositoryInterface;

final readonly class ListMembers
{
    public function __construct(
        private MemberRepositoryInterface $memberRepository,
    ) {}

    /**
     * @return MemberDTO[]
     */
    public function execute(): array
    {
        $members = $this->memberRepository->findAll();

        return array_map(
            fn($member) => new MemberDTO(
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
            ),
            $members
        );
    }
}
