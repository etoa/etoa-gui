<?php declare(strict_types=1);

namespace EtoA\Form\Type\Core;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use EtoA\User\ProfileImage;

class ProfileUploadType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $maxSize = ProfileImage::IMAGE_MAX_SIZE ?? 4194304;
        $maxWidth = ProfileImage::IMAGE_MAX_WIDTH;
        $maxHeight = ProfileImage::IMAGE_MAX_HEIGHT;

        $resolver->setDefaults([
            'mapped' => false,
            'required' => false,
            'constraints' => [
                new Image([
                    'maxSize' => $maxSize,
                    'maxHeight' => $maxHeight,
                    'maxWidth' => $maxWidth,
                    'mimeTypesMessage' => 'Das Bild muss vom Typ JPEG, PNG oder GIF sein!',
                    'maxSizeMessage' => 'Das Bild ist zu gross ({{ size }} {{ suffix }}), max. {{ limit }} {{ suffix }} erlaubt)!',
                    'maxHeightMessage' => 'Das Bild ist zu hoch ({{ height }}px, max {{ max_height }}px erlaubt)!',
                    'maxWidthMessage' => 'Das Bild ist zu breit ({{ width }}px, max {{ max_width }}px erlaubt)!'
                ])
            ],
        ]);
    }

    public function getParent(): string
    {
        return FileType::class;
    }
}