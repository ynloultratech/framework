<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloUserBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use YnloFramework\Component\KernelBuilder\KernelBuilder;

class YnloUserExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $configDir = __DIR__.'/../Resources/config';
        $loader = new YamlFileLoader($container, new FileLocator($configDir));
        $loader->load('services.yml');
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $defined = $container->getExtensionConfig('fos_user')[0];

        //fos
        if (!isset($defined['db_driver'])) {
            $fosUser['db_driver'] = 'orm';
        }

        $fosUser['user_class'] = KernelBuilder::$userClass;
        $fosUser['firewall_name'] = 'main';
        $container->prependExtensionConfig('fos_user', $fosUser);

        //security
        $security['encoders'] = [
            KernelBuilder::$userClass => [
                'algorithm' => 'md5',
                'encode_as_base64' => false,
                'iterations' => 1,
            ],
        ];

        $container->prependExtensionConfig('security', $security);
    }
}
