<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloFormBundle\Form\Transformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class DateTimePickerTransformer.
 */
class DateTimePickerTransformer implements DataTransformerInterface
{
    /**
     * @var \IntlDateFormatter
     */
    protected $formatter;

    /**
     * DateTimePickerTransformer constructor.
     *
     * @param \IntlDateFormatter $formatter
     */
    public function __construct(\IntlDateFormatter $formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if ($value) {
            if (!$value instanceof \DateTime) {
                $value = new \DateTime($value);
            }
            $value = $this->formatter->format($value);
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        return new \DateTime($value);
    }
}
