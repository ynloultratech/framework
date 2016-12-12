<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloModalBundle\Response;

class AjaxRefreshResponse extends AjaxSuccessResponse
{
    /**
     * {@inheritdoc}
     */
    public function __construct($params = [])
    {
        parent::__construct(array_merge(['refresh' => true], $params));
    }
}
