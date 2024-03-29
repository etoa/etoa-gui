<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Text\TextRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TextController extends AbstractAdminController
{
    public function __construct(
        private TextRepository $textRepository
    ) {
    }

    #[Route("/admin/texts/", name: "admin.texts")]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function overview(): Response
    {
        $texts = [];
        foreach ($this->textRepository->getAllTextIDs() as $id) {
            $texts[] = $this->textRepository->find($id);
        }

        return $this->render('admin/texts/overview.html.twig', [
            'texts' => $texts,
        ]);
    }

    #[Route("/admin/texts/{id}/edit", name: "admin.texts.edit")]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function edit(Request $request, string $id): Response
    {
        if ($this->textRepository->isValidTextId($id)) {
            $text = $this->textRepository->find($id);
            if ($request->isMethod('POST')) {
                if ($request->request->has('save')) {
                    $text->content = $request->request->get('content');
                    $this->textRepository->save($text);
                } elseif ($request->request->has('reset')) {
                    $this->textRepository->reset($id);
                    $text = $this->textRepository->find($id);
                }
            }

            return $this->render('admin/texts/edit.html.twig', [
                'subtitle' => 'Text bearbeiten',
                'text' => $text,
            ]);
        }

        return $this->render('admin/texts/edit.html.twig', [
            'subtitle' => 'Text bearbeiten',
        ]);
    }

    #[Route("/admin/texts/{id}/preview", name: "admin.texts.preview")]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function preview(string $id): Response
    {
        if ($this->textRepository->isValidTextId($id)) {
            return $this->render('admin/texts/preview.html.twig', [
                'subtitle' => $this->textRepository->getLabel($id),
                'text' => $this->textRepository->find($id),
            ]);
        }

        return $this->render('admin/texts/preview.html.twig', [
            'subtitle' => 'Textvorschau',
        ]);
    }

    #[Route("/admin/texts/{id}/enable", name: "admin.texts.enable")]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function enable(string $id): RedirectResponse
    {
        if ($this->textRepository->isValidTextId($id)) {
            $text = $this->textRepository->find($id);
            $text->enabled = true;
            $this->textRepository->save($text);
        }

        return $this->redirectToRoute('admin.texts');
    }

    #[Route("/admin/texts/{id}/disable", name: "admin.texts.disable")]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function disable(string $id): RedirectResponse
    {
        if ($this->textRepository->isValidTextId($id)) {
            $text = $this->textRepository->find($id);
            $text->enabled = false;
            $this->textRepository->save($text);
        }

        return $this->redirectToRoute('admin.texts');
    }
}
