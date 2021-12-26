<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Core\Configuration\ConfigurationService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class AllianceNewsDefaultBanType extends AbstractType
{
    public function __construct(
        private ConfigurationService $config
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('timespan', ChoiceType::class, [
                'label' => 'Standardeinstellung für Sperre',
                'choices' => [
                    '6 Stunden' => 21600,
                    '12 Stunden' => 43200,
                    '18 Stunden' => 64800,
                    '1 Tag' => 86400,
                    '2 Tage' => 172800,
                    '3 Tage' => 259200,
                    '5 Tage' => 432000,
                    '1 Woche' => 604800,
                ],
                'data' => $this->config->getInt('townhall_ban'),
            ])
            ->add('reason', TextType::class, [
                'label' => 'mit folgendem Text',
                'data' => $this->config->param1('townhall_ban'),
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Speichern',
            ]);
    }
}
