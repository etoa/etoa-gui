<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Alliance\AllianceNewsRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\AllianceNews;
use EtoA\Form\Type\Admin\AllianceNewsCleanupType;
use EtoA\Form\Type\Admin\AllianceNewsDefaultBanType;
use EtoA\Form\Type\Admin\AllianceNewsEditType;
use EtoA\User\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AllianceNewsController extends AbstractAdminController
{
    public function __construct(
        private readonly AllianceNewsRepository $allianceNewsRepository,
        private readonly UserRepository         $userRepository,
        private readonly ConfigurationService   $config
    )
    {
    }

    #[Route('/admin/alliances/news', name: 'admin.alliances.news', priority: 10)]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function news(Request $request): Response
    {
        $cleanupForm = $this->createForm(AllianceNewsCleanupType::class);
        $cleanupForm->handleRequest($request);
        if ($cleanupForm->isSubmitted() && $cleanupForm->isValid()) {
            $deleted = $this->allianceNewsRepository->deleteOlderThan(time() - $cleanupForm->getData()['timespan']);
            $this->addFlash('success', $deleted . " Beiträge wurden gelöscht!");
        }

        $defaultBanForm = $this->createForm(AllianceNewsDefaultBanType::class);
        $defaultBanForm->handleRequest($request);
        if ($defaultBanForm->isSubmitted() && $defaultBanForm->isValid()) {
            $this->config->set('townhall_ban', $defaultBanForm->getData()['timespan'], $defaultBanForm->getData()['reason']);
            $this->addFlash('success', "Einstellungen gespeichert!");
        }

        return $this->render('admin/alliance/news.html.twig', [
            'cleanupForm' => $cleanupForm->createView(),
            'defaultBanForm' => $defaultBanForm->createView(),
            'newsEntries' => $this->allianceNewsRepository->getNewsEntries(),
        ]);
    }

    #[Route('/admin/alliances/news/{id}/edit', name: 'admin.alliances.news.edit')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function edit(AllianceNews $news, Request $request): Response
    {
        $form = $this->createForm(AllianceNewsEditType::class, $news);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->allianceNewsRepository->save();

            $this->addFlash('success', "Beitrag wurde aktualisiert!");

            return $this->redirectToRoute('admin.alliances.news');
        }

        return $this->render('admin/alliance/news-edit.html.twig', [
            'form' => $form->createView(),
            'news' => $news,
        ]);
    }

    #[Route('/admin/alliances/news/{id}/delete', name: 'admin.alliances.news.delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function delete(AllianceNews $news): Response
    {
        $this->allianceNewsRepository->remove($news);
        $this->allianceNewsRepository->save();
        $this->addFlash('success', "Beitrag wurde gelöscht!");

        return $this->redirectToRoute('admin.alliances.news');
    }

    #[Route('/admin/alliances/news/{id}/ban', name: 'admin.alliances.news.ban', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function ban(AllianceNews $news): Response
    {
        $t1 = time();
        $t2 = $t1 + $this->config->getInt('townhall_ban');
        $this->userRepository->blockUser($news->getAuthor(), $t1, $t2, $this->config->param1('townhall_ban'), $this->getUser()->getData());
        $this->addFlash('success', "Der Benutzer wurde gesperrt!");

        return $this->redirectToRoute('admin.alliances.news');
    }
}
