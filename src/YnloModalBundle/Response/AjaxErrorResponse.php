<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloModalBundle\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

class AjaxErrorResponse extends JsonResponse
{
    /**
     * {@inheritdoc}
     */
    public function __construct($errorMsg)
    {
        parent::__construct(['result' => 'error', 'error' => $errorMsg]);
    }
}
