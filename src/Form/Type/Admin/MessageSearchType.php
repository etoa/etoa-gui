<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Form\Type\Core\MessageCategoryType;
use EtoA\Form\Type\Core\UserType;
use EtoA\Form\Type\Core\YesNoMaybeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class MessageSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sender', UserType::class)
            ->add('recipient', UserType::class, [
                'label' => 'Empfänger',
            ])
            ->add('subject', TextType::class, [
                'label' => 'Betreff',
            ])
            ->add('text', TextType::class, [
                'label' => 'Text',
            ])
            ->add('category', MessageCategoryType::class, [
                'label' => 'Kategorie',
            ])
            ->add('read', YesNoMaybeType::class, [
                'label' => 'Gelesen',
            ])
            ->add('deleted', YesNoMaybeType::class, [
                'label' => 'Gelöscht',
            ])
            ->add('archived', YesNoMaybeType::class, [
                'label' => 'Archiviert',
            ])
            ->add('massmail', YesNoMaybeType::class, [
                'label' => 'Rundmail',
            ]);
    }
}
