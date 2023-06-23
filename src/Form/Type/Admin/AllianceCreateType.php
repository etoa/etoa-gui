<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Form\Type\Core\UserType;
use EtoA\User\UserSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class AllianceCreateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('tag', TextType::class)
            ->add('name', TextType::class)
            ->add('founder', UserType::class, [
                'required' => true,
                'placeholder' => false,
                'label' => 'Gründer',
                'search' => UserSearch::create()->allianceId(0),
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Erstellen',
            ]);
    }
}
