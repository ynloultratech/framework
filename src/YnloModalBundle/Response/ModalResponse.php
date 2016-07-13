<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace YnloFramework\YnloModalBundle\Response;

use Rafrsr\LibArray2Object\Object2ArrayBuilder;
use Symfony\Component\HttpFoundation\Response;
use YnloFramework\YnloModalBundle\Modal\Modal;

class ModalResponse extends Response
{
    /**
     * @param Modal $modal
     * @throws \InvalidArgumentException
     */
    public function __construct(Modal $modal)
    {
        $modalJson =  json_encode(Object2ArrayBuilder::create()->build()->createArray($modal));

        parent::__construct(
            $modalJson, 200, [
                'X-Modal' => true,
                'Content-Type'=>'application/json'
            ]
        );
    }
}