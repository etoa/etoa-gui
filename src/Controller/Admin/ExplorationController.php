<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Entity\User;
use EtoA\Form\Type\Core\UserType;
use EtoA\Universe\Cell\CellRepository;
use EtoA\User\UserRepository;
use EtoA\User\UserUniverseDiscoveryService;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ExplorationController extends AbstractAdminController
{
    public function __construct(
        private readonly UserRepository               $userRepository,
        private readonly CellRepository               $cellRepository,
        private readonly UserUniverseDiscoveryService $userUniverseDiscoveryService
    )
    {
    }

    #[Route("/admin/universe/exploration/", name: "admin.universe.exploration")]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function overview(Request $request): Response
    {
        $users = $this->userRepository->searchUserNicknames();
        if (count($users) === 0) {
            $this->addFlash('error', 'Keine Benutzer vorhanden!');
        }

        $form = $this->createFormBuilder()
            ->add('users', UserType::class, [
                'placeholder'=>'(Benutzer wählen...)',
                'attr' => [
                    'onchange' => 'this.form.submit()'
                ]
            ])
            ->getForm()
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute('admin.universe.exploration.user',['id'=>$form->get('users')->getData()->getId()]);
        }

        return $this->render('admin/universe/exploration.html.twig', [
            'form' => $form
        ]);
    }

    #[Route("/admin/universe/exploration/user/{id}", name: "admin.universe.exploration.user")]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function user(Request $request, ?User $user = null): Response
    {
        if (!$user) {
            return $this->redirectToRoute('admin.universe.exploration');
        }

        $sx = 1;
        $sy = 1;
        $cx = 1;
        $cy = 1;
        $radius = 1;

        // Discover selected cell
        if ($request->request->has('discover_selected')) {
            $sx = $request->request->getInt('sx');
            $sy = $request->request->getInt('sy');
            $cx = $request->request->getInt('cx');
            $cy = $request->request->getInt('cy');
            $radius = abs($request->request->getInt('radius'));

            $cell = $this->cellRepository->getCellIdByCoordinates($sx, $sy, $cx, $cy);
            if ($cell !== null) {
                $this->userUniverseDiscoveryService->setDiscovered($user, $cell, $radius);
                $this->addFlash('success', 'Koordinaten erkundet!');
            } else {
                $this->addFlash('error', 'Ungültige Koordinate!');
            }
        } // Reset discovered coordinates
        elseif ($request->request->has('discover_reset')) {
            $this->userUniverseDiscoveryService->setDiscoveredAll($user, false);
            $this->addFlash('success', 'Erkundung zurückgesetzt!');
        } // Discover all coordinates
        elseif ($request->request->has('discover_all')) {
            $this->userUniverseDiscoveryService->setDiscoveredAll($user, true);
            $this->addFlash('success', 'Alles erkundet!');
        }

        $form = $this->createFormBuilder()
            ->add('users', UserType::class, [
                'placeholder'=>'(Benutzer wählen...)',
                'attr' => [
                    'onchange' => 'this.form.submit()'
                ],
                'data' => $user
            ])
            ->add('sx', IntegerType::class, [
                'data' => $sx,
                'attr' => ['size'=>2]
            ])
            ->add('sy', IntegerType::class, [
                'data' => $sy,
                'attr' => ['size'=>2]
            ])
            ->add('cx', IntegerType::class, [
                'data' => $cx,
                'attr' => ['size'=>2]
            ])
            ->add('cy', IntegerType::class, [
                'data' => $cy,
                'attr' => ['size'=>2]
            ])
            ->add('radius', IntegerType::class, [
                'data' => $radius,
                'attr' => ['size'=>1]
            ])
            ->add('selected', SubmitType::class, [
                'label' => 'Gewählte Koordinate erkunden'
            ])
            ->add('reset', SubmitType::class, [
                'label' => 'Erkundung zurücksetzen',
                'attr' => [
                    "onclick"=>"return confirm('Wirklich zurücksetzen?')"]
            ])
            ->add('all', SubmitType::class, [
                'label' => 'Alles erkunden'
            ])
            ->getForm()
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($form->get('users')->getData() !== $user) {
                return $this->redirectToRoute('admin.universe.exploration.user',['id'=>$form->get('users')->getData()->getId()]);
            }

            // Discover selected cell
            if ($form->get('selected')->isClicked()) {
                $sx = $form->get('sx')->getData();
                $sy = $form->get('sy')->getData();
                $cx = $form->get('cx')->getData();
                $cy = $form->get('cy')->getData();
                $radius = abs($form->get('radius')->getData());

                $cell = $this->cellRepository->getCellIdByCoordinates($sx, $sy, $cx, $cy);
                if ($cell !== null) {
                    $this->userUniverseDiscoveryService->setDiscovered($user, $cell, $radius);
                    $this->addFlash('success', 'Koordinaten erkundet!');
                } else {
                    $this->addFlash('error', 'Ungültige Koordinate!');
                }
            } // Reset discovered coordinates
            elseif ($form->get('reset')->isClicked()) {
                $this->userUniverseDiscoveryService->setDiscoveredAll($user, false);
                $this->addFlash('success', 'Erkundung zurückgesetzt!');
            } // Discover all coordinates
            elseif ($form->get('all')->isClicked()) {
                $this->userUniverseDiscoveryService->setDiscoveredAll($user, true);
                $this->addFlash('success', 'Alles erkundet!');
            }
        }

        return $this->render('admin/universe/exploration.html.twig', [
            'form' => $form,
            'discoveredPercent' => $this->userUniverseDiscoveryService->getDiscoveredPercent($user),
        ]);
    }
}
