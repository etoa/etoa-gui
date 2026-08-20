<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Alliance\AllianceBuildListRepository;
use EtoA\Alliance\AllianceDiplomacyRepository;
use EtoA\Alliance\AllianceImageStorage;
use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\AllianceService;
use EtoA\Alliance\AllianceTechnologyListRepository;
use EtoA\Alliance\InvalidAllianceParametersException;
use EtoA\Entity\Alliance;
use EtoA\Entity\AllianceBuildListItem;
use EtoA\Entity\AllianceTechnologyListItem;
use EtoA\Form\Type\Admin\AllianceBuildingAddType;
use EtoA\Form\Type\Admin\AllianceCreateType;
use EtoA\Form\Type\Admin\AllianceDepositSearchType;
use EtoA\Form\Type\Admin\AllianceDiplomacyType;
use EtoA\Form\Type\Admin\AllianceEditType;
use EtoA\Form\Type\Admin\AllianceMembersType;
use EtoA\Form\Type\Admin\AllianceRanksType;
use EtoA\Form\Type\Admin\AllianceSearchType;
use EtoA\Form\Type\Admin\AllianceTechnologyAddType;
use EtoA\Support\StringUtils;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AllianceController extends AbstractAdminController
{
    public function __construct(
        private readonly AllianceRepository           $allianceRepository,
        private readonly AllianceService              $allianceService,
        private readonly AllianceImageStorage         $allianceImageStorage,
        private readonly AllianceDiplomacyRepository  $allianceDiplomacyRepository,
        private readonly AllianceBuildListRepository  $allianceBuildListRepository,
        private readonly AllianceTechnologyListRepository $allianceTechnologyListRepository
    )
    {
    }

    #[Route('/admin/alliances/', name: 'admin.alliances')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function list(Request $request): Response
    {
        return $this->render('admin/alliance/list.html.twig', [
            'form' => $this->createForm(AllianceSearchType::class, $request->query->all()),
            'total' => $this->allianceRepository->count([]),
        ]);
    }

    #[Route('/admin/alliances/create', name: 'admin.alliances.new')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function create(Request $request): Response
    {
        $form = $this->createForm(AllianceCreateType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            try {
                $alliance = $this->allianceService->create(
                    $data['tag'],
                    $data['name'],
                    $data['founder'],
                );

                $this->addFlash('success', sprintf('Alliance %s erstellt', $alliance->toString()));

                return $this->redirectToRoute('admin.alliances');
            } catch (InvalidAllianceParametersException $ex) {
                $this->addFlash('error', "Allianz konnte nicht erstellt werden!\n\n" . $ex->getMessage());
            }
        }

        return $this->render('admin/alliance/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/alliances/{id}', name: 'admin.alliances.view')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function view(?Alliance $alliance = null): Response
    {
        if ($alliance === null) {
            $this->addFlash('error', 'Allianz nicht gefunden!');

            return $this->redirectToRoute('admin.alliances');
        }

        return $this->render('admin/alliance/view.html.twig', [
            'alliance' => $alliance,
        ]);
    }

    #[Route('/admin/alliances/{id}/edit', name: 'admin.alliances.edit')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function edit(Request $request, ?Alliance $alliance = null): Response
    {
        if ($alliance === null) {
            $this->addFlash('error', 'Allianz nicht gefunden!');

            return $this->redirectToRoute('admin.alliances');
        }

        $form = $this->createForm(AllianceEditType::class, $alliance);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->has('deleteImage') && $form->get('deleteImage')->getData()) {
                if ($alliance->getImage()) {
                    $this->allianceImageStorage->delete($alliance->getImage());
                    $this->allianceRepository->clearPicture($alliance);

                    $this->addFlash('success', 'Bild entfernt!');
                }
            }

            $this->allianceRepository->save();

            $this->addFlash('success', 'Allianzdaten aktualisiert!');
            return $this->redirectToRoute('admin.alliances.view', ['id' => $alliance->getId()]);
        }

        return $this->render('admin/alliance/edit.html.twig', [
            'alliance' => $alliance,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/alliances/{id}/members', name: 'admin.alliances.members')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function members(Alliance $alliance, Request $request): Response
    {
        $formMembers = $this->createFormBuilder($alliance)
            ->add('members', CollectionType::class, [
                'entry_type' => AllianceMembersType::class,
                'label' => false
            ])->add('submit', SubmitType::class, ['label' => 'Übernehmen'])
            ->getForm()->handleRequest($request);

        if ($formMembers->isSubmitted() && $formMembers->isValid()) {
            foreach($formMembers->get('members')->all() as $member) {
                if($member->get('kick')->getData()) {
                    $alliance->removeMember($member->getData());
                }
            }
            $this->allianceRepository->save();
            return $this->redirectToRoute('admin.alliances.members',['id'=>$alliance->getId()]);
        }

        $formRanks = $this->createFormBuilder($alliance)
            ->add('ranks', CollectionType::class, [
                'entry_type' => AllianceRanksType::class,
                'label' => false
            ])->add('submit', SubmitType::class, ['label' => 'Übernehmen'])
            ->getForm()->handleRequest($request);

        if ($formRanks->isSubmitted() && $formRanks->isValid()) {
            foreach($formRanks->get('ranks')->all() as $rank) {
                if($rank->get('delete')->getData()) {
                    $alliance->removeRank($rank->getData());
                }
            }
            $this->allianceRepository->save();
            return $this->redirectToRoute('admin.alliances.members',['id'=>$alliance->getId()]);
        }

        return $this->render('admin/alliance/members.html.twig', [
            'formMembers' => $formMembers,
            'formRanks' => $formRanks
        ]);
    }

    #[Route('/admin/alliances/{id}/diplomacy', name: 'admin.alliances.diplomacy')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function diplomacy(Alliance $alliance, Request $request): Response
    {
        $diplomacy = $this->allianceDiplomacyRepository->getDiplomacies($alliance);
        $form = $this->createFormBuilder(['diplomacy'=>$diplomacy])
            ->add('diplomacy', CollectionType::class, [
                'entry_type' => AllianceDiplomacyType::class,
                'label' => false
            ])->add('submit', SubmitType::class, ['label' => 'Übernehmen'])
            ->getForm()->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach($form->get('diplomacy')->all() as $diplomacy) {
                if($diplomacy->get('delete')->getData()) {
                    $this->allianceDiplomacyRepository->remove($diplomacy->getData());
                }
            }
            $this->allianceDiplomacyRepository->save();
            $this->addFlash('success', 'Diplomatie aktualisiert!');

            return $this->redirectToRoute('admin.alliances.diplomacy',['id'=>$alliance->getId()]);
        }

        return $this->render('admin/alliance/diplomacy.html.twig', [
            'alliance' => $alliance,
            'form' => $form
        ]);
    }

    #[Route('/admin/alliances/{id}/history', name: 'admin.alliances.history')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function history(Alliance $alliance): Response
    {
        return $this->render('admin/alliance/history.html.twig', [
            'alliance' => $alliance
        ]);
    }

    #[Route('/admin/alliances/{id}/resources', name: 'admin.alliances.resources')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function resources(Alliance $alliance, Request $request): Response
    {
        $form = $this->createFormBuilder($alliance)
            ->add('resMetal', TextType::class, [
                'attr' => [
                    'size'=>12,
                    'maxlength'=>20,
                    'autocomplete'=>"off",
                    'onfocus'=>"this.select()",
                    'onclick'=>"this.select()",
                    'onkeydown'=>"return nurZahlen(event)"
                ],
                'label' => false
            ])
            ->add('resCrystal', TextType::class, [
                'attr' => [
                    'size'=>12,
                    'maxlength'=>20,
                    'autocomplete'=>"off",
                    'onfocus'=>"this.select()",
                    'onclick'=>"this.select()",
                    'onkeydown'=>"return nurZahlen(event)"
                ],
                'label' => false
            ])
            ->add('resPlastic', TextType::class, [
                'attr' => [
                    'size'=>12,
                    'maxlength'=>20,
                    'autocomplete'=>"off",
                    'onfocus'=>"this.select()",
                    'onclick'=>"this.select()",
                    'onkeydown'=>"return nurZahlen(event)"
                ],
                'label' => false
            ])
            ->add('resFuel', TextType::class, [
                'attr' => [
                    'size'=>12,
                    'maxlength'=>20,
                    'autocomplete'=>"off",
                    'onfocus'=>"this.select()",
                    'onclick'=>"this.select()",
                    'onkeydown'=>"return nurZahlen(event)"
                ],
                'label' => false
            ])
            ->add('resFood', TextType::class, [
                'attr' => [
                    'size'=>12,
                    'maxlength'=>20,
                    'autocomplete'=>"off",
                    'onfocus'=>"this.select()",
                    'onclick'=>"this.select()",
                    'onkeydown'=>"return nurZahlen(event)"
                ],
                'label' => false
            ])
            ->add('addMetal', TextType::class, [
                'attr' => [
                    'size'=>8,
                    'maxlength'=>20,
                    'autocomplete'=>"off",
                    'onfocus'=>"this.select()",
                    'onclick'=>"this.select()",
                    'onkeydown'=>"return nurZahlen(event)"
                ],
                'label' => false,
                'required' => false,
                'data' => 0,
                'mapped' => false
            ])
            ->add('addCrystal', TextType::class, [
                'attr' => [
                    'size'=>8,
                    'maxlength'=>20,
                    'autocomplete'=>"off",
                    'onfocus'=>"this.select()",
                    'onclick'=>"this.select()",
                    'onkeydown'=>"return nurZahlen(event)"
                ],
                'label' => false,
                'required' => false,
                'data' => 0,
                'mapped' => false
            ])
            ->add('addPlastic', TextType::class, [
                'attr' => [
                    'size'=>8,
                    'maxlength'=>20,
                    'autocomplete'=>"off",
                    'onfocus'=>"this.select()",
                    'onclick'=>"this.select()",
                    'onkeydown'=>"return nurZahlen(event)"
                ],
                'label' => false,
                'required' => false,
                'data' => 0,
                'mapped' => false
            ])
            ->add('addFuel', TextType::class, [
                'attr' => [
                    'size'=>8,
                    'maxlength'=>20,
                    'autocomplete'=>"off",
                    'onfocus'=>"this.select()",
                    'onclick'=>"this.select()",
                    'onkeydown'=>"return nurZahlen(event)"
                ],
                'label' => false,
                'required' => false,
                'data' => 0,
                'mapped' => false
            ])
            ->add('addFood', TextType::class, [
                'attr' => [
                    'size'=>8,
                    'maxlength'=>20,
                    'autocomplete'=>"off",
                    'onfocus'=>"this.select()",
                    'onclick'=>"this.select()",
                    'onkeydown'=>"return nurZahlen(event)"
                ],
                'label' => false,
                'required' => false,
                'data' => 0,
                'mapped' => false
            ])
            ->add('submit', SubmitType::class, ['label' => 'Übernehmen'])
            ->getForm()->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->allianceRepository->save();
            $this->allianceRepository->addResources(
                $alliance,
                StringUtils::parseFormattedNumber($form->get('addMetal')->getData()),
                StringUtils::parseFormattedNumber($form->get('addCrystal')->getData()),
                StringUtils::parseFormattedNumber($form->get('addPlastic')->getData()),
                StringUtils::parseFormattedNumber($form->get('addFuel')->getData()),
                StringUtils::parseFormattedNumber($form->get('addFood')->getData())
            );
            $this->addFlash('success', 'Ressourcen aktualisiert!');
        }

        return $this->render('admin/alliance/resources.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/admin/alliances/{id}/deposit', name: 'admin.alliances.deposit')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function deposit(Alliance $alliance, Request $request): Response
    {
        return $this->render('admin/alliance/deposit.html.twig', [
            'alliance' => $alliance,
            'form' => $this->createForm(AllianceDepositSearchType::class, $request->query->all(), ['allianceId' => $alliance->getId()]),
        ]);
    }

    #[Route('/admin/alliances/{id}/buildings', name: 'admin.alliances.buildings')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function buildings(Request $request, Alliance $alliance): Response
    {
        $newBuildListItem = new AllianceBuildListItem();
        $newBuildListItem->setAlliance($alliance);

        $form = $this->createForm(AllianceBuildingAddType::class, $newBuildListItem);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var AllianceBuildListItem $data */
            $data = $form->getData();
            if ($this->allianceBuildListRepository->existsInAlliance($data->getAlliance(), $data->getAllianceBuilding())) {
                $this->allianceBuildListRepository->updateForAlliance(
                    $data->getAlliance(),
                    $data->getAllianceBuilding(),
                    $data->getLevel(),
                    $data->getMemberFor()
                );

                $this->addFlash('success', 'Gebäudedatensatz erfolgreich bearbeitet!');
            } else {
                $newBuildListItem->setMemberFor(count($alliance->getMembers()->toArray()));
                $alliance->addBuildlist($newBuildListItem);

                $this->addFlash('success', 'Gebäudedatensatz erfolgreich eingefügt!');
            }
        }

        return $this->render('admin/alliance/buildings.html.twig', [
            'alliance' => $alliance,
            'buildlist' => $alliance->getBuildlist(),
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/alliances/{id}/technologies', name: 'admin.alliances.technologies')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function technologies(Request $request, Alliance $alliance): Response
    {
        $newAllianceTechnology = new AllianceTechnologyListItem();
        $newAllianceTechnology->setAlliance($alliance);

        $form = $this->createForm(AllianceTechnologyAddType::class, $newAllianceTechnology);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var AllianceTechnologyListItem $data */
            $data = $form->getData();
            if ($this->allianceTechnologyListRepository->existsInAlliance($data->getAlliance(), $data->getTechnology())) {
                $this->allianceTechnologyListRepository->updateForAlliance(
                    $data->getAlliance(),
                    $data->getTechnology(),
                    $data->getLevel(),
                    $data->getMemberFor()
                );

                $this->addFlash('success', 'Technologiedatensatz erfolgreich bearbeitet!');
            } else {
                $newAllianceTechnology->setMemberFor(count($alliance->getMembers()->toArray()));
                $alliance->addTechlist($newAllianceTechnology);

                $this->addFlash('success', 'Technologiedatensatz erfolgreich eingefügt!');
            }
        }

        return $this->render('admin/alliance/technologies.html.twig', [
            'alliance' => $alliance,
            'techlist' => $alliance->getTechlist(),
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/alliances/{id}/delete', name: 'admin.alliances.delete')]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function delete(Request $request, ?Alliance $alliance = null): Response
    {
        if ($alliance === null) {
            $this->addFlash('error', 'Allianz nicht gefunden!');

            return $this->redirectToRoute('admin.alliances');
        }

        if ($request->isMethod('POST')) {
            if ($this->allianceService->delete($alliance)) {
                $this->addFlash('success', 'Die Allianz wurde gelöscht!');
            } else {
                $this->addFlash('error', 'Allianz konnte nicht gelöscht werden (ist sie in einem aktiven Krieg?)');
            }

            return $this->redirectToRoute('admin.alliances');
        }

        return $this->render('admin/alliance/delete.html.twig', [
            'alliance' => $alliance
        ]);
    }
}
