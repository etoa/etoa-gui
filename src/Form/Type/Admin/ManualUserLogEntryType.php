<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;

class ManualUserLogEntryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('message', TextareaType::class, [
                'label' => 'Inhalt',
                'required' => true,
                'constraints' => [new Length(['min' => 3])],
                'attr' => [
                    'rows' => "4",
                    'cols' => "70",
                ]
            ])
            ->add('send', SubmitType::class, [
                'label' => 'Speichern',
            ]);
    }
}
