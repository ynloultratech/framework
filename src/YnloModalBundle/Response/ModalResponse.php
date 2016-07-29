<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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