<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloAdminBundle\Twig\Tests;

use YnloFramework\YnloAdminBundle\Action\ActionInterface;
use YnloFramework\YnloAdminBundle\Action\BatchAction;

/**
 * AdminActionTest.
 */
class AdminActionTest extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'admin_action_test';
    }

    public function getTests()
    {
        return [
            new \Twig_SimpleTest(
                'batch_action',
                function (ActionInterface $object) {
                    return $object instanceof BatchAction;
                }
            ),
        ];
    }
}
