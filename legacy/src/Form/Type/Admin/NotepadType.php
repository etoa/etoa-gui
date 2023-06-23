<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class NotepadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => false,
                'required' => true,
                'attr' => [
                    'size' => '50',
                ],
            ])
            ->add('text', TextareaType::class, [
                'required' => true,
                'label' => false,
                'attr' => [
                    'cols' => '80',
                    'rows' => '20',
                ],
            ])
        ->add('submit', SubmitType::class, [
            'label' => 'Speichern',
        ]);
    }
}
