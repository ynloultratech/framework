<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace YnloFramework\YnloFormBundle\Autocomplete;

interface AutocompleteResultsInterface extends \Countable, \IteratorAggregate, \ArrayAccess
{
    /**
     * Array of results
     *
     * @return array
     */
    public function toArray();

    /**
     * Should return the amount of items available
     * in case of partial result this should be greater than
     * current $elements count
     *
     * @return boolean
     */
    public function getTotalOverAll();

    /**
     * @param integer $total
     *
     * @return $this
     */
    public function setTotalOverAll($total);
}