<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use EtoA\Entity\Message;
use EtoA\Entity\MessageData;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class MessageDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('subject', TextType::class, [
                'label'=>false,
                'attr' => [
                    'maxlength' => 255,
                    'style' => "width:97%"
                ],
                'constraints' => [new NotBlank(message: 'Bitte gib einen Betreff ein!')],
                'error_bubbling' => true
            ])
            ->add('text', CKEditorType::class, [
                'label'=>false,
                'attr' => [
                    'rows' => "12",
                    'cols'=> "60"
                ],
                'constraints' => [new NotBlank(message: 'Bitte gib einen Text ein!')],
                'error_bubbling' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MessageData::class,
        ]);
    }
}