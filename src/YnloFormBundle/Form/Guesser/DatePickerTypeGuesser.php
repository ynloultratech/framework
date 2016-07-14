<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace YnloFramework\YnloFormBundle\Form\Guesser;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use YnloFramework\YnloFormBundle\Form\Type\DatePickerType;
use YnloFramework\YnloFormBundle\Form\Type\DateTimePickerType;
use Symfony\Bridge\Doctrine\Form\DoctrineOrmTypeGuesser;
use Symfony\Component\Form\Guess\Guess;
use Symfony\Component\Form\Guess\TypeGuess;

/**
 * Class DatePickerTypeGuesser
 */
class DatePickerTypeGuesser extends DoctrineOrmTypeGuesser
{
    /**
     * {@inheritdoc}
     */
    public function guessType($class, $property)
    {
        if (!$ret = $this->getMetadata($class)) {
            return new TypeGuess('text', [], Guess::LOW_CONFIDENCE);
        }

        /** @var ClassMetadataInfo $metadata */
        list($metadata,) = $ret;

        //fix to read embedded properties
        if (!$metadata->hasField($property)) {
            $property = str_replace('__', '.', $property);
        }

        switch ($metadata->getTypeOfField($property)) {
            case Type::DATE:
                return new TypeGuess(DatePickerType::class, [], Guess::VERY_HIGH_CONFIDENCE);
            case Type::DATETIME:
                return new TypeGuess(DateTimePickerType::class, [], Guess::VERY_HIGH_CONFIDENCE);
        }

        return null;
    }
}