<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Admin\AdminUserRepository;
use EtoA\Entity\AdminUser;
use EtoA\Form\Type\Admin\AdminUserType;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Security\Admin\CurrentAdmin;
use phpDocumentor\Reflection\Types\This;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AdminManagementController extends AbstractAdminController
{
    public function __construct(
        private readonly AdminUserRepository $adminUserRepository,
        private readonly LogRepository       $logRepository,
    )
    {
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
            $this->adminUserRepository->persist($admin);
            $this->adminUserRepository->save();

            $this->addFlash('success', "Gespeichert!");
            $this->logRepository->add(LogFacility::ADMIN, LogSeverity::INFO, "Der Administrator " . $this->getUser()->getUsername() . " erstellt einen neuen Administrator: " . $admin->getNick() . "(" . $admin->getId() . ").");

            return $this->redirectToRoute('admin.admin_management');
        }

        return $this->render('admin/adminmanagement/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/admin-management/{id}/edit', name: 'admin.admin_management.edit')]
    #[IsGranted('ROLE_ADMIN_SUPER-ADMIN')]
    public function edit(Request $request, AdminUser $admin): Response
    {
        $form = $this->createForm(AdminUserType::class, $admin);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            if (array_key_exists('password',$this->adminUserRepository->getChangeset($admin))) {
                $this->logRepository->add(LogFacility::ADMIN, LogSeverity::INFO, "Der Administrator " . $this->getUser()->getUsername() . " ändert das Passwort des Administrators " . $admin->getNick() . "(" . $admin->getId() . ").");
            }

            if ($form->has('tfa_remove') && $form->get('tfa_remove')->getData()) {
                $admin->setTfaSecret('');
                $this->logRepository->add(LogFacility::ADMIN, LogSeverity::INFO, "Der Administrator " . $this->getUser()->getUsername() . " deaktiviert die Zwei-Faktor-Authentifizierung des Administrators " . $admin->getNick() . "(" . $admin->getId() . ").");
            }

            $this->adminUserRepository->save();

            $this->addFlash('success', "Gespeichert!");
            $this->logRepository->add(LogFacility::ADMIN, LogSeverity::INFO, "Der Administrator " . $this->getUser()->getUsername() . " ändert die Daten des Administrators " . $admin->getNick() . " (ID: " . $admin->getId() . ").");

            return $this->redirectToRoute('admin.admin_management');
        }

        return $this->render('admin/adminmanagement/edit.html.twig', [
            'form' => $form->createView(),
            'admin' => $admin,
        ]);
    }

    #[Route('/admin/admin-management/{id}/delete', name: 'admin.admin_management.delete')]
    #[IsGranted('ROLE_ADMIN_SUPER-ADMIN')]
    public function delete(AdminUser $admin): Response
    {
        $this->logRepository->add(LogFacility::ADMIN, LogSeverity::INFO, "Der Administrator " . $this->getUser()->getUsername() . " löscht den Administrator " . $admin->getNick() . " (ID: " . $admin->getId() . ").");

        $this->adminUserRepository->remove($admin);
        $this->adminUserRepository->save();

        $this->addFlash('success', 'Benutzer gelöscht!');

        return $this->redirectToRoute('admin.admin_management');
    }
}
