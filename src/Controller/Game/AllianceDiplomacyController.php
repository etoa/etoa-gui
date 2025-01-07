<?php

namespace EtoA\Controller\Game;

use EtoA\Alliance\AllianceDiplomacyLevel;
use EtoA\Alliance\AllianceDiplomacyPoints;
use EtoA\Alliance\AllianceDiplomacyRepository;
use EtoA\Alliance\AllianceHistoryRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Entity\Alliance;
use EtoA\Entity\AllianceDiplomacy;
use EtoA\Message\MessageCategoryId;
use EtoA\Message\MessageCategoryRepository;
use EtoA\Message\MessageRepository;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;
use function Symfony\Component\Translation\t;

class AllianceDiplomacyController extends AbstractGameController
{
    public function __construct(
        private readonly AllianceDiplomacyRepository $allianceDiplomacyRepository,
        private readonly AllianceRepository $allianceRepository,
        private readonly MessageRepository $messageRepository,
        private readonly MessageCategoryRepository $messageCategoryRepository,
        private readonly AllianceHistoryRepository $allianceHistoryRepository
    )
    {}

    #[Route('/game/alliance/diplomacy/relations', name: 'game.alliance.diplomacy.relations')]
    public function relations(Request $request): Response
    {
        $alliance = $this->getUser()->getData()->getAlliance();
        $diplomacies = $this->allianceDiplomacyRepository->getDiplomacies($alliance);
        $relations = array();

        if (count($diplomacies) > 0) {
            foreach ($diplomacies as $diplomacy) {
                $relations[$alliance === $diplomacy->getAlliance2() ? $diplomacy->getAlliance1()->getId() : $diplomacy->getAlliance2()->getId()] = $diplomacy;
            }
        }

        // Allianzen laden
        $alliances = $this->allianceRepository->getAlliances();

        return $this->render('game/alliance/diplomacy/alliance_diplomacy_overview.html.twig',[
            'alliances' =>$alliances,
            'relations' => $relations,
        ]);
    }

    #[Route('/game/alliance/diplomacy/begin-bnd/{id}', name: 'game.alliance.diplomacy.begin_bnd')]
    public function begin_bnd(Request $request, ?Alliance $otherAlliance = null): Response
    {
        $alliance = $this->getUser()->getData()->getAlliance();
        if ($otherAlliance && $otherAlliance !== $alliance) {

            $bnd = new AllianceDiplomacy();

            $form = $this->createFormBuilder($bnd)
                ->add('name', TextType::class, [
                    'attr' => [
                        'size' => 30,
                        'maxlength' => 30,
                    ],
                    'constraints' => [
                        new NotBlank(['message' => 'Kein Name angegeben!']),
                    ],
                ])
                ->add('text', TextareaType::class, [
                    'attr' => [
                        'rows' => 10,
                        'cols' => 50,
                    ],
                    'constraints' => [
                        new NotBlank(['message' => 'Kein Text angegeben!']),
                    ],
                ])
                ->add('send', SubmitType::class, [
                    'attr' => [
                        'onclick' => 'return checkPactOffer()',
                        'onsubmit' => "return checkPactOffer()"
                    ],
                    'label' => 'Senden'
                ])
                ->getForm()
                ->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                if ($this->allianceDiplomacyRepository->existsDiplomacyBetween($alliance,$otherAlliance)) {
                    $msg['error'] = "Deine Allianz steht schon in einer Beziehung (Bündnis/Krieg) mit der ausgewählten Allianz oder es ist bereits eine Bewerbung um ein Bündnis vorhanden!";
                } else {
                    $bnd->setAlliance1($alliance);
                    $bnd->setAlliance2($otherAlliance);
                    $bnd->setLevel(AllianceDiplomacyLevel::BND_REQUEST);
                    $bnd->setDate(time());
                    $bnd->setDiplomat($this->getUser()->getData());

                    $this->allianceDiplomacyRepository->persist($bnd);
                    $this->allianceDiplomacyRepository->save();

                    $msg['success'] = "Du hast einer Allianz erfolgreich ein Bündnis angeboten!";
                    //Nachricht an den Leader der gegnerischen Allianz schreiben
                    $this->messageRepository->createSystemMessage($otherAlliance->getFounder(), $this->messageCategoryRepository->find(MessageCategoryId::ALLIANCE), 'Bündnisanfrage', "Die Allianz [b]" . $alliance->toString() . "[/b] fragt euch für ein Bündnis an.\n
                        [b]Text:[/b] " . addslashes($bnd->getText()) . "\n
                        Geschrieben von [b]" . $this->getUser()->getData()->getNick() . "[/b].\n Gehe auf die [page=alliance]Allianzseite[/page] um die Anfrage zu bearbeiten!");
                }
            }

            return $this->render('game/alliance/diplomacy/alliance_diplomacy_new_bnd.html.twig',[
                'otherAlliance' =>$otherAlliance,
                'form' => $form,
                'msg' => $msg??null
            ]);
        }

        return $this->render('game/error.html.twig',[
            'msg' => 'Diese Allianz existiert nicht!',
            'path' => $this->generateUrl('game.alliance.overview'),
            'headline' => 'Allianz'
        ]);
    }

    #[Route('/game/alliance/diplomacy/begin-war/{id}', name: 'game.alliance.diplomacy.begin_war')]
    public function begin_war(Request $request, ?Alliance $otherAlliance = null): Response
    {
        $alliance = $this->getUser()->getData()->getAlliance();
        if ($otherAlliance && $otherAlliance !== $alliance) {
            $war = new AllianceDiplomacy();

            $form = $this->createFormBuilder($war)
                ->add('publicText', TextAreaType::class, [
                    'attr' => [
                        'rows' => 10,
                        'cols' => 50,
                    ],
                    'constraints' => [
                        new NotBlank(['message' => 'Kein Name angegeben!']),
                    ],
                ])
                ->add('text', TextareaType::class, [
                    'attr' => [
                        'rows' => 10,
                        'cols' => 50,
                    ],
                    'constraints' => [
                        new NotBlank(['message' => 'Kein Text angegeben!']),
                    ],
                ])
                ->add('send', SubmitType::class, [
                    'attr' => [
                        'onclick' => 'return checkWarDeclaration()',
                        'onsubmit' => "return checkWarDeclaration()"
                    ],
                    'label' => 'Senden'
                ])
                ->getForm()
                ->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                if ($this->allianceDiplomacyRepository->existsDiplomacyBetween($alliance,$otherAlliance)) {
                    $msg['error'] = "Deine Allianz steht schon in einer Beziehung (Bündnis/Krieg) mit der ausgewählten Allianz oder es ist bereits eine Bewerbung um ein Bündnis vorhanden!";
                } else {
                    $war->setAlliance1($alliance);
                    $war->setAlliance2($otherAlliance);
                    $war->setLevel(AllianceDiplomacyLevel::WAR);
                    $war->setDate(time());
                    $war->setPoints(AllianceDiplomacyPoints::POINTS_PER_WAR);
                    $war->setDiplomat($this->getUser()->getData());
                    $war->setName('');

                    $this->allianceDiplomacyRepository->persist($war);
                    $this->allianceDiplomacyRepository->save();

                    $msg['success'] = "Du hast einer Allianz den Krieg erklärt!";

                    $this->allianceHistoryRepository->addEntry($alliance, "Der Allianz [b]" . $otherAlliance->toString() . "[/b] wird der Krieg erkl&auml;rt!");
                    $this->allianceHistoryRepository->addEntry($otherAlliance, "Die Allianz [b]" . $alliance->toString() . "[/b] erkl&auml;rt den Krieg!");

                    //Nachricht an den Leader der gegnerischen Allianz schreiben
                    $this->messageRepository->createSystemMessage($otherAlliance->getFounder(), $this->messageCategoryRepository->find(MessageCategoryId::ALLIANCE), 'Kriegserklärung', "Die Allianz [b]" . $alliance->toString() . "[/b] erklärt euch den Krieg!\n
                        Die Kriegserklärung wurde von [b]" . $this->getUser()->getData()->getNick() . "[/b] geschrieben.\n Geh auf die Allianzseite für mehr Details!");
                }
            }

            return $this->render('game/alliance/diplomacy/alliance_diplomacy_new_war.html.twig',[
                'otherAlliance' =>$otherAlliance,
                'form' => $form,
                'msg' => $msg??null
            ]);
        }

        return $this->render('game/error.html.twig',[
            'msg' => 'Diese Allianz existiert nicht!',
            'path' => $this->generateUrl('game.alliance.diplomacy.relations'),
            'headline' => 'Allianz'
        ]);
    }

    #[Route('/game/alliance/diplomacy/view/{id}', name: 'game.alliance.diplomacy.view')]
    public function view(Request $request, ?AllianceDiplomacy $diplomacy = null): Response
    {
        $currentAlliance = $this->getUser()->getData()->getAlliance();

        if ($diplomacy && ($diplomacy->getAlliance1() === $currentAlliance || $diplomacy->getAlliance2() === $currentAlliance)) {

            $form = $this->createFormBuilder($diplomacy);

            switch ($diplomacy->getLevel()) {
                case AllianceDiplomacyLevel::BND_REQUEST:
                    if ($diplomacy->getAlliance1() === $currentAlliance) {
                        $form = $form->add('submitWithdrawPact', SubmitType::class,[
                                'label'=>'Bündnisangebot zurückziehen',
                                'attr' => [
                                    'onclick' => "return confirm('Angebot wirklich zurückziehen?')"
                                ]
                            ]);
                    }
                    else {
                        $form = $form->add('answer', TextAreaType::class, [
                            'attr' => [
                                'rows' => 6,
                                'cols' => 70,
                            ],
                            'constraints' => [
                                new NotBlank(['message' => 'Kein Text angegeben!']),
                            ],
                            'mapped' => false
                        ])
                        ->add('acceptPact', SubmitType::class,['label'=>'Bündnisangebot annehmen'])
                        ->add('rejectPact', SubmitType::class,['label'=>'Bündnisangebot ablehnen']);
                    }
                    break;
                case AllianceDiplomacyLevel::BND_CONFIRMED:
                    $form = $form->add('publicText', TextAreaType::class, [
                        'attr' => [
                            'rows' => 6,
                            'cols' => 70,
                        ],
                        'constraints' => [
                            new NotBlank(['message' => 'Kein Text angegeben!']),
                        ],
                    ])
                    ->add('submitPublicText', SubmitType::class,['label'=>'Speichern']);
                    break;
                case AllianceDiplomacyLevel::WAR:
                    if ($diplomacy->getAlliance1() === $currentAlliance) {
                        $form = $form->add('publicText', TextAreaType::class, [
                            'attr' => [
                                'rows' => 6,
                                'cols' => 70,
                            ],
                            'constraints' => [
                                new NotBlank(['message' => 'Kein Text angegeben!']),
                            ],
                        ])
                        ->add('submitPublicText', SubmitType::class,['label'=>'Speichern']);
                    }
                    break;
            }

            $form = $form->getForm()
                         ->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // Withdraw pact offer
                if($form->has('submitWithdrawPact') && $form->get('submitWithdrawPact')->isClicked()) {
                    $this->allianceDiplomacyRepository->remove($diplomacy);

                    // Inform opposite leader
                    $this->messageRepository->createSystemMessage($diplomacy->getAlliance2()->getFounder(),  $this->messageCategoryRepository->find(MessageCategoryId::ALLIANCE), "Anfrage zurückgenommen", "Die Allianz [b]" . $diplomacy->getAlliance1()->getName() . "[/b] hat ihre Büdnisanfrage wieder zurückgezogen.");

                    // Display message
                    return $this->render('game/success.html.twig',[
                        'msg' => 'Anfrage gelöscht! Die Allianzleitung der Allianz <b>' . $diplomacy->getAlliance2()->getName() . '</b> wurde per Nachricht darüber informiert.<br/><br/>',
                        'path' => $this->generateUrl('game.alliance.diplomacy.relations'),
                        'headline' => 'Allianz'
                    ]);
                }

                // Accept pact offer
                if($form->has('acceptPact') && $form->get('acceptPact')->isClicked()) {
                    // Send message to alliance leader
                    $text = "Das Bündnis [b]" . $diplomacy->getName() . "[/b] zwischen den Allianzen [b][" . $diplomacy->getAlliance1()->getTag() . "] " . $diplomacy->getAlliance1()->getName() . "[/b] und [b][" . $diplomacy->getAlliance2()->getTag(). "] " . $diplomacy->getAlliance2()->getName() . "[/b] ist zustande gekommen!\n\nBitte denke daran, einen öffentlichen Text zum Bündnis hinzuzufügen!\n[b]Nachricht:[/b] " . $form->get('answer')->getData();
                    $this->messageRepository->createSystemMessage($diplomacy->getAlliance2()->getFounder(),  $this->messageCategoryRepository->find(MessageCategoryId::ALLIANCE) , "Bündnis angenommen", $text);

                    // Log decision
                    $text = "Die Allianzen [b][" . $diplomacy->getAlliance1()->getTag() . "] " . $diplomacy->getAlliance1()->getName() . "[/b] und [b][" . $diplomacy->getAlliance2()->getTag() . "] " . $diplomacy->getAlliance2()->getName() . "[/b] schliessen ein Bündnis!";
                    $this->allianceHistoryRepository->addEntry($diplomacy->getAlliance2(), $text);
                    $this->allianceHistoryRepository->addEntry($diplomacy->getAlliance1(), $text);

                    // Save pact
                    $this->allianceDiplomacyRepository->acceptBnd($diplomacy, AllianceDiplomacyPoints::POINTS_PER_PACT);

                    return $this->render('game/success.html.twig',[
                        'msg' => 'Bündnis angenommen! Bitte denke daran, einen öffentlichen Text zum Bündnis hinzuzufügen!',
                        'path' => $this->generateUrl('game.alliance.diplomacy.relations'),
                        'headline' => 'Allianz'
                    ]);
                }

                // Reject pact offer
                if($form->has('rejectPact') && $form->get('rejectPact')->isClicked()) {
                    // Nachricht an den Leader der anfragenden Allianz
                    $otherFounder = $diplomacy->getAlliance1()->getFounder();
                    $text = "Die Bündnisanfrage [b]" . $diplomacy->getName() . "[/b] wurde von der Allianz [b][" . $diplomacy->getAlliance2()->getTag() . "] " . $diplomacy->getAlliance2()->getName() . "[/b] abgelehnt!\n\n[b]Nachricht:[/b] " . $form->get('answer')->getData();
                    $this->messageRepository->createSystemMessage($otherFounder, $this->messageCategoryRepository->find(MessageCategoryId::ALLIANCE), "Bündnisantrag abgelehnt", $text);

                    // Löscht BND
                    $this->allianceDiplomacyRepository->remove($diplomacy);
                    $this->allianceDiplomacyRepository->save();

                    // Logt die Absage
                    $this->allianceHistoryRepository->addEntry($diplomacy->getAlliance1(), "Die Bündnisanfrage [b]" . $diplomacy->getName() . "[/b] der Allianz [b][" . $diplomacy->getAlliance2()->getTag() . "] " . $diplomacy->getAlliance2()->getName() . "[/b] wird abgelehnt!");
                    $this->allianceHistoryRepository->addEntry($diplomacy->getAlliance2(), "Die Bündnisanfrage [b]" . $diplomacy->getName() . "[/b] wird von der Allianz [b][" . $diplomacy->getAlliance1()->getTag() . "] " . $diplomacy->getAlliance1()->getName() . "[/b] abgelehnt!");

                    return $this->render('game/success.html.twig',[
                        'msg' => 'Bündnis abgelehnt!',
                        'path' => $this->generateUrl('game.alliance.diplomacy.relations'),
                        'headline' => 'Allianz'
                    ]);
                }

                // Save public text
                if($form->has('submitPublicText') && $form->get('submitPublicText')->isClicked()) {
                    $this->allianceDiplomacyRepository->save();

                    return $this->render('game/success.html.twig',[
                        'msg' => 'Text gespeichert',
                        'path' => $this->generateUrl('game.alliance.diplomacy.relations'),
                        'headline' => 'Allianz'
                    ]);
                }
            }

            return $this->render('game/alliance/diplomacy/alliance_diplomacy_view.html.twig',[
                'diplomacy' =>$diplomacy,
                'form' => $form,
                'msg' => $msg??null
            ]);
        }

        return $this->render('game/error.html.twig',[
            'msg' => 'Datensatz nicht vorhanden!',
            'path' => $this->generateUrl('game.alliance.diplomacy.relations'),
            'headline' => 'Allianz'
        ]);
    }

    #[Route('/game/alliance/diplomacy/end-bnd/{id}', name: 'game.alliance.diplomacy.end_bnd')]
    public function end_bnd(Request $request, ?AllianceDiplomacy $diplomacy = null): Response
    {
        $currentAlliance = $this->getUser()->getData()->getAlliance();

        if (
            $diplomacy &&
            ($diplomacy->getAlliance1() === $currentAlliance || $diplomacy->getAlliance2() === $currentAlliance) &&
            $diplomacy->getLevel() === AllianceDiplomacyLevel::BND_CONFIRMED
        ) {
            $form = $this->createFormBuilder()
                ->add('endText', TextareaType::class, [
                    'attr' => [
                        'rows' => 6,
                        'cols' => 70,
                    ],
                    'constraints' => [
                        new NotBlank(['message' => 'Kein Text angegeben!']),
                    ],
                ])
                ->add('send', SubmitType::class, [
                    'attr' => [
                        'onclick' => 'return checkEndPact()',
                        'onsubmit' => "return checkEndPact()"
                    ],
                    'label' => 'Senden'
                ])
                ->getForm()
                ->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
            }

            return $this->render('game/alliance/diplomacy/alliance_diplomacy_end_bnd.html.twig',[
                'diplomacy' =>$diplomacy,
                'form' => $form,
            ]);

        }

        return $this->render('game/error.html.twig',[
            'msg' => 'Datensatz nicht vorhanden!',
            'path' => $this->generateUrl('game.alliance.diplomacy.relations'),
            'headline' => 'Allianz'
        ]);
    }
}