<?php

declare(strict_types=1);

namespace Application\UseCase;

use Application\DTO\CreateMemberDTO;
use Application\DTO\MemberDTO;
use Domain\Entity\Member;
use Domain\Exception\MemberAlreadyExistsException;
use Domain\Repository\MemberRepositoryInterface;
use Domain\ValueObject\Email;
use Domain\ValueObject\MemberId;
use Domain\ValueObject\Phone;

final readonly class CreateMember
{
    public function __construct(
        private MemberRepositoryInterface $memberRepository,
    ) {}

    public function execute(CreateMemberDTO $dto): MemberDTO
    {
        $email = Email::fromString($dto->email);

        if ($this->memberRepository->existsByEmail($email)) {
            throw MemberAlreadyExistsException::withEmail($dto->email);
        }

        $phone = $dto->phone ? Phone::fromString($dto->phone) : null;

        $member = Member::register(
            id: MemberId::generate(),
            firstName: $dto->firstName,
            lastName: $dto->lastName,
            email: $email,
            phone: $phone,
        );

        $this->memberRepository->save($member);

        return $this->toDTO($member);
    }

    private function toDTO(Member $member): MemberDTO
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
