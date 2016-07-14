<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace YnloFramework\YnloFormBundle\Form\Transformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class DateTimePickerTransformer
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
     * @inheritdoc
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
     * @inheritdoc
     */
    public function reverseTransform($value)
    {
        return new \DateTime($value);
    }
}