<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Admin\AdminUser;
use EtoA\Admin\AdminUserRepository;
use EtoA\Form\Type\Admin\AdminUserType;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Security\Admin\CurrentAdmin;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdminManagementController extends AbstractAdminController
{
    public function __construct(
        private AdminUserRepository $adminUserRepository,
        private LogRepository $logRepository,
    ) {
    }

    #[Route('/admin/admin-management/', name: 'admin.admin_management')]
    public function index(): Response
    {
        return $this->render('admin/adminmanagement/list.html.twig', [
            'admins' => $this->adminUserRepository->findAll(),
        ]);
    }

    #[Route('/admin/admin-management/new', name: 'admin.admin_management.new')]
    #[IsGranted('ROLE_ADMIN_SUPER-ADMIN')]
    public function new(Request $request): Response
    {
        $admin = new AdminUser();
        $form = $this->createForm(AdminUserType::class, $admin);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->adminUserRepository->save($admin);

            $this->addFlash('success', "Gespeichert!");
            $this->logRepository->add(LogFacility::ADMIN, LogSeverity::INFO, "Der Administrator " . $this->getUser()->getUsername() . " erstellt einen neuen Administrator: " . $admin->nick . "(" . $admin->id . ").");

            return $this->redirectToRoute('admin.admin_management');
        }

        return $this->render('admin/adminmanagement/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/admin-management/{id}/edit', name: 'admin.admin_management.edit')]
    #[IsGranted('ROLE_ADMIN_SUPER-ADMIN')]
    public function edit(Request $request, int $id, UserPasswordHasherInterface $passwordHasher): Response
    {
        $admin = $this->adminUserRepository->find($id);
        $form = $this->createForm(AdminUserType::class, $admin);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ((bool) $admin->passwordString) {
                $this->adminUserRepository->setPassword($admin, $passwordHasher->hashPassword(new CurrentAdmin($admin), $admin->passwordString));
                $this->logRepository->add(LogFacility::ADMIN, LogSeverity::INFO, "Der Administrator " . $this->getUser()->getUsername() . " ändert das Passwort des Administrators " . $admin->nick . "(" . $admin->id . ").");
            }

            if ($form->has('tfa_remove') && (bool) $form->get('tfa_remove')->getData()) {
                $admin->tfaSecret = "";
                $this->logRepository->add(LogFacility::ADMIN, LogSeverity::INFO, "Der Administrator " . $this->getUser()->getUsername() . " deaktiviert die Zwei-Faktor-Authentifizierung des Administrators " . $admin->nick . "(" . $admin->id . ").");
            }

            $this->adminUserRepository->save($admin);

            $this->addFlash('success', "Gespeichert!");
            $this->logRepository->add(LogFacility::ADMIN, LogSeverity::INFO, "Der Administrator " . $this->getUser()->getUsername() . " ändert die Daten des Administrators " . $admin->nick . " (ID: " . $admin->id . ").");

            return $this->redirectToRoute('admin.admin_management');
        }

        return $this->render('admin/adminmanagement/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/admin-management/{id}/delete', name: 'admin.admin_management.delete')]
    #[IsGranted('ROLE_ADMIN_SUPER-ADMIN')]
    public function delete(int $id): Response
    {
        $admin = $this->adminUserRepository->find($id);
        if ($admin != null && $this->adminUserRepository->remove($admin)) {
            $this->logRepository->add(LogFacility::ADMIN, LogSeverity::INFO, "Der Administrator " . $this->getUser()->getUsername() . " löscht den Administrator " . $admin->nick . " (ID: " . $admin->id . ").");
            $this->addFlash('success', 'Benutzer gelöscht!');
        }

        return $this->redirectToRoute('admin.admin_management');
    }
}
