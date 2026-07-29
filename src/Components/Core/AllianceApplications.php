<?php declare(strict_types=1);

namespace EtoA\Components\Core;

use EtoA\Alliance\AllianceApplicationRepository;
use EtoA\Alliance\AllianceHistoryRepository;
use EtoA\Alliance\AllianceMemberCosts;
use EtoA\Alliance\AllianceRights;
use EtoA\Alliance\AllianceService;
use EtoA\Controller\Game\AbstractGameController;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\Alliance;
use EtoA\Entity\AllianceApplication;
use EtoA\Form\Type\Core\AllianceApplicationType;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Message\MessageCategoryId;
use EtoA\Message\MessageCategoryRepository;
use EtoA\Message\MessageRepository;
use EtoA\Universe\Resources\BaseResources;
use EtoA\User\UserRepository;
use EtoA\User\UserService;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/alliance_applications.html.twig', route: 'live_component_game')]
class AllianceApplications extends AbstractGameController
{
    use ComponentWithFormTrait;
    use DefaultActionTrait;

    private ?Alliance $alliance = null;

    /** @var array<int, AllianceApplication>|null */
    private ?array $applications = null;

    private ?BaseResources $memberCosts = null;
    private bool $memberCostsCalculated = false;
    private ?int $currentMemberCount = null;

    /** @var string[] */
    private array $successMessages = [];

    /** @var string[] */
    private array $errorMessages = [];

    public function __construct(
        private readonly AllianceApplicationRepository $allianceApplicationRepository,
        private readonly AllianceHistoryRepository     $allianceHistoryRepository,
        private readonly AllianceMemberCosts           $allianceMemberCosts,
        private readonly AllianceService               $allianceService,
        private readonly ConfigurationService          $config,
        private readonly LogRepository                 $logRepository,
        private readonly MessageCategoryRepository     $messageCategoryRepository,
        private readonly MessageRepository             $messageRepository,
        private readonly UserRepository                $userRepository,
        private readonly UserService                   $userService,
    )
    {
    }

    /**
     * @return array<int, AllianceApplication>
     */
    public function getApplications(): array
    {
        if ($this->applications === null) {
            $this->applications = [];
            foreach ($this->allianceApplicationRepository->findBy(['alliance' => $this->getAlliance()]) as $application) {
                if ($application->getUser() !== null) {
                    $this->applications[$application->getUser()->getId()] = $application;
                }
            }
        }

        return $this->applications;
    }

    public function getMaxMemberCount(): int
    {
        return $this->config->getInt('alliance_max_member_count');
    }

    public function getCurrentMemberCount(): int
    {
        return $this->currentMemberCount ??= $this->userRepository->count(['alliance' => $this->getAlliance()]);
    }

    public function isAcceptAllowed(): bool
    {
        return $this->getMaxMemberCount() === 0 || $this->getCurrentMemberCount() < $this->getMaxMemberCount();
    }

    /**
     * Anzahl der aktuell auf "Annehmen" gestellten Bewerbungen.
     */
    public function getAcceptCount(): int
    {
        $count = 0;
        foreach ($this->formValues['applications'] ?? [] as $values) {
            if ((int) ($values['action'] ?? AllianceApplicationType::ACTION_IGNORE) === AllianceApplicationType::ACTION_ACCEPT) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Rohstoffe, die bei der aktuellen Auswahl vom Allianzkonto abgezogen werden,
     * oder null, falls keine Bewerbung angenommen wird
     */
    public function getMemberCosts(): ?BaseResources
    {
        if (!$this->memberCostsCalculated) {
            $this->memberCostsCalculated = true;

            $acceptCount = $this->getAcceptCount();
            if ($acceptCount > 0) {
                $currentMemberCount = $this->getCurrentMemberCount();
                $this->memberCosts = $this->allianceMemberCosts->calculate(
                    $this->getAlliance()->getId(),
                    $currentMemberCount,
                    $currentMemberCount + $acceptCount
                );
            }
        }

        return $this->memberCosts;
    }

    /** @return string[] */
    public function getSuccessMessages(): array
    {
        return $this->successMessages;
    }

    /** @return string[] */
    public function getErrorMessages(): array
    {
        return $this->errorMessages;
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createFormBuilder(['applications' => $this->getApplications()])
            ->add('applications', CollectionType::class, [
                'entry_type' => AllianceApplicationType::class,
                'entry_options' => ['label' => false],
                'label' => false,
            ])
            ->getForm();
    }

    #[LiveAction]
    public function save(): void
    {
        $this->submitForm();

        $alliance = $this->getAlliance();
        $category = $this->messageCategoryRepository->find(MessageCategoryId::ALLIANCE);
        $maxMemberCount = $this->getMaxMemberCount();
        $currentMemberCount = $this->getCurrentMemberCount();
        $newMemberCount = $currentMemberCount;

        foreach ($this->getForm()->get('applications') as $applicationForm) {
            /** @var AllianceApplication $application */
            $application = $applicationForm->getData();
            $applicant = $application->getUser();
            if ($applicant === null) {
                continue;
            }

            $nick = $applicant->getNick();
            $answer = trim((string) $applicationForm->get('answer')->getData());

            switch ((int) $applicationForm->get('action')->getData()) {
                // Anfrage annehmen
                case AllianceApplicationType::ACTION_ACCEPT:
                    if ($maxMemberCount !== 0 && $newMemberCount >= $maxMemberCount) {
                        $this->errorMessages[] = "Maximale Anzahl an Mitgliedern erreicht!";
                        break 2;
                    }

                    $newMemberCount++;
                    $this->successMessages[] = $nick . " wurde angenommen.";

                    // Nachricht an den Bewerber schicken
                    $this->messageRepository->createSystemMessage($applicant, $category, "Bewerbung angenommen", "Deine Allianzbewerbung wurde angenommen!\n\n[b]Antwort:[/b]\n" . $answer);

                    // Log schreiben
                    $this->allianceHistoryRepository->addEntry($alliance, "Die Bewerbung von [b]" . $nick . "[/b] wurde akzeptiert!");
                    $this->logRepository->add(LogFacility::ALLIANCE, LogSeverity::INFO, "Der Spieler [b]" . $nick . "[/b] tritt der Allianz [b]" . $alliance->toString() . "[/b] bei!");
                    $this->userService->addToUserLog($applicant, "alliance", "{nick} ist nun ein Mitglied der Allianz " . $alliance->getName() . ".");

                    // Speichern
                    $applicant->setAlliance($alliance);
                    $this->allianceApplicationRepository->remove($application);
                    $this->allianceApplicationRepository->save();
                    break;

                // Anfrage ablehnen
                case AllianceApplicationType::ACTION_REJECT:
                    $this->successMessages[] = $nick . " wurde abgelehnt.";

                    // Nachricht an den Bewerber schicken
                    $this->messageRepository->createSystemMessage($applicant, $category, "Bewerbung abgelehnt", "Deine Allianzbewerbung wurde abgelehnt!\n\n[b]Antwort:[/b]\n" . $answer);

                    // Log schreiben
                    $this->allianceHistoryRepository->addEntry($alliance, "Die Bewerbung von [b]" . $nick . "[/b] wurde abgelehnt!");

                    // Anfrage löschen
                    $this->allianceApplicationRepository->remove($application);
                    $this->allianceApplicationRepository->save();
                    break;

                // Anfrage unbearbeitet lassen, jedoch Nachricht verschicken, wenn etwas geschrieben ist
                default:
                    if ($answer !== '') {
                        $this->messageRepository->createSystemMessage($applicant, $category, "Bewerbung: Nachricht", "Antwort auf die Bewerbung an die Allianz [b]" . $alliance->toString() . "[/b]:\n" . $answer);

                        $this->successMessages[] = $nick . ": Nachricht gesendet";
                    }
            }
        }

        // Wenn neue Mitglieder hinzugefügt worden sind, werden ev. die Allianzrohstoffe angepasst
        if ($newMemberCount > $currentMemberCount) {
            $this->allianceMemberCosts->increase($alliance, $currentMemberCount, $newMemberCount);
        }

        $this->successMessages[] = "Änderungen übernommen";

        // Bearbeitete Bewerbungen sind weg und die Radiobuttons stehen wieder auf dem Standard
        $this->applications = null;
        $this->memberCosts = null;
        $this->memberCostsCalculated = false;
        $this->currentMemberCount = null;
        $this->resetForm();
    }

    /**
     * Die Rechte werden hier nochmals geprüft, da die Live-Component-Route unabhängig
     * vom Controller aufrufbar ist.
     */
    private function getAlliance(): Alliance
    {
        if ($this->alliance === null) {
            $user = $this->getUser()->getData();
            $alliance = $user->getAlliance();

            if ($alliance === null
                || !$this->allianceService->getUserAlliancePermissions($alliance, $user)->checkHasRights(AllianceRights::APPLICATIONS, 'alliance')) {
                throw new AccessDeniedHttpException();
            }

            $this->alliance = $alliance;
        }

        return $this->alliance;
    }
}
