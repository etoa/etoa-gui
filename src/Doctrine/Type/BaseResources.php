<?php

namespace EtoA\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Exception;
use EtoA\Universe\Resources\BaseResources as Resources;

final class BaseResources extends Type
{

    public const NAME = 'baseResources';

    /**
     * @inheritDoc
     */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform)
    {}

    /** @throws ConversionException */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof Resources) {
            return parent::convertToDatabaseValue($value->toString(), $platform);
        }

        throw ConversionException::conversionFailedInvalidType(
            $value,
            $this->getName(),
            ['null', Resources::class],
        );
    }

    /** @throws ConversionException */
    public function convertToPHPValue($value, AbstractPlatform $platform): Resources
    {
        /** @var string|null $value */
        $value = parent::convertToPHPValue($value, $platform);
        $baseRes = new Resources();

        if ($value === null) {
            return $baseRes;
        }

        try {
            $res = explode(',',$value);
            $baseRes->metal = $res[0];
            $baseRes->crystal = $res[1];
            $baseRes->plastic = $res[2];
            $baseRes->fuel = $res[3];
            $baseRes->food = $res[4];
            $baseRes->people = $res[5];

            return $baseRes;
        } catch (Exception $e) {
            throw ConversionException::conversionFailed(
                $value,
                $this->getName(),
                $e,
            );
        }
    }


    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return self::NAME;
    }
}