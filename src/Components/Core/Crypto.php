<?php

namespace EtoA\Components\Core;

use EtoA\Alliance\AllianceBuildingCooldownRepository;
use EtoA\Alliance\AllianceBuildingId;
use EtoA\Controller\Game\AbstractGameController;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\DTO\CryptoDto;
use EtoA\Entity\Entity;
use EtoA\Entity\User;
use EtoA\Fleet\Exception\FleetScanFailedException;
use EtoA\Fleet\Exception\FleetScanPreconditionsNotMetException;
use EtoA\Fleet\Exception\InvalidFleetScanParameterException;
use EtoA\Fleet\FleetScanService;
use EtoA\Support\BBCodeUtils;
use EtoA\Support\StringUtils;
use EtoA\Universe\Entity\EntityCoordinates;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntityService;
use EtoA\Universe\Entity\EntityType;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Resources\ResourceNames;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsLiveComponent(template: 'components/crypto.html.twig')]
class Crypto extends AbstractGameController
{
    use ComponentWithFormTrait;
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public array $distance = ['text'=>'-','style'=>''];

    #[LiveProp(writable: true)]
    public array $targetInfo = ['text'=>'Wähle bitte ein Ziel...','style'=>''];

    #[LiveProp(writable: true)]
    public int $userCooldownDifference = 0;

    #[LiveProp(writable: true)]
    public int $level = 0;

    #[LiveProp]
    public User|null $user = null;

    #[LiveProp]
    public int $userCooldown = 0;

    #[LiveProp(writable: ['sx','sy','cx','cy','pos'])]
    public CryptoDto $cryptoDto;

    #[LiveProp]
    public string $report = '';

    #[LiveProp]
    public string $error = '';

    public function __construct(
        private readonly FleetScanService $fleetScanService,
        private readonly PlanetRepository $planetRepository,
        private readonly RequestStack $requestStack,
        private readonly ConfigurationService $configurationService,
        private readonly EntityRepository $entityRepository,
        private readonly EntityService $entityService,
        private readonly AllianceBuildingCooldownRepository $allianceBuildingCooldownRepository
    )
    {
        $this->cryptoDto = new CryptoDto();
    }

    public function getCooldown(): int
    {
        return $this->fleetScanService->calculateCooldown($this->level);
    }

    public function getCurrentTrit():int
    {
        $request = $this->requestStack->getCurrentRequest();

        $cp = $this->planetRepository->find($request->getSession()->get('cpid'));
        return $cp->getResFuel();
    }

    #[LiveAction]
    public function getCryptoDistance(): void
    {
        if ($this->cryptoDto->getSx() < 1) {
            $this->cryptoDto->setSx(1);
        }
        if ($this->cryptoDto->getSy() < 1) {
            $this->cryptoDto->setSy(1);
        }
        if ($this->cryptoDto->getCx() < 1) {
            $this->cryptoDto->setCx(1);
        }
        if ($this->cryptoDto->getCy() < 1) {
            $this->cryptoDto->setCy(1);
        }
        if ($this->cryptoDto->getSx() > $this->configurationService->param1Int('num_of_sectors')) {
            $this->cryptoDto->setSx($this->configurationService->param1Int('num_of_sectors'));
        }
        if ($this->cryptoDto->getSy() > $this->configurationService->param2Int('num_of_sectors')) {
            $this->cryptoDto->setSy($this->configurationService->param2Int('num_of_sectors'));
        }
        if ($this->cryptoDto->getCx() > $this->configurationService->param1Int('num_of_cells')) {
            $this->cryptoDto->setCx($this->configurationService->param1Int('num_of_cells'));
        }
        if ($this->cryptoDto->getCy() > $this->configurationService->param2Int('num_of_cells')) {
            $this->cryptoDto->setCy($this->configurationService->param2Int('num_of_cells'));
        }
        if ($this->cryptoDto->getPos() < 1) {
            $this->cryptoDto->setPos(1);
        }
        if ($this->cryptoDto->getPos() > $this->configurationService->param2Int('num_planets')) {
            $this->cryptoDto->setPos($this->configurationService->param2Int('num_planets'));
        }

        $targetCoordinates = new EntityCoordinates($this->cryptoDto->getSx(), $this->cryptoDto->getSy(), $this->cryptoDto->getCx(), $this->cryptoDto->getCy(), $this->cryptoDto->getPos());#
        $targetEntity = $this->entityRepository->findByCoordinates($targetCoordinates);

        if (!$targetEntity || $targetEntity->getCode() !== EntityType::PLANET) {
            $this->targetInfo['text'] = 'Hier existiert kein Planet!';
            $this->targetInfo['style'] = 'color:#f00';
            $this->distance['text'] = '-';
            $this->distance['style'] = 'color:#f00';
        }
        else {
            $planet = $targetEntity->getPlanet();
            if (filled($planet->getName())) {
                $out = "<b>Planet:</b> " . $planet->getName();
            } else {
                $out = "<i>Unbenannter Planet</i>";
            }

            if ($planet->getUser()) {
                $out .= " <b>Besitzer:</b> " . $planet->getUser()->getNick();
                if ($this->user === $planet->getUser()) {
                    $out .= ' (Eigener Planet)';
                    $this->targetInfo['style'] = 'color:#f00';
                } else {
                    $this->targetInfo['style'] = 'color:#0f0';
                }
            } else {
                $this->targetInfo['style'] = 'color:#f00';
            }

            $this->targetInfo['text'] = $out;
            $request = $this->requestStack->getCurrentRequest();

            /** @var Entity $entity */
            $entity = $this->planetRepository->find($request->getSession()->get('cpid'))->getEntity();
            $cell = $entity->getCell();

            $sourceCoordinates = new EntityCoordinates($cell->getSx(), $cell->getSy(), $cell->getCx(), $cell->getCy(), $entity->getPos());
            $distance = $this->entityService->distanceByCoords($sourceCoordinates, $targetCoordinates);
            $this->distance['text'] = StringUtils::formatNumber($distance) . " AE";

            $range = $this->configurationService->getInt('crypto_range_per_level') * $this->level;

            if ($distance > $range) {
                $this->distance['text'] .= '(zu weit entfernt, ' . StringUtils::formatNumber($range) . ' max)';
                $this->distance['style'] = 'color:#f00';
            } else {
                $this->distance['style'] = 'color:#0f0';
            }
        }
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createFormBuilder()
            ->add('sx', TextType::class, [
                'label' => false,
                'attr' => [
                    'size'=>2,
                    'maxlength'=>2,
                    'data-live-action-param'=>"getCryptoDistance",
                    'data-action'=>"live#action",
                    'data-model'=>"cryptoDto.sx"
                ]
            ])
            ->add('sy', TextType::class, [
                'label' => false,
                'attr' => [
                    'size'=>2,
                    'maxlength'=>2,
                    'data-live-action-param'=>"getCryptoDistance",
                    'data-action'=>"live#action",
                    'data-model'=>"cryptoDto.sy"
                ]
            ])
            ->add('cx', TextType::class, [
                'label' => false,
                'attr' => [
                    'size'=>2,
                    'maxlength'=>2,
                    'data-live-action-param'=>"getCryptoDistance",
                    'data-action'=>"live#action",
                    'data-model'=>"cryptoDto.cx"
                ]
            ])
            ->add('cy', TextType::class, [
                'label' => false,
                'attr' => [
                    'size'=>2,
                    'maxlength'=>2,
                    'data-live-action-param'=>"getCryptoDistance",
                    'data-action'=>"live#action",
                    'data-model'=>"cryptoDto.cy"
                ]
            ])
            ->add('pos', TextType::class, [
                'label' => false,
                'attr' => [
                    'size'=>2,
                    'maxlength'=>2,
                    'data-live-action-param'=>"getCryptoDistance",
                    'data-action'=>"live#action",
                    'data-model'=>"cryptoDto.pos"
                ]
            ])
            ->add('submit', ButtonType::class, [
                'label' => 'Analyse für ' . StringUtils::formatNumber($this->configurationService->getInt('crypto_fuel_costs_per_scan')) . ' ' . ResourceNames::FUEL . ' starten',
                'attr' => [
                    'data-live-action-param'=>"scan",
                    'data-action'=>"live#action"
                ],
            ])
            ->getForm();
    }

    #[LiveAction]
    public function scan():void
    {
        $targetCoordinates = new EntityCoordinates(
            $this->cryptoDto->getSx(),
            $this->cryptoDto->getSy(),
            $this->cryptoDto->getcx(),
            $this->cryptoDto->getCy(),
            $this->cryptoDto->getPos()
        );
        $targetEntity = $this->entityRepository->findByCoordinates($targetCoordinates);

        try {
            $request = $this->requestStack->getCurrentRequest();
            $out = $this->fleetScanService->scanFleets($this->user, $this->planetRepository->find($request->getSession()->get('cpid')), $this->level, $targetEntity);

            $this->report = BBCodeUtils::toHTML($out);
        } catch (FleetScanPreconditionsNotMetException | InvalidFleetScanParameterException | FleetScanFailedException $ex) {
            $this->error = $ex->getMessage();
        }
    }

    #[PreMount]
    public function preMount(): void
    {
        $this->user = $this->getUser()->getData();
        $request = $this->requestStack->getCurrentRequest();

        $this->userCooldown =  $this->allianceBuildingCooldownRepository->getUserCooldown($this->user, AllianceBuildingId::CRYPTO->value);

        $entity = $this->planetRepository->find($request->getSession()->get('cpid'))->getEntity();
        $cell = $entity->getCell();
        $this->cryptoDto->setCx($cell->getCx());
        $this->cryptoDto->setCy($cell->getCy());
        $this->cryptoDto->setSx($cell->getSx());
        $this->cryptoDto->setSy($cell->getSy());
        $this->cryptoDto->setPos($entity->getPos());

        $this->getCryptoDistance();
    }
}