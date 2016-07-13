<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
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