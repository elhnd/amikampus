<?php

declare(strict_types=1);

namespace Application\UseCase;

use Application\DTO\UpdateMemberDTO;
use Application\DTO\MemberDTO;
use Domain\Exception\MemberNotFoundException;
use Domain\Repository\MemberRepositoryInterface;
use Domain\ValueObject\MemberId;
use Domain\ValueObject\Phone;

final readonly class UpdateMember
{
    public function __construct(
        private MemberRepositoryInterface $memberRepository,
    ) {}

    public function execute(UpdateMemberDTO $dto): MemberDTO
    {
        $memberId = MemberId::fromString($dto->memberId);
        $member = $this->memberRepository->findById($memberId);

        if (!$member) {
            throw MemberNotFoundException::withId($memberId);
        }

        $phone = $dto->phone ? Phone::fromString($dto->phone) : null;

        $member->updateProfile(
            firstName: $dto->firstName,
            lastName: $dto->lastName,
            phone: $phone,
            studentId: $dto->studentId,
            department: $dto->department,
        );

        $this->memberRepository->save($member);

        return $this->toDTO($member);
    }

    private function toDTO($member): MemberDTO
    {
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
