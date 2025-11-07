# AmikAmpu - Architecture Hexagonale

## 🏗️ Vue d'ensemble de l'Architecture

AmikAmpu utilise une **architecture hexagonale** (aussi appelée Ports & Adapters ou Clean Architecture) pour garantir une séparation claire des responsabilités et une maintenance facilitée.

### Principes Fondamentaux

1. **Domain** : Le cœur métier, indépendant de tout framework
2. **Application** : Les cas d'usage qui orchestrent le domaine
3. **Infrastructure** : Les détails techniques (DB, Web, etc.)
4. **Shared** : Code partagé entre les couches

---

## 📁 Structure Complète du Projet

```
src/
├── Domain/                           # 🎯 CŒUR MÉTIER (Business Logic)
│   ├── Entity/                      # Entités métier pures (POPO)
│   │   ├── Member.php
│   │   ├── User.php
│   │   ├── Role.php
│   │   ├── MemberRole.php
│   │   ├── Election.php             # Itération 4
│   │   ├── Candidate.php            # Itération 4
│   │   ├── Vote.php                 # Itération 4
│   │   ├── Document.php             # Itération 2
│   │   ├── Subscription.php         # Itération 13
│   │   └── Event.php                # Itération 14
│   │
│   ├── ValueObject/                 # Objets valeur (immuables)
│   │   ├── Email.php
│   │   ├── Phone.php
│   │   ├── MemberStatus.php
│   │   ├── VoteStatus.php
│   │   └── DocumentStatus.php
│   │
│   ├── Repository/                  # Interfaces des repositories
│   │   ├── MemberRepositoryInterface.php
│   │   ├── UserRepositoryInterface.php
│   │   ├── RoleRepositoryInterface.php
│   │   ├── ElectionRepositoryInterface.php
│   │   └── VoteRepositoryInterface.php
│   │
│   ├── Service/                     # Services métier
│   │   ├── MemberService.php
│   │   ├── ElectionService.php
│   │   └── VoteSecurityService.php
│   │
│   └── Exception/                   # Exceptions métier
│       ├── MemberNotFoundException.php
│       ├── MemberAlreadyExistsException.php
│       ├── InvalidVoteException.php
│       └── ElectionClosedException.php
│
├── Application/                      # 🔄 CAS D'USAGE (Use Cases)
│   ├── Port/                        # 🔌 PORTS (Interfaces hexagonales)
│   │   ├── In/                      # Input Ports (driven by UI/API)
│   │   │   ├── CreateMemberPort.php
│   │   │   ├── RegisterUserPort.php
│   │   │   ├── CastVotePort.php
│   │   │   └── ...                  # Autres ports d'entrée
│   │   │
│   │   └── Out/                     # Output Ports (drive infrastructure)
│   │       ├── NotificationPort.php
│   │       ├── OtpPort.php
│   │       ├── QrSignerPort.php
│   │       ├── ClockPort.php
│   │       ├── TransactionPort.php
│   │       └── FileStoragePort.php
│   │
│   ├── UseCase/                     # Use cases métier (implémentent Input Ports)
│   │   ├── Member/
│   │   │   ├── CreateMember.php
│   │   │   ├── UpdateMember.php
│   │   │   ├── FindMember.php
│   │   │   ├── ListMembers.php
│   │   │   ├── VerifyMember.php     # Itération 2
│   │   │   └── ImportMembers.php    # Itération 8
│   │   │
│   │   ├── Auth/
│   │   │   ├── RegisterUser.php     # Itération 1
│   │   │   ├── LoginUser.php        # Itération 1
│   │   │   ├── GenerateOTP.php      # Itération 1.5
│   │   │   └── VerifyOTP.php        # Itération 1.5
│   │   │
│   │   ├── Election/
│   │   │   ├── CreateElection.php   # Itération 4
│   │   │   ├── CastVote.php         # Itération 4
│   │   │   ├── ModifyVote.php       # Itération 5
│   │   │   ├── CloseElection.php    # Itération 4
│   │   │   └── AssignWinnerRole.php # Itération 7
│   │   │
│   │   └── Role/
│   │       ├── AssignRole.php       # Itération 3
│   │       └── RevokeRole.php       # Itération 3
│   │
│   ├── DTO/                         # Data Transfer Objects
│   │   ├── MemberDTO.php
│   │   ├── VoteDTO.php
│   │   └── ElectionResultDTO.php
│   │
│   └── Handler/                     # Handlers (optionnel, pour events)
│       └── MemberCreatedHandler.php
│
├── Infrastructure/                   # 🔌 DÉTAILS TECHNIQUES (Adapters)
│   ├── Adapter/                     # 🔄 ADAPTERS (implémentent Output Ports)
│   │   ├── Notification/
│   │   │   ├── EmailNotificationAdapter.php      # Implémente NotificationPort
│   │   │   └── WhatsAppNotificationAdapter.php   # Implémente NotificationPort
│   │   │
│   │   ├── Otp/
│   │   │   └── SymfonyOtpAdapter.php             # Implémente OtpPort
│   │   │
│   │   ├── Security/
│   │   │   ├── HmacQrSignerAdapter.php           # Implémente QrSignerPort
│   │   │   └── SystemClockAdapter.php            # Implémente ClockPort
│   │   │
│   │   ├── Storage/
│   │   │   ├── LocalFileStorageAdapter.php       # Implémente FileStoragePort
│   │   │   └── S3FileStorageAdapter.php          # Implémente FileStoragePort
│   │   │
│   │   └── Transaction/
│   │       └── DoctrineTransactionAdapter.php    # Implémente TransactionPort
│   │
│   ├── Doctrine/                    # Implémentation Doctrine ORM (Output Adapter)
│   │   ├── Repository/
│   │   │   ├── DoctrineMemberRepository.php
│   │   │   ├── DoctrineUserRepository.php
│   │   │   ├── DoctrineRoleRepository.php
│   │   │   ├── DoctrineElectionRepository.php
│   │   │   └── DoctrineVoteRepository.php
│   │   │
│   │   └── Mapping/                 # Mapping XML/YAML (pas d'annotations)
│   │       ├── Member.orm.xml
│   │       ├── User.orm.xml
│   │       ├── Role.orm.xml
│   │       ├── Election.orm.xml
│   │       └── Vote.orm.xml
│   │
│   ├── Web/                         # Controllers Web (Twig) - INPUT ADAPTERS
│   │   ├── Controller/
│   │   │   ├── HomeController.php
│   │   │   ├── AuthController.php
│   │   │   ├── MemberController.php
│   │   │   ├── ElectionController.php
│   │   │   └── AdminController.php
│   │   │
│   │   └── Form/                    # Formulaires Symfony
│   │       ├── RegistrationType.php
│   │       ├── MemberType.php
│   │       └── ElectionType.php
│   │
│   ├── Api/                         # Controllers API REST (Phase 2) - INPUT ADAPTERS
│   │   └── Controller/
│   │       ├── ApiMemberController.php
│   │       └── ApiElectionController.php
│   │
│   ├── Security/                    # Authentification & Sécurité (Adapters)
│   │   ├── UserProvider.php
│   │   ├── VoteHashGenerator.php
│   │   ├── OTPGenerator.php         # Itération 1.5 (pourrait impl OtpPort)
│   │   └── QRCodeSigner.php         # Itération 9 (pourrait impl QrSignerPort)
│   │
│   ├── Mailer/                      # Services d'envoi email (Output Adapters)
│   │   └── NotificationMailer.php   # Implémente NotificationPort
│   │
│   └── Persistence/                 # Migrations & Fixtures
│       ├── Migrations/
│       └── Fixtures/
│
└── Shared/                          # 🛠️ CODE PARTAGÉ
    ├── Event/                       # Événements domaine
    │   ├── MemberCreated.php
    │   ├── MemberVerified.php
    │   └── VoteCasted.php
    │
    ├── Validator/                   # Validateurs personnalisés
    │   ├── EmailValidator.php
    │   └── PhoneValidator.php
    │
    └── Kernel.php                   # Kernel Symfony
```

---

## � Ports & Adapters (Architecture Hexagonale)

L'architecture hexagonale repose sur la séparation claire entre **Ports** (interfaces) et **Adapters** (implémentations).

### 📥 Input Ports (Côté Gauche - Driving Side)

Les **Input Ports** définissent ce que l'application peut faire. Ils sont appelés par les adapters externes (UI, API).

#### Définition des Ports d'Entrée

```php
<?php
// Application/Port/In/CreateMemberPort.php

namespace App\Application\Port\In;

use App\Domain\Entity\Member;

interface CreateMemberPort
{
    public function execute(
        string $firstName,
        string $lastName,
        string $email,
        ?string $phone = null
    ): Member;
}
```

#### Use Cases = Implémentations des Input Ports

```php
<?php
// Application/UseCase/Member/CreateMember.php

namespace App\Application\UseCase\Member;

use App\Application\Port\In\CreateMemberPort;
use App\Domain\Entity\Member;
use App\Domain\Repository\MemberRepositoryInterface;
use App\Domain\ValueObject\Email;
use App\Domain\ValueObject\Phone;
use Ramsey\Uuid\Uuid;

class CreateMember implements CreateMemberPort
{
    public function __construct(
        private MemberRepositoryInterface $memberRepository
    ) {}

    public function execute(
        string $firstName,
        string $lastName,
        string $email,
        ?string $phone = null
    ): Member {
        $emailVO = new Email($email);
        $phoneVO = $phone ? new Phone($phone) : null;

        $member = new Member(
            Uuid::uuid4(),
            $firstName,
            $lastName,
            $emailVO,
            $phoneVO
        );

        $this->memberRepository->save($member);

        return $member;
    }
}
```

#### Input Adapters (Driving Adapters)

Les **Controllers** (Web/API) sont des adapters qui appellent les Input Ports :

```php
<?php
// Infrastructure/Web/Controller/MemberController.php

namespace App\Infrastructure\Web\Controller;

use App\Application\Port\In\CreateMemberPort;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MemberController extends AbstractController
{
    public function __construct(
        private CreateMemberPort $createMember  // Injection du Port, pas du Use Case
    ) {}

    #[Route('/members/create', name: 'member_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        try {
            $member = $this->createMember->execute(
                $request->request->get('firstName'),
                $request->request->get('lastName'),
                $request->request->get('email'),
                $request->request->get('phone')
            );

            $this->addFlash('success', 'Member created successfully!');
            
            return $this->redirectToRoute('member_show', ['id' => $member->getId()]);

        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('member_list');
        }
    }
}
```

### 📤 Output Ports (Côté Droit - Driven Side)

Les **Output Ports** définissent les dépendances dont l'application a besoin (persistence, notifications, etc.).

#### Ports de Sortie Principaux

**1. Repository Ports (déjà dans Domain/Repository/)**

```php
<?php
// Domain/Repository/MemberRepositoryInterface.php
namespace App\Domain\Repository;

use App\Domain\Entity\Member;
use Ramsey\Uuid\UuidInterface;

interface MemberRepositoryInterface  // C'est un OUTPUT PORT
{
    public function save(Member $member): void;
    public function findById(UuidInterface $id): ?Member;
    public function findByEmail(string $email): ?Member;
}
```

**2. Notification Port**

```php
<?php
// Application/Port/Out/NotificationPort.php

namespace App\Application\Port\Out;

interface NotificationPort
{
    public function send(string $to, string $subject, string $body): void;
    public function sendOtp(string $to, string $code): void;
}
```

**3. OTP Port**

```php
<?php
// Application/Port/Out/OtpPort.php

namespace App\Application\Port\Out;

interface OtpPort
{
    public function generate(int $length = 6): string;
    public function verify(string $code, string $storedCode): bool;
    public function isExpired(\DateTimeImmutable $createdAt, int $ttl = 300): bool;
}
```

**4. QR Signer Port**

```php
<?php
// Application/Port/Out/QrSignerPort.php

namespace App\Application\Port\Out;

interface QrSignerPort
{
    public function sign(array $data): string;
    public function verify(string $signature, array $data): bool;
}
```

**5. File Storage Port**

```php
<?php
// Application/Port/Out/FileStoragePort.php

namespace App\Application\Port\Out;

interface FileStoragePort
{
    public function store(string $path, string $content): string;
    public function retrieve(string $path): string;
    public function delete(string $path): void;
    public function exists(string $path): bool;
}
```

**6. Clock Port (pour tests)**

```php
<?php
// Application/Port/Out/ClockPort.php

namespace App\Application\Port\Out;

interface ClockPort
{
    public function now(): \DateTimeImmutable;
}
```

**7. Transaction Port**

```php
<?php
// Application/Port/Out/TransactionPort.php

namespace App\Application\Port\Out;

interface TransactionPort
{
    public function beginTransaction(): void;
    public function commit(): void;
    public function rollback(): void;
}
```

#### Output Adapters (Driven Adapters)

Les **Adapters** implémentent les Output Ports :

**1. Email Notification Adapter**

```php
<?php
// Infrastructure/Adapter/Notification/EmailNotificationAdapter.php

namespace App\Infrastructure\Adapter\Notification;

use App\Application\Port\Out\NotificationPort;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailNotificationAdapter implements NotificationPort
{
    public function __construct(
        private MailerInterface $mailer,
        private string $fromEmail
    ) {}

    public function send(string $to, string $subject, string $body): void
    {
        $email = (new Email())
            ->from($this->fromEmail)
            ->to($to)
            ->subject($subject)
            ->html($body);

        $this->mailer->send($email);
    }

    public function sendOtp(string $to, string $code): void
    {
        $this->send(
            $to,
            'Your OTP Code',
            "Your verification code is: <strong>{$code}</strong>"
        );
    }
}
```

**2. OTP Adapter**

```php
<?php
// Infrastructure/Adapter/Otp/SymfonyOtpAdapter.php

namespace App\Infrastructure\Adapter\Otp;

use App\Application\Port\Out\OtpPort;

class SymfonyOtpAdapter implements OtpPort
{
    public function generate(int $length = 6): string
    {
        return str_pad((string) random_int(0, 10 ** $length - 1), $length, '0', STR_PAD_LEFT);
    }

    public function verify(string $code, string $storedCode): bool
    {
        return hash_equals($storedCode, $code);
    }

    public function isExpired(\DateTimeImmutable $createdAt, int $ttl = 300): bool
    {
        $now = new \DateTimeImmutable();
        return $now->getTimestamp() - $createdAt->getTimestamp() > $ttl;
    }
}
```

**3. HMAC QR Signer Adapter**

```php
<?php
// Infrastructure/Adapter/Security/HmacQrSignerAdapter.php

namespace App\Infrastructure\Adapter\Security;

use App\Application\Port\Out\QrSignerPort;

class HmacQrSignerAdapter implements QrSignerPort
{
    public function __construct(private string $secret) {}

    public function sign(array $data): string
    {
        $payload = json_encode($data);
        return hash_hmac('sha256', $payload, $this->secret);
    }

    public function verify(string $signature, array $data): bool
    {
        return hash_equals($signature, $this->sign($data));
    }
}
```

**4. Local File Storage Adapter**

```php
<?php
// Infrastructure/Adapter/Storage/LocalFileStorageAdapter.php

namespace App\Infrastructure\Adapter\Storage;

use App\Application\Port\Out\FileStoragePort;

class LocalFileStorageAdapter implements FileStoragePort
{
    public function __construct(private string $storagePath) {}

    public function store(string $path, string $content): string
    {
        $fullPath = $this->storagePath . '/' . $path;
        $directory = dirname($fullPath);

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($fullPath, $content);
        return $path;
    }

    public function retrieve(string $path): string
    {
        $fullPath = $this->storagePath . '/' . $path;
        
        if (!file_exists($fullPath)) {
            throw new \RuntimeException("File not found: {$path}");
        }

        return file_get_contents($fullPath);
    }

    public function delete(string $path): void
    {
        $fullPath = $this->storagePath . '/' . $path;
        
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }

    public function exists(string $path): bool
    {
        return file_exists($this->storagePath . '/' . $path);
    }
}
```

**5. System Clock Adapter**

```php
<?php
// Infrastructure/Adapter/Security/SystemClockAdapter.php

namespace App\Infrastructure\Adapter\Security;

use App\Application\Port\Out\ClockPort;

class SystemClockAdapter implements ClockPort
{
    public function now(): \DateTimeImmutable
    {
        return new \DateTimeImmutable();
    }
}
```

**6. Doctrine Transaction Adapter**

```php
<?php
// Infrastructure/Adapter/Transaction/DoctrineTransactionAdapter.php

namespace App\Infrastructure\Adapter\Transaction;

use App\Application\Port\Out\TransactionPort;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineTransactionAdapter implements TransactionPort
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    public function beginTransaction(): void
    {
        $this->entityManager->beginTransaction();
    }

    public function commit(): void
    {
        $this->entityManager->commit();
    }

    public function rollback(): void
    {
        $this->entityManager->rollback();
    }
}
```

### ⚙️ Configuration Symfony (Wiring des Ports vers Adapters)

```yaml
# config/services.yaml
services:
    _defaults:
        autowire: true
        autoconfigure: true

    # === INPUT PORTS (Use Cases) ===
    App\Application\Port\In\CreateMemberPort:
        class: App\Application\UseCase\Member\CreateMember

    App\Application\Port\In\RegisterUserPort:
        class: App\Application\UseCase\Auth\RegisterUser

    # === OUTPUT PORTS (Bindings vers Adapters) ===
    
    # Repository Ports
    App\Domain\Repository\MemberRepositoryInterface:
        class: App\Infrastructure\Doctrine\Repository\DoctrineMemberRepository

    App\Domain\Repository\UserRepositoryInterface:
        class: App\Infrastructure\Doctrine\Repository\DoctrineUserRepository

    # Notification Port
    App\Application\Port\Out\NotificationPort:
        class: App\Infrastructure\Adapter\Notification\EmailNotificationAdapter
        arguments:
            $fromEmail: '%env(MAILER_FROM)%'

    # OTP Port
    App\Application\Port\Out\OtpPort:
        class: App\Infrastructure\Adapter\Otp\SymfonyOtpAdapter

    # QR Signer Port
    App\Application\Port\Out\QrSignerPort:
        class: App\Infrastructure\Adapter\Security\HmacQrSignerAdapter
        arguments:
            $secret: '%env(APP_SECRET)%'

    # File Storage Port
    App\Application\Port\Out\FileStoragePort:
        class: App\Infrastructure\Adapter\Storage\LocalFileStorageAdapter
        arguments:
            $storagePath: '%kernel.project_dir%/var/storage'

    # Clock Port
    App\Application\Port\Out\ClockPort:
        class: App\Infrastructure\Adapter\Security\SystemClockAdapter

    # Transaction Port
    App\Application\Port\Out\TransactionPort:
        class: App\Infrastructure\Adapter\Transaction\DoctrineTransactionAdapter
```

### 📊 Schéma Récapitulatif des Ports & Adapters

```
┌─────────────────────────────────────────────────────────────────┐
│                         INPUT SIDE (Left)                        │
│                      DRIVING ADAPTERS                            │
│  ┌──────────────┐    ┌──────────────┐    ┌──────────────┐      │
│  │ Web          │    │ API REST     │    │ CLI          │      │
│  │ Controllers  │    │ Controllers  │    │ Commands     │      │
│  └──────┬───────┘    └──────┬───────┘    └──────┬───────┘      │
│         │                   │                   │               │
│         └───────────────────┴───────────────────┘               │
│                             │                                   │
└─────────────────────────────┼───────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                       INPUT PORTS                                │
│  ┌────────────────────────────────────────────────────────┐     │
│  │  CreateMemberPort, RegisterUserPort, CastVotePort...  │     │
│  │  (Interfaces implemented by Use Cases)                 │     │
│  └────────────────────────────────────────────────────────┘     │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                    APPLICATION LAYER                             │
│  ┌────────────────────────────────────────────────────────┐     │
│  │  Use Cases (orchestrate business logic)               │     │
│  │  - CreateMember, RegisterUser, CastVote...            │     │
│  └────────────────────────────────────────────────────────┘     │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                      DOMAIN LAYER                                │
│  ┌────────────────────────────────────────────────────────┐     │
│  │  Entities, Value Objects, Domain Services             │     │
│  │  Pure business logic (no framework dependencies)      │     │
│  └────────────────────────────────────────────────────────┘     │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                      OUTPUT PORTS                                │
│  ┌────────────────────────────────────────────────────────┐     │
│  │  MemberRepositoryInterface, NotificationPort,          │     │
│  │  OtpPort, QrSignerPort, FileStoragePort...            │     │
│  └────────────────────────────────────────────────────────┘     │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                       OUTPUT SIDE (Right)                        │
│                       DRIVEN ADAPTERS                            │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐       │
│  │ Doctrine │  │ Mailer   │  │ File     │  │ External │       │
│  │ Repos    │  │ Service  │  │ Storage  │  │ APIs     │       │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘       │
└─────────────────────────────────────────────────────────────────┘
```

### ✅ Avantages de cette Approche

1. **Testabilité maximale**
   - Mock facile des Output Ports dans les tests
   - Tests unitaires purs du Domain sans infrastructure

2. **Indépendance technologique**
   - Changement d'ORM, mailer, storage sans toucher au Domain
   - Swap d'adapters via configuration

3. **Clarté architecturale**
   - Flux de données explicite
   - Dépendances clairement définies

4. **Facilité d'évolution**
   - Ajout de nouveaux adapters (WhatsApp, S3, etc.) sans modification du core
   - Migration progressive vers API REST

---

## �💡 Exemples de Code Détaillés

### 1️⃣ Domain Layer - Entité Member

```php
<?php

namespace App\Domain\Entity;

use App\Domain\ValueObject\Email;
use App\Domain\ValueObject\Phone;
use App\Domain\ValueObject\MemberStatus;
use Ramsey\Uuid\UuidInterface;

/**
 * Member Entity - Pure Business Logic
 * No Doctrine annotations, no framework dependencies
 */
class Member
{
    private UuidInterface $id;
    private string $firstName;
    private string $lastName;
    private Email $email;
    private ?Phone $phone;
    private MemberStatus $status;
    private \DateTimeImmutable $createdAt;
    private ?\DateTimeImmutable $verifiedAt;
    private array $documents = [];

    public function __construct(
        UuidInterface $id,
        string $firstName,
        string $lastName,
        Email $email,
        ?Phone $phone = null
    ) {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->phone = $phone;
        $this->status = MemberStatus::NOT_VERIFIED;
        $this->createdAt = new \DateTimeImmutable();
        $this->verifiedAt = null;
    }

    // Getters
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getFullName(): string
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getStatus(): MemberStatus
    {
        return $this->status;
    }

    public function isVerified(): bool
    {
        return $this->status->isVerified();
    }

    // Business Methods
    public function verifyAsStudent(): void
    {
        if ($this->isVerified()) {
            throw new \DomainException('Member is already verified');
        }

        $this->status = MemberStatus::VERIFIED_STUDENT;
        $this->verifiedAt = new \DateTimeImmutable();
    }

    public function verifyAsAlumni(): void
    {
        if ($this->isVerified()) {
            throw new \DomainException('Member is already verified');
        }

        $this->status = MemberStatus::VERIFIED_ALUMNI;
        $this->verifiedAt = new \DateTimeImmutable();
    }

    public function rejectVerification(string $reason): void
    {
        $this->status = MemberStatus::REJECTED;
        // Log rejection reason (could be stored in a separate entity)
    }

    public function canVote(): bool
    {
        return $this->isVerified();
    }

    public function addDocument(Document $document): void
    {
        $this->documents[] = $document;
    }
}
```

### 2️⃣ Domain Layer - Value Objects

```php
<?php

namespace App\Domain\ValueObject;

/**
 * MemberStatus - Immutable Value Object
 */
enum MemberStatus: string
{
    case NOT_VERIFIED = 'not_verified';
    case PENDING_VERIFICATION = 'pending_verification';
    case VERIFIED_STUDENT = 'verified_student';
    case VERIFIED_ALUMNI = 'verified_alumni';
    case REJECTED = 'rejected';
    case INACTIVE = 'inactive';

    public function isVerified(): bool
    {
        return match($this) {
            self::VERIFIED_STUDENT, self::VERIFIED_ALUMNI => true,
            default => false,
        };
    }

    public function canBeModified(): bool
    {
        return match($this) {
            self::NOT_VERIFIED, self::REJECTED => true,
            default => false,
        };
    }

    public function getLabel(): string
    {
        return match($this) {
            self::NOT_VERIFIED => 'Not Verified',
            self::PENDING_VERIFICATION => 'Pending Verification',
            self::VERIFIED_STUDENT => 'Verified Student',
            self::VERIFIED_ALUMNI => 'Verified Alumni',
            self::REJECTED => 'Rejected',
            self::INACTIVE => 'Inactive',
        };
    }
}
```

```php
<?php

namespace App\Domain\ValueObject;

/**
 * Email - Immutable Value Object with validation
 */
final class Email
{
    private string $value;

    public function __construct(string $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email format');
        }

        $this->value = strtolower($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(Email $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
```

### 3️⃣ Domain Layer - Repository Interface

```php
<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Member;
use App\Domain\ValueObject\Email;
use Ramsey\Uuid\UuidInterface;

/**
 * MemberRepositoryInterface - Port for persistence
 */
interface MemberRepositoryInterface
{
    public function save(Member $member): void;

    public function findById(UuidInterface $id): ?Member;

    public function findByEmail(Email $email): ?Member;

    public function findAll(int $page = 1, int $limit = 20): array;

    public function findVerifiedMembers(): array;

    public function delete(Member $member): void;

    public function existsByEmail(Email $email): bool;
}
```

### 4️⃣ Application Layer - Use Case

```php
<?php

namespace App\Application\UseCase\Member;

use App\Domain\Entity\Member;
use App\Domain\Repository\MemberRepositoryInterface;
use App\Domain\ValueObject\Email;
use App\Domain\ValueObject\Phone;
use App\Domain\Exception\MemberAlreadyExistsException;
use Ramsey\Uuid\Uuid;

/**
 * CreateMember Use Case
 * Orchestrates the member creation process
 */
class CreateMember
{
    public function __construct(
        private MemberRepositoryInterface $memberRepository
    ) {}

    public function execute(
        string $firstName,
        string $lastName,
        string $email,
        ?string $phone = null
    ): Member {
        // Validate business rules
        $emailVO = new Email($email);
        
        if ($this->memberRepository->existsByEmail($emailVO)) {
            throw new MemberAlreadyExistsException(
                "Member with email {$email} already exists"
            );
        }

        $phoneVO = $phone ? new Phone($phone) : null;

        // Create domain entity
        $member = new Member(
            Uuid::uuid4(),
            $firstName,
            $lastName,
            $emailVO,
            $phoneVO
        );

        // Persist
        $this->memberRepository->save($member);

        // Could dispatch domain event here
        // $this->eventDispatcher->dispatch(new MemberCreated($member));

        return $member;
    }
}
```

```php
<?php

namespace App\Application\UseCase\Member;

use App\Domain\Entity\Member;
use App\Domain\Repository\MemberRepositoryInterface;
use App\Domain\Exception\MemberNotFoundException;
use Ramsey\Uuid\UuidInterface;

/**
 * VerifyMember Use Case - Iteration 2
 */
class VerifyMember
{
    public function __construct(
        private MemberRepositoryInterface $memberRepository
    ) {}

    public function verifyAsStudent(UuidInterface $memberId): Member
    {
        $member = $this->memberRepository->findById($memberId);

        if (!$member) {
            throw new MemberNotFoundException("Member {$memberId} not found");
        }

        $member->verifyAsStudent();
        $this->memberRepository->save($member);

        return $member;
    }

    public function verifyAsAlumni(UuidInterface $memberId): Member
    {
        $member = $this->memberRepository->findById($memberId);

        if (!$member) {
            throw new MemberNotFoundException("Member {$memberId} not found");
        }

        $member->verifyAsAlumni();
        $this->memberRepository->save($member);

        return $member;
    }

    public function reject(UuidInterface $memberId, string $reason): Member
    {
        $member = $this->memberRepository->findById($memberId);

        if (!$member) {
            throw new MemberNotFoundException("Member {$memberId} not found");
        }

        $member->rejectVerification($reason);
        $this->memberRepository->save($member);

        return $member;
    }
}
```

### 5️⃣ Infrastructure Layer - Doctrine Repository

```php
<?php

namespace App\Infrastructure\Doctrine\Repository;

use App\Domain\Entity\Member;
use App\Domain\Repository\MemberRepositoryInterface;
use App\Domain\ValueObject\Email;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Ramsey\Uuid\UuidInterface;

/**
 * DoctrineMemberRepository - Adapter for Doctrine ORM
 */
class DoctrineMemberRepository implements MemberRepositoryInterface
{
    private EntityRepository $repository;

    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        $this->repository = $entityManager->getRepository(Member::class);
    }

    public function save(Member $member): void
    {
        $this->entityManager->persist($member);
        $this->entityManager->flush();
    }

    public function findById(UuidInterface $id): ?Member
    {
        return $this->repository->find($id);
    }

    public function findByEmail(Email $email): ?Member
    {
        return $this->repository->findOneBy(['email' => $email->getValue()]);
    }

    public function findAll(int $page = 1, int $limit = 20): array
    {
        return $this->repository->findBy(
            [],
            ['createdAt' => 'DESC'],
            $limit,
            ($page - 1) * $limit
        );
    }

    public function findVerifiedMembers(): array
    {
        return $this->repository->createQueryBuilder('m')
            ->where('m.status IN (:statuses)')
            ->setParameter('statuses', ['verified_student', 'verified_alumni'])
            ->orderBy('m.lastName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function delete(Member $member): void
    {
        $this->entityManager->remove($member);
        $this->entityManager->flush();
    }

    public function existsByEmail(Email $email): bool
    {
        return $this->repository->count(['email' => $email->getValue()]) > 0;
    }
}
```

### 6️⃣ Infrastructure Layer - Doctrine Mapping (XML)

```xml
<!-- config/doctrine/Member.orm.xml -->
<?xml version="1.0" encoding="UTF-8"?>
<doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                  xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                  https://www.doctrine-project.org/schemas/orm/doctrine-mapping.xsd">

    <entity name="App\Domain\Entity\Member" table="members">
        <id name="id" type="uuid" column="id">
            <generator strategy="NONE"/>
        </id>

        <field name="firstName" type="string" column="first_name" length="100"/>
        <field name="lastName" type="string" column="last_name" length="100"/>
        <field name="email" type="string" column="email" length="180" unique="true"/>
        <field name="phone" type="string" column="phone" length="20" nullable="true"/>
        <field name="status" type="string" column="status" length="30"/>
        <field name="createdAt" type="datetime_immutable" column="created_at"/>
        <field name="verifiedAt" type="datetime_immutable" column="verified_at" nullable="true"/>

        <one-to-many field="documents" target-entity="App\Domain\Entity\Document" mapped-by="member"/>
    </entity>

</doctrine-mapping>
```

### 7️⃣ Infrastructure Layer - Web Controller

```php
<?php

namespace App\Infrastructure\Web\Controller;

use App\Application\UseCase\Member\CreateMember;
use App\Application\UseCase\Member\ListMembers;
use App\Domain\Exception\MemberAlreadyExistsException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/members', name: 'member_')]
class MemberController extends AbstractController
{
    public function __construct(
        private CreateMember $createMember,
        private ListMembers $listMembers
    ) {}

    #[Route('/', name: 'list', methods: ['GET'])]
    public function list(): Response
    {
        $members = $this->listMembers->execute();

        return $this->render('member/list.html.twig', [
            'members' => $members
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            try {
                $member = $this->createMember->execute(
                    $request->request->get('firstName'),
                    $request->request->get('lastName'),
                    $request->request->get('email'),
                    $request->request->get('phone')
                );

                $this->addFlash('success', 'Member created successfully!');
                
                return $this->redirectToRoute('member_show', [
                    'id' => $member->getId()
                ]);

            } catch (MemberAlreadyExistsException $e) {
                $this->addFlash('error', $e->getMessage());
            } catch (\InvalidArgumentException $e) {
                $this->addFlash('error', 'Invalid data: ' . $e->getMessage());
            }
        }

        return $this->render('member/create.html.twig');
    }
}
```

### 8️⃣ Infrastructure Layer - Services Configuration

```yaml
# config/services.yaml
services:
    _defaults:
        autowire: true
        autoconfigure: true

    # Domain Services
    App\Domain\Service\:
        resource: '../src/Domain/Service'

    # Application Use Cases
    App\Application\UseCase\:
        resource: '../src/Application/UseCase'

    # Infrastructure
    App\Infrastructure\:
        resource: '../src/Infrastructure'
        exclude:
            - '../src/Infrastructure/Doctrine/Mapping'

    # Repository Bindings
    App\Domain\Repository\MemberRepositoryInterface:
        class: App\Infrastructure\Doctrine\Repository\DoctrineMemberRepository

    App\Domain\Repository\UserRepositoryInterface:
        class: App\Infrastructure\Doctrine\Repository\DoctrineUserRepository

    # Security Services
    App\Infrastructure\Security\OTPGenerator:
        arguments:
            $ttl: 300 # 5 minutes

    App\Infrastructure\Security\QRCodeSigner:
        arguments:
            $secret: '%env(APP_SECRET)%'
```

---

## 🔄 Flux de Données (Data Flow)

```
┌─────────────────────────────────────────────────────────────┐
│                         USER REQUEST                         │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                    INFRASTRUCTURE LAYER                      │
│  ┌───────────────────────────────────────────────────────┐  │
│  │  Controller (Web/API)                                 │  │
│  │  - Receives HTTP Request                              │  │
│  │  - Validates Input                                    │  │
│  │  - Calls Use Case                                     │  │
│  └───────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                     APPLICATION LAYER                        │
│  ┌───────────────────────────────────────────────────────┐  │
│  │  Use Case                                             │  │
│  │  - Orchestrates Business Logic                        │  │
│  │  - Calls Domain Services                              │  │
│  │  - Manages Transactions                               │  │
│  └───────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                       DOMAIN LAYER                           │
│  ┌───────────────────────────────────────────────────────┐  │
│  │  Entities + Value Objects + Business Rules            │  │
│  │  - Pure Business Logic                                │  │
│  │  - No Framework Dependencies                          │  │
│  │  - Domain Events                                      │  │
│  └───────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                    INFRASTRUCTURE LAYER                      │
│  ┌───────────────────────────────────────────────────────┐  │
│  │  Repository Implementation (Doctrine)                 │  │
│  │  - Persistence Logic                                  │  │
│  │  - Database Queries                                   │  │
│  │  - ORM Mapping                                        │  │
│  └───────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
                        DATABASE
```

---

## ✅ Avantages de cette Architecture

### 1. **Testabilité**
```php
// Test unitaire pur du Domain, sans framework
public function testMemberCanBeVerified(): void
{
    $member = new Member(
        Uuid::uuid4(),
        'John',
        'Doe',
        new Email('john@example.com')
    );

    $member->verifyAsStudent();

    $this->assertTrue($member->isVerified());
    $this->assertEquals(MemberStatus::VERIFIED_STUDENT, $member->getStatus());
}
```

### 2. **Indépendance du Framework**
- Le Domain ne connaît pas Symfony
- Possibilité de changer de framework sans toucher au métier
- Business logic réutilisable

### 3. **Maintenabilité**
- Séparation claire des responsabilités
- Code organisé et prévisible
- Facilite le travail en équipe

### 4. **Évolutivité**
- Ajout de nouveaux Use Cases facilité
- Migration vers API REST simple
- Ajout de nouvelles fonctionnalités sans casser l'existant

---

## 🎯 Règles d'Or

### ✅ À FAIRE
- Domain entities = POPO (Plain Old PHP Objects)
- Use Cases orchestrent, ne contiennent pas de logique métier
- Repository interfaces dans le Domain
- Mapping Doctrine séparé (XML/YAML)

### ❌ À ÉVITER
- Annotations Doctrine dans les entités Domain
- Dépendances Symfony dans le Domain
- Logique métier dans les Controllers
- Couplage fort entre les couches

---

## 📚 Ressources

- [Hexagonal Architecture par Alistair Cockburn](https://alistair.cockburn.us/hexagonal-architecture/)
- [Clean Architecture par Robert C. Martin](https://blog.cleancoder.com/uncle-bob/2012/08/13/the-clean-architecture.html)
- [DDD avec Symfony](https://symfony.com/doc/current/best_practices.html)
