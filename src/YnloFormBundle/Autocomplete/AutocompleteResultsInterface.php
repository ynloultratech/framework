<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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