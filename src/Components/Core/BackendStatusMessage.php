<?php

namespace EtoA\Components\Core;

use EtoA\Support\BBCodeUtils;
use EtoA\Support\RuntimeDataStore;
use EtoA\Text\TextRepository;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: 'components/backend_status_message.html.twig')]
class BackendStatusMessage
{


    public function __construct(
        private readonly RuntimeDataStore      $runtimeDataStore,
        private readonly TextRepository        $textRepository
    )
    {
    }

    public function getStatus(): bool
    {
        $backendStatus = $this->runtimeDataStore->get('backend_status');
        if ($backendStatus != null && $backendStatus == 0) {
            $infoText = $this->textRepository->find('backend_offline_message');
            if ($infoText->isEnabled()) {
                return false;
            }
        }

        return true;
    }

    public function getStatusMessage(): string {
        return BBCodeUtils::toHTML($this->textRepository->find('backend_offline_message')->getContent());
    }
}