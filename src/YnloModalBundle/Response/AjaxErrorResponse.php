<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace YnloFramework\YnloModalBundle\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

class AjaxErrorResponse extends JsonResponse
{
    /**
     * @inheritDoc
     */
    public function __construct($errorMsg)
    {
        parent::__construct(['result' => 'error', 'error' => $errorMsg]);
    }
}