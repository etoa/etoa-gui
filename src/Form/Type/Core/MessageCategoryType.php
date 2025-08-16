<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use EtoA\Message\MessageCategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageCategoryType extends AbstractType
{
    public function __construct(
        private readonly MessageCategoryRepository $messageCategoryRepository
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'placeholder' => '(Alle)',
            'choices' => $this->messageCategoryRepository->findBy([],['order'=>'DESC']),
            'choice_value' => 'id',
            'choice_label' => 'name',
        ]);
    }

    public function getParent(): string
    {
        return SearchableChoiceType::class;
    }
}
