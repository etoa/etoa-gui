<?php

namespace EtoA\Controller\Game;

use EtoA\Alliance\AllianceDiplomacyLevel;
use EtoA\Alliance\AllianceDiplomacyRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Entity\Alliance;
use EtoA\Entity\AllianceDiplomacy;
use EtoA\Message\MessageCategoryId;
use EtoA\Message\MessageCategoryRepository;
use EtoA\Message\MessageRepository;
use phpDocumentor\Reflection\Types\This;
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
        private readonly MessageCategoryRepository $messageCategoryRepository
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
}