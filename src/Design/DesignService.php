<?php

namespace EtoA\Design;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\User\UserPropertiesRepository;
use Symfony\Bundle\SecurityBundle\Security;

class DesignService
{
    public function __construct(
        private readonly UserPropertiesRepository $userPropertiesRepository,
        private readonly ConfigurationService     $config,
        private readonly Security $security,
    )
    {
    }

    public function getCurrentDesign():string
    {
        $cu = $this->security->getUser();
        $properties = $this->userPropertiesRepository->getOrCreateProperties($cu->getData());
        $design = 'build/'.Design::DIRECTORY . "/official/" . $this->config->get('default_css_style');
        if ($properties->getCssStyle()) {
            if (is_dir('build/'.Design::DIRECTORY . "/official/" . $properties->getCssStyle())) {
                $design = 'build/'.Design::DIRECTORY . "/official/" . $properties->getCssStyle();
            }
        }

        return $design;
    }
}