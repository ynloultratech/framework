<?php

/*
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 *
 * @author YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package Mobile-ERP
 */

namespace YnloFramework\YnloFormBundle\Form\Guesser;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use YnloFramework\YnloFormBundle\Form\Type\SwitcheryType;
use Symfony\Bridge\Doctrine\Form\DoctrineOrmTypeGuesser;
use Symfony\Component\Form\Guess\Guess;
use Symfony\Component\Form\Guess\TypeGuess;

/**
 * Class SwitcheryTypeGuesser
 */
class SwitcheryTypeGuesser extends DoctrineOrmTypeGuesser
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

        if ($metadata->getTypeOfField($property) == Type::BOOLEAN) {
            return new TypeGuess(SwitcheryType::class, [], Guess::VERY_HIGH_CONFIDENCE);
        }

        return null;
    }
}