<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace YnloFramework\YnloFrameworkBundle\Component\TaggedServices;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class TaggedServicesCompilerPass implements CompilerPassInterface
{
    /**
     * @inheritDoc
     * @throws \InvalidArgumentException
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('ynlo.tagged_services')) {
            return;
        }

        $manager = $container->getDefinition('ynlo.tagged_services');

        $definitions = $container->getDefinitions();
        foreach ($definitions as $id => $definition) {
            foreach ($definition->getTags() as $tagName => $tagAttributes) {
                $manager->addMethodCall(
                    'addSpecification',
                    [$id, $tagName, $tagAttributes[0]]
                );
            }
        }
    }
}