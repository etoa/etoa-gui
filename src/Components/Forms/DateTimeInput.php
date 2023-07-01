<?php

namespace EtoA\Components\Forms;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsTwigComponent('forms:date_time_input')]
class DateTimeInput
{
    public string $name;

    public int $value = 0;

    #[PreMount]
    public function preMount(array $data): array
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired('name');
        $resolver->setDefined('value');
        return $resolver->resolve($data);
    }
}