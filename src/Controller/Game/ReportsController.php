<?php

namespace EtoA\Controller\Game;

use EtoA\Form\Type\Core\ReportType;
use EtoA\Message\ReportRepository;
use EtoA\Message\ReportTypes;
use EtoA\Pagination\ArrayPaginator;
use EtoA\Pagination\SimplePagination;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReportsController extends AbstractGameController
{
    public function __construct(
        private readonly ReportRepository $reportRepository
    )
    {
    }

    #[Route('/game/reports/all', name: 'game.reports.all')]
    public function all(Request $request): Response {
        $reports = $this->reportRepository->findBy(['deleted'=>false,'archived'=>false, 'user'=>$this->getUser()->getData()]);

        $currentPage = $request->query->getInt('page', 1);

        $paginator = new ArrayPaginator($reports, $currentPage, 20);
        $pagination = new SimplePagination($paginator);

        $form = $this->createFormBuilder(['reports'=>$paginator->getPaginatedItems()])
            ->add('reports', CollectionType::class, [
                'entry_type' => ReportType::class,
                'label' => false
            ])
            ->add('delete', SubmitType::class, [
                'label' => 'Markierte löschen'
            ])
            ->add('deleteAll', SubmitType::class, [
                'label' => 'Alle löschen'
            ])
            ->add('archive', SubmitType::class, [
                'label' => 'Markierte archivieren'
            ])
            ->getForm()
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($form->get('deleteAll')->isClicked()) {
                foreach ($form->getData()['reports'] as $report) {
                    $report->setDeleted(true);

                    $this->reportRepository->save();

                    return $this->redirectToRoute('game.reports.all');
                }
            }
            elseif ($form->get('delete')->isClicked()) {

            }
            elseif ($form->get('archive')->isClicked()) {
                foreach ($form->getData()['reports'] as $report) {
                    if($report->isDeleted()) {
                        $report->setDeleted(false);
                        $report->setArchived(true);

                        $this->reportRepository->save();

                        return $this->redirectToRoute('game.reports.all');
                    }
                }
            }
            else {
                $form->getClickedButton()->getParent()->getData()->setDeleted(true);
            }

            $this->reportRepository->save();
        }

        return $this->render('game/reports/reports_overview.html.twig', [
            'pagination' => $pagination,
            'title' => 'Neueste Berichte',
            'form' => $form
        ]);
    }

    #[Route('/game/reports/battle', name: 'game.reports.battle')]
    public function battle(Request $request): Response {
        $reports = $this->reportRepository->findBy(['deleted'=>false,'archived'=>false,'user'=>$this->getUser()->getData(),'type'=>'battle']);

        $currentPage = $request->query->getInt('page', 1);

        $paginator = new ArrayPaginator($reports, $currentPage, 20);
        $pagination = new SimplePagination($paginator);

        $form = $this->createFormBuilder(['reports'=>$paginator->getPaginatedItems()])
            ->add('reports', CollectionType::class, [
                'entry_type' => ReportType::class,
                'label' => false
            ])
            ->add('delete', SubmitType::class, [
                'label' => 'Markierte löschen'
            ])
            ->add('deleteAll', SubmitType::class, [
                'label' => 'Alle löschen'
            ])
            ->add('archive', SubmitType::class, [
                'label' => 'Markierte archivieren'
            ])
            ->getForm()
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($form->get('deleteAll')->isClicked()) {
                foreach ($form->getData()['reports'] as $report) {
                    $report->setDeleted(true);

                    $this->reportRepository->save();

                    return $this->redirectToRoute('game.reports.all');
                }
            }
            elseif ($form->get('delete')->isClicked()) {

            }
            elseif ($form->get('archive')->isClicked()) {
                foreach ($form->getData()['reports'] as $report) {
                    if($report->isDeleted()) {
                        $report->setDeleted(false);
                        $report->setArchived(true);

                        $this->reportRepository->save();

                        return $this->redirectToRoute('game.reports.all');
                    }
                }
            }
            else {
                $form->getClickedButton()->getParent()->getData()->setDeleted(true);
            }

            $this->reportRepository->save();
        }

        return $this->render('game/reports/reports_overview.html.twig', [
            'pagination' => $pagination,
            'title' => ReportTypes::BATTLE->label(),
            'form' => $form
        ]);
    }

    #[Route('/game/reports/spy', name: 'game.reports.spy')]
    public function spy(Request $request): Response {
        $reports = $this->reportRepository->findBy(['deleted'=>false,'archived'=>false,'user'=>$this->getUser()->getData(),'type'=>'spy']);

        $currentPage = $request->query->getInt('page', 1);

        $paginator = new ArrayPaginator($reports, $currentPage, 20);
        $pagination = new SimplePagination($paginator);

        $form = $this->createFormBuilder(['reports'=>$paginator->getPaginatedItems()])
            ->add('reports', CollectionType::class, [
                'entry_type' => ReportType::class,
                'label' => false
            ])
            ->add('delete', SubmitType::class, [
                'label' => 'Markierte löschen'
            ])
            ->add('deleteAll', SubmitType::class, [
                'label' => 'Alle löschen'
            ])
            ->add('archive', SubmitType::class, [
                'label' => 'Markierte archivieren'
            ])
            ->getForm()
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($form->get('deleteAll')->isClicked()) {
                foreach ($form->getData()['reports'] as $report) {
                    $report->setDeleted(true);

                    $this->reportRepository->save();

                    return $this->redirectToRoute('game.reports.all');
                }
            }
            elseif ($form->get('delete')->isClicked()) {

            }
            elseif ($form->get('archive')->isClicked()) {
                foreach ($form->getData()['reports'] as $report) {
                    if($report->isDeleted()) {
                        $report->setDeleted(false);
                        $report->setArchived(true);

                        $this->reportRepository->save();

                        return $this->redirectToRoute('game.reports.all');
                    }
                }
            }
            else {
                $form->getClickedButton()->getParent()->getData()->setDeleted(true);
            }

            $this->reportRepository->save();
        }

        return $this->render('game/reports/reports_overview.html.twig', [
            'pagination' => $pagination,
            'title' => ReportTypes::SPY->label(),
            'form' => $form
        ]);
    }

    #[Route('/game/reports/explore', name: 'game.reports.explore')]
    public function explore(Request $request): Response {
        $reports = $this->reportRepository->findBy(['deleted'=>false,'archived'=>false,'user'=>$this->getUser()->getData(),'type'=>'explore']);

        $currentPage = $request->query->getInt('page', 1);

        $paginator = new ArrayPaginator($reports, $currentPage, 20);
        $pagination = new SimplePagination($paginator);

        $form = $this->createFormBuilder(['reports'=>$paginator->getPaginatedItems()])
            ->add('reports', CollectionType::class, [
                'entry_type' => ReportType::class,
                'label' => false
            ])
            ->add('delete', SubmitType::class, [
                'label' => 'Markierte löschen'
            ])
            ->add('deleteAll', SubmitType::class, [
                'label' => 'Alle löschen'
            ])
            ->add('archive', SubmitType::class, [
                'label' => 'Markierte archivieren'
            ])
            ->getForm()
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($form->get('deleteAll')->isClicked()) {
                foreach ($form->getData()['reports'] as $report) {
                    $report->setDeleted(true);

                    $this->reportRepository->save();

                    return $this->redirectToRoute('game.reports.all');
                }
            }
            elseif ($form->get('delete')->isClicked()) {

            }
            elseif ($form->get('archive')->isClicked()) {
                foreach ($form->getData()['reports'] as $report) {
                    if($report->isDeleted()) {
                        $report->setDeleted(false);
                        $report->setArchived(true);

                        $this->reportRepository->save();

                        return $this->redirectToRoute('game.reports.all');
                    }
                }
            }
            else {
                $form->getClickedButton()->getParent()->getData()->setDeleted(true);
            }

            $this->reportRepository->save();
        }

        return $this->render('game/reports/reports_overview.html.twig', [
            'pagination' => $pagination,
            'title' => ReportTypes::EXPLORE->label(),
            'form' => $form
        ]);
    }

    #[Route('/game/reports/market', name: 'game.reports.market')]
    public function market(Request $request): Response {
        $reports = $this->reportRepository->findBy(['deleted'=>false,'archived'=>false,'user'=>$this->getUser()->getData(),'type'=>'market']);

        $currentPage = $request->query->getInt('page', 1);

        $paginator = new ArrayPaginator($reports, $currentPage, 20);
        $pagination = new SimplePagination($paginator);

        $form = $this->createFormBuilder(['reports'=>$paginator->getPaginatedItems()])
            ->add('reports', CollectionType::class, [
                'entry_type' => ReportType::class,
                'label' => false
            ])
            ->add('delete', SubmitType::class, [
                'label' => 'Markierte löschen'
            ])
            ->add('deleteAll', SubmitType::class, [
                'label' => 'Alle löschen'
            ])
            ->add('archive', SubmitType::class, [
                'label' => 'Markierte archivieren'
            ])
            ->getForm()
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($form->get('deleteAll')->isClicked()) {
                foreach ($form->getData()['reports'] as $report) {
                    $report->setDeleted(true);

                    $this->reportRepository->save();

                    return $this->redirectToRoute('game.reports.all');
                }
            }
            elseif ($form->get('delete')->isClicked()) {

            }
            elseif ($form->get('archive')->isClicked()) {
                foreach ($form->getData()['reports'] as $report) {
                    if($report->isDeleted()) {
                        $report->setDeleted(false);
                        $report->setArchived(true);

                        $this->reportRepository->save();

                        return $this->redirectToRoute('game.reports.all');
                    }
                }
            }
            else {
                $form->getClickedButton()->getParent()->getData()->setDeleted(true);
            }

            $this->reportRepository->save();
        }

        return $this->render('game/reports/reports_overview.html.twig', [
            'pagination' => $pagination,
            'title' => ReportTypes::MARKET->label(),
            'form' => $form
        ]);
    }

    #[Route('/game/reports/crypto', name: 'game.reports.crypto')]
    public function crypto(Request $request): Response {
        $reports = $this->reportRepository->findBy(['deleted'=>false,'archived'=>false,'user'=>$this->getUser()->getData(),'type'=>'crypto']);

        $currentPage = $request->query->getInt('page', 1);

        $paginator = new ArrayPaginator($reports, $currentPage, 20);
        $pagination = new SimplePagination($paginator);

        $form = $this->createFormBuilder(['reports'=>$paginator->getPaginatedItems()])
            ->add('reports', CollectionType::class, [
                'entry_type' => ReportType::class,
                'label' => false
            ])
            ->add('delete', SubmitType::class, [
                'label' => 'Markierte löschen'
            ])
            ->add('deleteAll', SubmitType::class, [
                'label' => 'Alle löschen'
            ])
            ->add('archive', SubmitType::class, [
                'label' => 'Markierte archivieren'
            ])
            ->getForm()
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($form->get('deleteAll')->isClicked()) {
                foreach ($form->getData()['reports'] as $report) {
                    $report->setDeleted(true);

                    $this->reportRepository->save();

                    return $this->redirectToRoute('game.reports.all');
                }
            }
            elseif ($form->get('delete')->isClicked()) {

            }
            elseif ($form->get('archive')->isClicked()) {
                foreach ($form->getData()['reports'] as $report) {
                    if($report->isDeleted()) {
                        $report->setDeleted(false);
                        $report->setArchived(true);

                        $this->reportRepository->save();

                        return $this->redirectToRoute('game.reports.all');
                    }
                }
            }
            else {
                $form->getClickedButton()->getParent()->getData()->setDeleted(true);
            }

            $this->reportRepository->save();
        }

        return $this->render('game/reports/reports_overview.html.twig', [
            'pagination' => $pagination,
            'title' => ReportTypes::CRYPTO->label(),
            'form' => $form
        ]);
    }

    #[Route('/game/reports/other', name: 'game.reports.other')]
    public function other(Request $request): Response {
        $reports = $this->reportRepository->findBy(['deleted'=>false,'archived'=>false,'user'=>$this->getUser()->getData(),'type'=>'other']);

        $currentPage = $request->query->getInt('page', 1);

        $paginator = new ArrayPaginator($reports, $currentPage, 20);
        $pagination = new SimplePagination($paginator);

        $form = $this->createFormBuilder(['reports'=>$paginator->getPaginatedItems()])
            ->add('reports', CollectionType::class, [
                'entry_type' => ReportType::class,
                'label' => false
            ])
            ->add('delete', SubmitType::class, [
                'label' => 'Markierte löschen'
            ])
            ->add('deleteAll', SubmitType::class, [
                'label' => 'Alle löschen'
            ])
            ->add('archive', SubmitType::class, [
                'label' => 'Markierte archivieren'
            ])
            ->getForm()
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($form->get('deleteAll')->isClicked()) {
                foreach ($form->getData()['reports'] as $report) {
                    $report->setDeleted(true);

                    $this->reportRepository->save();

                    return $this->redirectToRoute('game.reports.all');
                }
            }
            elseif ($form->get('delete')->isClicked()) {

            }
            elseif ($form->get('archive')->isClicked()) {
                foreach ($form->getData()['reports'] as $report) {
                    if($report->isDeleted()) {
                        $report->setDeleted(false);
                        $report->setArchived(true);

                        $this->reportRepository->save();

                        return $this->redirectToRoute('game.reports.all');
                    }
                }
            }
            else {
                $form->getClickedButton()->getParent()->getData()->setDeleted(true);
            }

            $this->reportRepository->save();
        }

        return $this->render('game/reports/reports_overview.html.twig', [
            'pagination' => $pagination,
            'title' => ReportTypes::OTHER->label(),
            'form' => $form
        ]);
    }

    #[Route('/game/reports/archive', name: 'game.reports.archive')]
    public function archive(Request $request): Response {
        $reports = $this->reportRepository->findBy(['deleted'=>false,'archived'=>true,'user'=>$this->getUser()->getData()]);

        $currentPage = $request->query->getInt('page', 1);

        $paginator = new ArrayPaginator($reports, $currentPage, 20);
        $pagination = new SimplePagination($paginator);

        $form = $this->createFormBuilder(['reports'=>$paginator->getPaginatedItems()])
            ->add('reports', CollectionType::class, [
                'entry_type' => ReportType::class,
                'label' => false
            ])
            ->add('delete', SubmitType::class, [
                'label' => 'Markierte löschen'
            ])
            ->add('deleteAll', SubmitType::class, [
                'label' => 'Alle löschen'
            ])
            ->getForm()
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($form->get('deleteAll')->isClicked()) {
                foreach ($form->getData()['reports'] as $report) {
                    $report->setDeleted(true);

                    $this->reportRepository->save();

                    return $this->redirectToRoute('game.reports.all');
                }
            }
            elseif ($form->get('delete')->isClicked()) {

            }
            elseif ($form->get('archive')->isClicked()) {
                foreach ($form->getData()['reports'] as $report) {
                    if($report->isDeleted()) {
                        $report->setDeleted(false);
                        $report->setArchived(true);

                        $this->reportRepository->save();

                        return $this->redirectToRoute('game.reports.all');
                    }
                }
            }
            else {
                $form->getClickedButton()->getParent()->getData()->setDeleted(true);
            }

            $this->reportRepository->save();
        }

        return $this->render('game/reports/reports_overview.html.twig', [
            'pagination' => $pagination,
            'title' => 'Archiv',
            'form' => $form
        ]);
    }
}