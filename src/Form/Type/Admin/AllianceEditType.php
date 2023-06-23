<?php declare(strict_types=1);

namespace EtoA\Form\Type\Admin;

use EtoA\Alliance\AllianceWithMemberCount;
use EtoA\Form\Type\Core\UserType;
use EtoA\User\UserSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;

class AllianceEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var AllianceWithMemberCount $data */
        $data = $options['data'];
        $builder
            ->add('id', TextType::class, [
                'disabled' => 'disabled',
                'label' => 'ID',
            ])
            ->add('tag', TextType::class, [
                'label' => 'Tag',
            ])
            ->add('name', TextType::class, [
                'label' => 'Name',
            ])
            ->add('founderId', UserType::class, [
                'label' => 'Gründer',
                'required' => true,
                'placeholder' => false,
                'search' => UserSearch::create()->allianceId($data->id),
            ])
            ->add('text', TextareaType::class, [
                'label' => 'Text',
                'required' => false,
            ])
            ->add('foundationTimestamp', DateTimeType::class, [
                'label' => 'Gründung',
                'disabled' => 'disabled',
                'input' => 'timestamp',
            ])
            ->add('url', UrlType::class, [
                'label' => 'Website',
                'required' => false,
            ])
            ->add('applicationTemplate', TextareaType::class, [
                'label' => 'Bewerbungsvorlage',
                'required' => false,
            ]);

        if ((bool) $data->image) {
            $builder
                ->add('deleteImage', CheckboxType::class, [
                    'label' => 'Bild entfernen',
                    'required' => false,
                    'mapped' => false,
                ]);
        }

        $builder
            ->add('submit', SubmitType::class, [
                'label' => 'Übernehmen',
            ]);
    }
}
