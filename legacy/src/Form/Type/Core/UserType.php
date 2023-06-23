<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use EtoA\User\UserRepository;
use EtoA\User\UserSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\ChoiceList\Factory\Cache\ChoiceLoader;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'required' => false,
            'placeholder' => '(Alle)',
            'search' => null,
            'with_system' => false,
            'choice_loader' => function (Options $options): ChoiceLoader {
                return ChoiceList::lazy($this, function () use ($options): array {
                    $search = $options->offsetGet('search');
                    if (!$search instanceof UserSearch) {
                        $search = UserSearch::create();
                    }

                    $choices = array_flip($this->userRepository->searchUserNicknames($search));
                    if ((bool) $options['with_system']) {
                        $choices['System'] = 0;
                    }

                    return $choices;
                });
            },
        ]);
    }

    public function getParent(): string
    {
        return SearchableChoiceType::class;
    }
}
