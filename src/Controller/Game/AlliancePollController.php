<?php

namespace EtoA\Controller\Game;

use EtoA\Alliance\AlliancePollRepository;
use EtoA\Alliance\AllianceRights;
use EtoA\Alliance\AllianceService;
use EtoA\Entity\AlliancePoll;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;

class AlliancePollController extends AbstractGameController
{
    public function __construct(
        private readonly AlliancePollRepository $alliancePollRepository,
        private readonly AllianceService $service
    )
    {}

    #[Route('/game/alliance/polls/overview', name: 'game.alliance.polls.overview')]
    public function overview(): Response
    {
        $cu = $this->getUser()->getData();
        $userAlliancePermission = $this->service->getUserAlliancePermissions($cu->getAlliance(), $cu);

        if ($userAlliancePermission->checkHasRights(AllianceRights::POLLS, 'alliance')) {
            return $this->render('game/alliance/polls/alliance_polls_overview.html.twig',[
                'polls' => $this->alliancePollRepository->getPolls($this->getUser()->getData()->getAlliance())
            ]);
        }

        return $this->render('game/error.html.twig',[
            'msg' => 'Fehlende Berechtigung!',
            'path' => $this->generateUrl('game.alliance.overview'),
            'headline' => 'Allianz'
        ]);
    }

    #[Route('/game/alliance/polls/create', name: 'game.alliance.polls.create')]
    public function create(Request $request): Response
    {
        $cu = $this->getUser()->getData();
        $userAlliancePermission = $this->service->getUserAlliancePermissions($cu->getAlliance(), $cu);

        if ($userAlliancePermission->checkHasRights(AllianceRights::POLLS, 'alliance')) {
            $poll = new AlliancePoll();
            $form = $this->generateForm($poll)->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $poll->setAlliance($cu->getAlliance());
                $poll->setTimestamp(time());

                $this->alliancePollRepository->persist($poll);
                $this->alliancePollRepository->save();

                return $this->render('game/success.html.twig',[
                    'msg' => 'Umfrage wurde gespeichert!',
                    'path' => $this->generateUrl('game.alliance.polls.overview'),
                    'headline' => 'Allianz'
                ]);
            }

            return $this->render('game/alliance/polls/alliance_polls_create.html.twig',[
                'form' => $form
            ]);
        }

        return $this->render('game/error.html.twig',[
            'msg' => 'Fehlende Berechtigung!',
            'path' => $this->generateUrl('game.alliance.overview'),
            'headline' => 'Allianz'
        ]);
    }

    #[Route('/game/alliance/polls/edit/{id}', name: 'game.alliance.polls.edit')]
    public function edit(Request $request, ?AlliancePoll $alliancePoll = null): Response
    {
        $cu = $this->getUser()->getData();
        $userAlliancePermission = $this->service->getUserAlliancePermissions($cu->getAlliance(), $cu);

        if ($userAlliancePermission->checkHasRights(AllianceRights::POLLS, 'alliance')) {
            if($alliancePoll && $alliancePoll->getAlliance() === $cu->getAlliance()) {
                $form = $this->generateForm($alliancePoll)->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $this->alliancePollRepository->save();

                    return $this->render('game/success.html.twig',[
                        'msg' => 'Umfrage wurde gespeichert!',
                        'path' => $this->generateUrl('game.alliance.polls.overview'),
                        'headline' => 'Allianz'
                    ]);
                }

                return $this->render('game/alliance/polls/alliance_polls_edit.html.twig',[
                    'form' => $form
                ]);
            }

            return $this->render('game/error.html.twig',[
                'msg' => 'Datensatz nicht gefunden!',
                'path' => $this->generateUrl('game.alliance.polls.overview'),
                'headline' => 'Allianz'
            ]);
        }

        return $this->render('game/error.html.twig',[
            'msg' => 'Fehlende Berechtigung!',
            'path' => $this->generateUrl('game.alliance.overview'),
            'headline' => 'Allianz'
        ]);
    }

    #[Route('/game/alliance/polls/delete/{id}', name: 'game.alliance.polls.delete')]
    public function delete(?AlliancePoll $alliancePoll = null): Response
    {
        $cu = $this->getUser()->getData();
        $userAlliancePermission = $this->service->getUserAlliancePermissions($cu->getAlliance(), $cu);

        if ($userAlliancePermission->checkHasRights(AllianceRights::POLLS, 'alliance')) {
            if ($alliancePoll && $alliancePoll->getAlliance() === $cu->getAlliance()) {
                $this->alliancePollRepository->deletePoll($alliancePoll);

                return $this->render('game/success.html.twig',[
                    'msg' => 'Umfrage wurde gelöscht!',
                    'path' => $this->generateUrl('game.alliance.polls.overview'),
                    'headline' => 'Allianz'
                ]);

            }

            return $this->render('game/error.html.twig', [
                'msg' => 'Datensatz nicht gefunden!',
                'path' => $this->generateUrl('game.alliance.polls.overview'),
                'headline' => 'Allianz'
            ]);
        }

        return $this->render('game/error.html.twig', [
            'msg' => 'Fehlende Berechtigung!',
            'path' => $this->generateUrl('game.alliance.overview'),
            'headline' => 'Allianz'
        ]);
    }

    #[Route('/game/alliance/polls/deactivate/{id}', name: 'game.alliance.polls.deactivate')]
    public function deactivate(?AlliancePoll $alliancePoll = null): Response
    {
        $cu = $this->getUser()->getData();
        $userAlliancePermission = $this->service->getUserAlliancePermissions($cu->getAlliance(), $cu);

        if ($userAlliancePermission->checkHasRights(AllianceRights::POLLS, 'alliance')) {
            if ($alliancePoll && $alliancePoll->getAlliance() === $cu->getAlliance()) {
                $this->alliancePollRepository->updateActive($alliancePoll,false);

                return $this->redirectToRoute('game.alliance.polls.overview');
            }

            return $this->render('game/error.html.twig', [
                'msg' => 'Datensatz nicht gefunden!',
                'path' => $this->generateUrl('game.alliance.polls.overview'),
                'headline' => 'Allianz'
            ]);
        }

        return $this->render('game/error.html.twig', [
            'msg' => 'Fehlende Berechtigung!',
            'path' => $this->generateUrl('game.alliance.overview'),
            'headline' => 'Allianz'
        ]);
    }

    #[Route('/game/alliance/polls/activate/{id}', name: 'game.alliance.polls.activate')]
    public function activate(?AlliancePoll $alliancePoll = null): Response
    {
        $cu = $this->getUser()->getData();
        $userAlliancePermission = $this->service->getUserAlliancePermissions($cu->getAlliance(), $cu);

        if ($userAlliancePermission->checkHasRights(AllianceRights::POLLS, 'alliance')) {
            if ($alliancePoll && $alliancePoll->getAlliance() === $cu->getAlliance()) {
                $this->alliancePollRepository->updateActive($alliancePoll,true);

                return $this->redirectToRoute('game.alliance.polls.overview');
            }

            return $this->render('game/error.html.twig', [
                'msg' => 'Datensatz nicht gefunden!',
                'path' => $this->generateUrl('game.alliance.polls.overview'),
                'headline' => 'Allianz'
            ]);
        }

        return $this->render('game/error.html.twig', [
            'msg' => 'Fehlende Berechtigung!',
            'path' => $this->generateUrl('game.alliance.overview'),
            'headline' => 'Allianz'
        ]);
    }

    private function generateForm(AlliancePoll $poll): FormInterface
    {
        return $this->createFormBuilder($poll)
            ->add('title', TextType::class, [
                'attr' => [
                    'size' => 80,
                    'maxlength' => 150,
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Kein Text angegeben!']),
                ],
            ])
            ->add('question', TextType::class, [
                'attr' => [
                    'size' => 80,
                    'maxlength' => 150,
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Kein Text angegeben!']),
                ],
            ])
            ->add('answer1', TextType::class, [
                'attr' => [
                    'size' => 70,
                    'maxlength' => 150,
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Kein Text angegeben!']),
                ],
            ])
            ->add('answer2', TextType::class, [
                'attr' => [
                    'size' => 70,
                    'maxlength' => 150,
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Kein Text angegeben!']),
                ],
            ])
            ->add('answer3', TextType::class, [
                'attr' => [
                    'size' => 70,
                    'maxlength' => 150,
                ],
                'required' => false,
                'empty_data' => ''
            ])
            ->add('answer4', TextType::class, [
                'attr' => [
                    'size' => 70,
                    'maxlength' => 150,
                ],
                'required' => false,
                'empty_data' => ''
            ])
            ->add('answer5', TextType::class, [
                'attr' => [
                    'size' => 70,
                    'maxlength' => 150,
                ],
                'required' => false,
                'empty_data' => ''
            ])
            ->add('answer6', TextType::class, [
                'attr' => [
                    'size' => 70,
                    'maxlength' => 150,
                ],
                'required' => false,
                'empty_data' => ''
            ])
            ->add('answer7', TextType::class, [
                'attr' => [
                    'size' => 70,
                    'maxlength' => 150,
                ],
                'required' => false,
                'empty_data' => ''
            ])
            ->add('answer8', TextType::class, [
                'attr' => [
                    'size' => 70,
                    'maxlength' => 150,
                ],
                'required' => false,
                'empty_data' => ''
            ])
            ->add('send', SubmitType::class, [
                'label' => 'Speichern'
            ])
            ->getForm();
    }
}