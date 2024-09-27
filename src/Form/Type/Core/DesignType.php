<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use EtoA\Design\DesignsService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DesignType extends AbstractType
{
    public function __construct(
        private readonly DesignsService $designsService,
        private readonly Security       $security,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $cu = $this->security->getUser();
        $designs = $this->designsService->getDesigns();
        $choices = [];
        foreach ($designs as $k => $v) {
            if (!$v['restricted'] || $cu->getData()->getAdmin() || $cu->getData()->getDeveloper()) {
                $choices[$v['name']] = $k;
            }
        }

        $resolver->setDefaults([
            'required' => false,
            'placeholder' => '(Standard)',
            'choices' => $choices,
            'attr' => [
                'data-model'=>"on(change)|cssStyle"
            ],
        ]);
    }

    public function getParent(): string
    {
        return SearchableChoiceType::class;
    }
}
