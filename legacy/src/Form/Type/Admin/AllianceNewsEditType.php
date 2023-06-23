<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Form\Type\Core\AllianceType;
use EtoA\Form\Type\Core\UserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class AllianceNewsEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('authorAllianceId', AllianceType::class, [
                'placeholder' => '(keine)',
                'label' => 'Absender Allianz',
            ])
            ->add('authorUserId', UserType::class, [
                'placeholder' => '(keiner)',
                'label' => 'Absender Spieler',
                'required' => true,
            ])
            ->add('toAllianceId', AllianceType::class, [
                'placeholder' => '(keine)',
                'label' => 'Empfänger Allianz',
            ])
            ->add('title', TextType::class, [
                'label' => 'Titel',
            ])
            ->add('text', TextareaType::class, [
                'label' => 'Text',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Speichern',
            ])
        ;
    }
}
