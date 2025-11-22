<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Text\TextRepository;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TextController extends AbstractAdminController
{
    public function __construct(
        private readonly TextRepository $textRepository
    )
    {
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

            $form = $this->createFormBuilder($text)
                ->add('content', TextareaType::class, [
                    'attr' => [
                        'rows'=>28,
                        'cols'=>100
                    ]
                ])
                ->add('submit', SubmitType::class, [
                    'label' => 'Übernehmen',
                    'attr' => [
                        'class' => "positive"
                    ]
                ])

                ->getForm()
                ->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->textRepository->save();
            }

            return $this->render('admin/texts/edit.html.twig', [
                'subtitle' => 'Text bearbeiten',
                'form' => $form
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
            $text->setEnabled(true);

            $this->textRepository->save();
        }

        return $this->redirectToRoute('admin.texts');
    }

    #[Route("/admin/texts/{id}/disable", name: "admin.texts.disable")]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function disable(string $id): RedirectResponse
    {
        if ($this->textRepository->isValidTextId($id)) {
            $text = $this->textRepository->find($id);
            $text->setEnabled(false);
            $this->textRepository->save();
        }

        return $this->redirectToRoute('admin.texts');
    }
}
