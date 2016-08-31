<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloFormBundle\Form\Transformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class DateTimePickerTransformer implements DataTransformerInterface
{
    private $formatter;
    private $customFormat;

    public function __construct(\IntlDateFormatter $formatter, $customFormat)
    {
        $this->formatter = $formatter;
        $this->customFormat = $customFormat;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if (null === $value) {
            return '';
        }

        if (!$value instanceof \DateTime) {
            $value = new \DateTime($value);
        }

        if (null !== $this->customFormat) {
            return $this->formatter->format($this->customFormat);
        }

        return $this->formatter->format($value);
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (!is_string($value)) {
            throw new TransformationFailedException('Expected a string.');
        }

        if ('' === $value) {
            return;
        }

        return new \DateTime($value);
    }
}
