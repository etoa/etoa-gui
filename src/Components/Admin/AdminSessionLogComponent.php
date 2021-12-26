<?php declare(strict_types=1);

namespace EtoA\Components\Admin;

use EtoA\Admin\AdminSessionLog;
use EtoA\Admin\AdminSessionRepository;
use EtoA\Form\Type\Admin\AdminSessionLogType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('admin_session_log')]
class AdminSessionLogComponent extends AbstractController
{
    use ComponentWithFormTrait;
    use DefaultActionTrait;

    public function __construct(
        private AdminSessionRepository $sessionRepository
    ) {
    }

    #[LiveProp()]
    public ?int $userId = null;

    public function __invoke(): void
    {
        $this->userId = null;
        if ($this->getFormValues()['user'] !== '') {
            $this->userId = (int) $this->getFormValues()['user'];
        }
    }

    /**
     * @return AdminSessionLog[]
     */
    public function getLogs(): array
    {
        $logs = [];
        if ($this->userId !== null) {
            $logs = $this->sessionRepository->findSessionLogsByUser($this->userId);
        }

        return $logs;
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(AdminSessionLogType::class, $this->getFormValues());
    }
}
