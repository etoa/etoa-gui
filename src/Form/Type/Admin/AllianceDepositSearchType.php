<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Form\Type\Core\UserType;
use EtoA\User\UserSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AllianceDepositSearchType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['allianceId' => 0]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $allianceId = $options['allianceId'];

        $builder
            ->add('user', UserType::class, [
                'search' => UserSearch::create()->allianceId($allianceId),
            ])
            ->add('display', ChoiceType::class, [
                'label' => 'Ausgabe',
                'choices' => [
                    'Einzeln' => 0,
                    'Summiert' => 1,
                ],
                'data' => 0,
                'expanded' => true,
                'multiple' => false,
            ]);
    }
}
