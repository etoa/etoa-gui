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
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $data = $event->getData();

            $event->getForm()->add('peopleWorking', TextType::class, [
                'mapped' => false,
                'data' => $data instanceof BuildingListItem ? (string) $data->getPeopleWorking() : '0',
                'disabled' => $data instanceof BuildingListItem && $data->getPeopleWorkingStatus(),
                'attr' => [
                    'onKeyUp' => "FormatNumber(this.id,this.value, ".$data->getEntity()->getPeople().", '', '');"
                ],
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