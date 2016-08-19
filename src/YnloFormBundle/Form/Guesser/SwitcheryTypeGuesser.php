<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloFormBundle\Form\Guesser;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Symfony\Bridge\Doctrine\Form\DoctrineOrmTypeGuesser;
use Symfony\Component\Form\Guess\Guess;
use Symfony\Component\Form\Guess\TypeGuess;
use YnloFramework\YnloFormBundle\Form\Type\SwitcheryType;

/**
 * Class SwitcheryTypeGuesser.
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
        list($metadata) = $ret;

        //fix to read embedded properties
        if (!$metadata->hasField($property)) {
            $property = str_replace('__', '.', $property);
        }

        if ($metadata->getTypeOfField($property) == Type::BOOLEAN) {
            return new TypeGuess(SwitcheryType::class, [], Guess::VERY_HIGH_CONFIDENCE);
        }

        return;
    }
}
