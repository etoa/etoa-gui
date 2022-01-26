<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Form\Type\Core\AllianceType;
use EtoA\Form\Type\Core\RaceType;
use EtoA\Form\Type\Core\YesNoMaybeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class UserSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nickname', TextType::class)
            ->add('name', TextType::class)
            ->add('email', TextType::class)
            ->add('emailFix', TextType::class, [
                'label' => 'Fixe E-Mail',
            ])
            ->add('allianceId', AllianceType::class, [
                'label' => 'Allianz',
            ])
            ->add('raceId', RaceType::class, [
                'label' => 'Rasse',
            ])
            ->add('hmod', YesNoMaybeType::class, [
                'label' => 'Urlaubsmodus',
            ])
            ->add('blocked', YesNoMaybeType::class, [
                'label' => 'Gesperrt',
            ])
            ->add('ghost', YesNoMaybeType::class, [
                'label' => 'Geist',
            ])
            ->add('chatAdmin', YesNoMaybeType::class, [
                'label' => 'Chat-Admin',
            ]);
    }
}
