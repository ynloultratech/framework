<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace YnloFramework\YnloModalBundle\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

class AjaxSuccessResponse extends JsonResponse
{
    /**
     * @inheritDoc
     */
    public function __construct($params = [])
    {
        parent::__construct(array_merge(['result' => 'ok'], $params));
    }
}