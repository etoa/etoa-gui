<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class EditUserObserverType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('observe', TextareaType::class, [
                'label' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Speichern',
            ]);
    }
}
