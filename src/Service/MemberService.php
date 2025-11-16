<?php

namespace App\Service;

use App\Entity\Member;
use App\Repository\MemberRepository;
use Doctrine\ORM\EntityManagerInterface;

class MemberService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly MemberRepository $memberRepository
    ) {}

    public function saveMember(Member $member): void
    {
        $this->entityManager->persist($member);
        $this->entityManager->flush();
    }

    public function getMembers(): array
    {
        return $this->memberRepository->findAll();
    }

    public function getMemberById(int $id): ?Member
    {
        return $this->memberRepository->find($id);
    }

    public function getMemberByEmail(string $email): ?Member
    {
        return $this->memberRepository->findOneBy(['email' => $email]);
    }

    public function updateMember(Member $member): void
    {
        // Pas besoin de persist() car l'entité est déjà managée
        // On flush directement pour sauvegarder les modifications
        $this->entityManager->flush();
    }

    public function deleteMember(Member $member): void
    {
        $this->entityManager->remove($member);
        $this->entityManager->flush();
    }
}