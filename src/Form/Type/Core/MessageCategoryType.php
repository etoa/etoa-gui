<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use EtoA\Message\MessageCategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageCategoryType extends AbstractType
{
    public function __construct(
        private MessageCategoryRepository $messageCategoryRepository
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'required' => true,
            'placeholder' => '(Alle)',
            'choices' => array_flip($this->messageCategoryRepository->getNames()),
        ]);
    }

    public function getParent(): string
    {
        return SearchableChoiceType::class;
    }
}
