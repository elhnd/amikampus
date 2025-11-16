<?php

namespace App\Controller;

use App\Entity\Member;
use App\Service\MemberService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MemberController extends AbstractController
{
    public function __construct(
        private readonly MemberService $memberService
    ) {}

    #[Route('/member', name: 'app_member')]
    public function index(): Response
    {
        // Rediriger vers le profile pour l'instant
        return $this->redirectToRoute('app_member_profile');
    }

    #[Route('/member/signup', name: 'app_member_signup', methods: ['GET', 'POST'])]
    public function signup(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            try {
                $member = new Member();
                $member->setFirstName($request->request->get('firstName'));
                $member->setLastName($request->request->get('lastName'));
                $member->setEmail($request->request->get('email'));
                $member->setPhone($request->request->get('phone'));
                
                $birthDate = new \DateTime($request->request->get('birthDate'));
                $member->setBirthDate($birthDate);

                $this->memberService->saveMember($member);

                $this->addFlash('success', 'Your account has been created successfully!');
                
                return $this->redirectToRoute('app_member_login');
            } catch (\Exception $e) {
                $this->addFlash('error', 'An error occurred while creating your account. Please try again.');
            }
        }

        return $this->render('member/signup.html.twig');
    }

    #[Route('/member/login', name: 'app_member_login', methods: ['GET', 'POST'])]
    public function login(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            try {
                $email = $request->request->get('email');
                $password = $request->request->get('password');
                $remember = $request->request->get('remember');

                // TODO: Implémenter l'authentification avec Symfony Security
                // Pour l'instant, c'est une simulation
                
                // Vérifier si un membre existe avec cet email
                $member = $this->memberService->getMemberByEmail($email);
                
                if ($member) {
                    // TODO: Vérifier le mot de passe hashé
                    $this->addFlash('success', 'Welcome back, ' . $member->getFirstName() . '!');
                    return $this->redirectToRoute('app_member');
                } else {
                    $this->addFlash('error', 'Invalid email or password.');
                }
            } catch (\Exception $e) {
                $this->addFlash('error', 'An error occurred during login. Please try again.');
            }
        }

        return $this->render('member/login.html.twig');
    }

    #[Route('/member/profile', name: 'app_member_profile')]
    public function profile(): Response
    {
        // TODO: Récupérer le membre connecté depuis la session/security
        // Pour l'instant, on prend le premier membre de la DB comme exemple
        $members = $this->memberService->getMembers();
        
        if (empty($members)) {
            // Si pas de membre, créer un membre de démo
            $member = new Member();
            $member->setFirstName('John');
            $member->setLastName('Doe');
            $member->setEmail('john.doe@example.com');
            $member->setPhone('+1 234 567 8900');
            $member->setBirthDate(new \DateTime('1990-01-15'));
        } else {
            $member = $members[0];
        }

        return $this->render('member/profile.html.twig', [
            'member' => $member,
        ]);
    }

    #[Route('/member/profile/edit', name: 'app_member_edit_profile', methods: ['GET', 'POST'])]
    public function editProfile(Request $request): Response
    {
        // TODO: Récupérer le membre connecté depuis la session/security
        // Pour l'instant, on prend le premier membre de la DB comme exemple
        $members = $this->memberService->getMembers();
        
        if (empty($members)) {
            // Si pas de membre, créer un membre de démo
            $member = new Member();
            $member->setFirstName('John');
            $member->setLastName('Doe');
            $member->setEmail('john.doe@example.com');
            $member->setPhone('+1 234 567 8900');
            $member->setBirthDate(new \DateTime('1990-01-15'));
        } else {
            $member = $members[0];
        }

        // Traitement du formulaire POST
        if ($request->isMethod('POST')) {
            try {
                // Mettre à jour les informations personnelles
                $member->setFirstName($request->request->get('firstName'));
                $member->setLastName($request->request->get('lastName'));
                $member->setEmail($request->request->get('email'));
                $member->setPhone($request->request->get('phone'));
                
                // Mettre à jour la date de naissance si fournie
                $birthDateStr = $request->request->get('birthDate');
                if ($birthDateStr) {
                    $birthDate = new \DateTime($birthDateStr);
                    $member->setBirthDate($birthDate);
                }

                // TODO: Gérer l'upload de l'avatar
                // TODO: Gérer le changement de mot de passe
                // TODO: Gérer les préférences (langue, timezone, notifications)

                // Sauvegarder les modifications
                $this->memberService->updateMember($member);

                $this->addFlash('success', '✓ Votre profil a été mis à jour avec succès !');
                
                return $this->redirectToRoute('app_member_profile');
            } catch (\Exception $e) {
                $this->addFlash('error', '⚠ Une erreur est survenue lors de la mise à jour de votre profil. Veuillez réessayer.');
            }
        }

        return $this->render('member/edit-profile.html.twig', [
            'member' => $member,
        ]);
    }

    #[Route('/member/events', name: 'app_member_events')]
    public function events(): Response
    {
        // TODO: Récupérer le membre connecté depuis la session/security
        // Pour l'instant, on prend le premier membre de la DB comme exemple
        $members = $this->memberService->getMembers();
        
        if (empty($members)) {
            // Si pas de membre, créer un membre de démo
            $member = new Member();
            $member->setFirstName('John');
            $member->setLastName('Doe');
            $member->setEmail('john.doe@example.com');
            $member->setPhone('+1 234 567 8900');
            $member->setBirthDate(new \DateTime('1990-01-15'));
        } else {
            $member = $members[0];
        }

        // TODO: Récupérer les événements depuis la base de données
        // Pour l'instant, afficher la page avec données statiques dans le template
        
        return $this->render('member/events.html.twig', [
            'member' => $member,
        ]);
    }

    #[Route('/member/event/{id}', name: 'app_member_event_detail')]
    public function eventDetail(int $id): Response
    {
        // TODO: Récupérer le membre connecté depuis la session/security
        // Pour l'instant, on prend le premier membre de la DB comme exemple
        $members = $this->memberService->getMembers();
        
        if (empty($members)) {
            // Si pas de membre, créer un membre de démo
            $member = new Member();
            $member->setFirstName('John');
            $member->setLastName('Doe');
            $member->setEmail('john.doe@example.com');
            $member->setPhone('+1 234 567 8900');
            $member->setBirthDate(new \DateTime('1990-01-15'));
        } else {
            $member = $members[0];
        }

        // TODO: Récupérer l'événement depuis la base de données avec l'ID
        // Pour l'instant, afficher la page avec données statiques dans le template
        
        return $this->render('member/event-detail.html.twig', [
            'member' => $member,
            'eventId' => $id,
        ]);
    }

    #[Route('/member/card', name: 'app_member_card')]
    public function membershipCard(): Response
    {
        // TODO: Récupérer le membre connecté depuis la session/security
        // Pour l'instant, on prend le premier membre de la DB comme exemple
        $members = $this->memberService->getMembers();
        
        if (empty($members)) {
            // Si pas de membre, créer un membre de démo
            $member = new Member();
            $member->setFirstName('John');
            $member->setLastName('Doe');
            $member->setEmail('john.doe@example.com');
            $member->setPhone('+1 234 567 8900');
            $member->setBirthDate(new \DateTime('1990-01-15'));
        } else {
            $member = $members[0];
        }

        // TODO: Récupérer les informations de la carte et cotisation depuis la base de données
        // Pour l'instant, afficher la page avec données statiques dans le template
        
        return $this->render('member/membership-card.html.twig', [
            'member' => $member,
        ]);
    }

    #[Route('/member/bureau', name: 'app_member_bureau')]
    public function bureau(): Response
    {
        // TODO: Récupérer le membre connecté depuis la session/security
        // Pour l'instant, on prend le premier membre de la DB comme exemple
        $members = $this->memberService->getMembers();
        
        if (empty($members)) {
            // Si pas de membre, créer un membre de démo
            $member = new Member();
            $member->setFirstName('John');
            $member->setLastName('Doe');
            $member->setEmail('john.doe@example.com');
            $member->setPhone('+1 234 567 8900');
            $member->setBirthDate(new \DateTime('1990-01-15'));
        } else {
            $member = $members[0];
        }

        // TODO: Récupérer les membres du bureau depuis la base de données
        // Pour l'instant, afficher la page avec données statiques dans le template
        
        return $this->render('member/bureau.html.twig', [
            'member' => $member,
        ]);
    }

    #[Route('/member/student/{id}', name: 'app_member_student_profile')]
    public function studentProfile(int $id): Response
    {
        // TODO: Récupérer le membre connecté depuis la session/security
        // Pour l'instant, on prend le premier membre de la DB comme exemple
        $members = $this->memberService->getMembers();
        
        if (empty($members)) {
            // Si pas de membre, créer un membre de démo
            $member = new Member();
            $member->setFirstName('John');
            $member->setLastName('Doe');
            $member->setEmail('john.doe@example.com');
            $member->setPhone('+1 234 567 8900');
            $member->setBirthDate(new \DateTime('1990-01-15'));
        } else {
            $member = $members[0];
        }

        // TODO: Récupérer l'étudiant depuis la base de données avec l'ID
        // Pour l'instant, afficher la page avec données statiques dans le template
        
        return $this->render('member/student-profile.html.twig', [
            'member' => $member,
            'studentId' => $id,
        ]);
    }

    #[Route('/member/directory', name: 'app_member_directory')]
    public function directory(): Response
    {
        // TODO: Récupérer le membre connecté depuis la session/security
        // Pour l'instant, on prend le premier membre de la DB comme exemple
        $members = $this->memberService->getMembers();
        
        if (empty($members)) {
            // Si pas de membre, créer un membre de démo
            $member = new Member();
            $member->setFirstName('John');
            $member->setLastName('Doe');
            $member->setEmail('john.doe@example.com');
            $member->setPhone('+1 234 567 8900');
            $member->setBirthDate(new \DateTime('1990-01-15'));
        } else {
            $member = $members[0];
        }

        // TODO: Récupérer tous les membres depuis la base de données
        // Pour l'instant, afficher la page avec données statiques dans le template
        
        return $this->render('member/directory.html.twig', [
            'member' => $member,
        ]);
    }
}
