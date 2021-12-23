<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GameOfflineType extends AbstractType
{
    public function __construct(
        private RequestStack $requestStack
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('isOffline', false);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ((bool) $options['isOffline']) {
            $builder
                ->add('offline_ips_allow', TextareaType::class, [
                    'required' => false,
                    'label' => sprintf('Erlaubte IP Adressen  (deine ist %s)', $this->requestStack->getCurrentRequest()->getClientIp()),
                    'attr' => [
                        'rows' => 6,
                        'cols' => 60,
                    ],
                ])
                ->add('offline_message', TextareaType::class, [
                    'required' => false,
                    'label' => 'Nachricht',
                    'attr' => [
                        'rows' => 6,
                        'cols' => 60,
                    ],
                ]);
        }
    }
}
