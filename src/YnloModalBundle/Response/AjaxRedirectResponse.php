<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloModalBundle\Response;

class AjaxRedirectResponse extends AjaxSuccessResponse
{
    /**
     * @inheritDoc
     */
    public function __construct($uri)
    {
        parent::__construct(['redirect' => $uri]);
    }
}