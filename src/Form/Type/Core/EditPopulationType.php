<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use EtoA\Entity\BuildingListItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditPopulationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // The template only lets the user edit peopleWorking while the workplace isn't
        // already active (see population.html.twig); disabling it here (rather than
        // just not rendering the widget) keeps this entry's form structure intact, so
        // Symfony ignores the (never submitted) value instead of writing null onto the
        // non-nullable BuildingListItem::$peopleWorking property.
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $data = $event->getData();

            $event->getForm()->add('peopleWorking', TextType::class, [
                'disabled' => $data instanceof BuildingListItem && $data->getPeopleWorkingStatus(),
            ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BuildingListItem::class,
        ]);
    }
}