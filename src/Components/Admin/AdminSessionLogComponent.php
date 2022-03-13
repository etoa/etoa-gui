<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Admin\AdminSessionLog;
use EtoA\Admin\AdminSessionRepository;
use EtoA\Form\Request\Admin\AdminSessionLogRequest;
use EtoA\Form\Type\Admin\AdminSessionLogType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('admin_session_log')]
class AdminSessionLogComponent extends AbstractController
{
    use ComponentWithFormTrait;
    use DefaultActionTrait;

    public AdminSessionLogRequest $request;

    public function __construct(
        private AdminSessionRepository $sessionRepository
    ) {
        $this->request = new AdminSessionLogRequest();
    }

    /**
     * @return AdminSessionLog[]
     */
    public function getLogs(): array
    {
        $logs = [];
        if ($this->request->user !== null) {
            $logs = $this->sessionRepository->findSessionLogsByUser($this->request->user);
        }

        return $logs;
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(AdminSessionLogType::class, $this->request);
    }
}
