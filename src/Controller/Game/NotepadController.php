<?php

namespace EtoA\Controller\Game;

use EtoA\Entity\Notepad;
use EtoA\Entity\NotepadData;
use EtoA\Notepad\NotepadDataRepository;
use EtoA\Notepad\NotepadRepository;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NotepadController extends AbstractGameController
{
    public function __construct(
        private readonly NotepadRepository $notepadRepository,
        private readonly NotepadDataRepository $notepadDataRepository
    )
    {
    }

    #[Route('/game/notepad/list', name: 'game.notepad.list')]
    public function list(): Response
    {
        $list = $this->notepadRepository->findBy(['user'=>$this->getUser()->getData()]);

        return $this->render('game/notepad/notepad_list.html.twig',[
            'list' => $list
        ]);
    }

    #[Route('/game/notepad/new', name: 'game.notepad.new')]
    public function new(Request $request): Response
    {
        $note = new NotepadData();
        $form = $this->createFormBuilder($note)
            ->add('text', TextType::class, [
                'label' => false,
                'attr' => ['size' => 40]
            ])
            ->add('subject', TextareaType::class, [
                'label' => false,
                'attr' => [
                    'cols' => 50,
                    'rows' => 10,
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Speichern'
            ])
            ->getForm()
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->notepadDataRepository->add($note, $this->getUser()->getData());
            return $this->redirectToRoute('game.notepad.list');
        }

        return $this->render('game/notepad/notepad_new.html.twig',[
            'form' => $form
        ]);
    }

    #[Route('/game/notepad/delete/{id}', name: 'game.notepad.delete')]
    public function delete(Notepad $notepad): Response
    {
        if($notepad->getUser() === $this->getUser()->getData()) {
            $this->notepadRepository->remove($notepad);
            $this->notepadRepository->save();

            return $this->redirectToRoute('game.notepad.list');
        }

        return $this->render('game/error.html.twig', [
            'msg' => 'Notiz nicht gefunden!',
            'path' => $this->generateUrl('game.notepad.list'),
            'headline' => 'Notizen'
        ]);
    }

    #[Route('/game/notepad/edit/{id}', name: 'game.notepad.edit')]
    public function edit(Notepad $notepad, Request $request): Response
    {
        if($notepad->getUser() === $this->getUser()->getData()) {

            $form = $this->createFormBuilder($notepad->getData())
                ->add('text', TextType::class, [
                    'label' => false,
                    'attr' => ['size' => 40]
                ])
                ->add('subject', TextareaType::class, [
                    'label' => false,
                    'attr' => [
                        'cols' => 50,
                        'rows' => 10,
                    ]
                ])
                ->add('save', SubmitType::class, [
                    'label' => 'Speichern'
                ])
                ->getForm()
                ->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $notepad->setTimestamp(time());
                $this->notepadRepository->save();

                return $this->redirectToRoute('game.notepad.list');
            }

            return $this->render('game/notepad/notepad_edit.html.twig',[
                'form' => $form
            ]);
        }

        return $this->render('game/error.html.twig', [
            'msg' => 'Notiz nicht gefunden!',
            'path' => $this->generateUrl('game.notepad.list'),
            'headline' => 'Notizen'
        ]);
    }
}