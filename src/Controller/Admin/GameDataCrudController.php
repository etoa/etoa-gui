<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Admin\Forms\AdvancedForm;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GameDataCrudController extends AbstractAdminController
{
    protected function renderContent(Request $request, AdvancedForm $form): Response
    {
        return $this->render('admin/default.html.twig', [
            'title' => $form->getName(),
            'content' => $form->router($request),
        ]);
    }
}
