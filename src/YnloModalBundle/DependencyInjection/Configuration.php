<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloModalBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use YnloFramework\YnloFrameworkBundle\DependencyInjection\AssetRegister\AssetConfiguration;

class Configuration implements ConfigurationInterface
{
    use AssetConfiguration;

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ynlo_modal')->addDefaultsIfNotSet()->children();

        $this->createAssetConfig($rootNode, [
            'bootstrap_dialog_js' => 'bundles/ynlomodal/vendor/bootstrap-dialog/bootstrap-dialog.min.js',
            'bootstrap_dialog_css' => 'bundles/ynlomodal/vendor/bootstrap-dialog/bootstrap-dialog.min.css',
        ]);

        $rootNode->scalarNode('spinicon')->defaultValue('fa fa-spinner fa-pulse');
        $rootNode->scalarNode('loaderTemplate')->defaultValue('<div class="loader loader-lg"></div>');
        $rootNode->scalarNode('loaderDialogClass')->defaultValue('modal-remote-loader');
        $rootNode->enumNode('size')->defaultValue('size-normal')->values(['size-normal', 'size-wide', 'size-large', 'size-small']);

        $modalForm = $rootNode->arrayNode('modal_form')->addDefaultsIfNotSet()
            ->info('Default template to create modal forms.')
            ->children();

        $modalForm->scalarNode('template')->defaultValue('YnloModalBundle::modal_form.html.twig');

        $default = [
            'close' => [
                'label' => 'modals.btn.cancel',
                'translation_domain' => 'YnloModalBundle',
                'icon' => null,
                'class' => 'btn-link',
                'action' => 'close',
            ],
            'ok' => [
                'label' => 'modals.btn.ok',
                'translation_domain' => 'YnloModalBundle',
                'icon' => null,
                'class' => 'btn-primary',
                'action' => 'submit',
            ],
        ];
        $buttons = $modalForm->arrayNode('buttons')
            ->useAttributeAsKey('id')
            ->defaultValue($default)
            ->example($default)
            ->prototype('array');

        $button = $buttons->children();

        /* @var $button NodeBuilder */
        $button->scalarNode('label');
        $button->scalarNode('translation_domain');
        $button->scalarNode('class')->defaultValue('btn-default');
        $button->scalarNode('icon');
        $button->scalarNode('action');

        $urlParams = $rootNode->arrayNode('url_params')->info('Query params to use in the url to create modals.')->children();
        $urlParams->scalarNode('target')->defaultValue('data-target');
        $urlParams->scalarNode('refresh')->defaultValue('data-refresh');
        $urlParams->scalarNode('redirect')->defaultValue('data-redirect');

        $urlParams = $rootNode->arrayNode('data_attributes')->info('Attributes to use in links to create modals.')->children();
        $urlParams->scalarNode('target')->defaultValue('data-target');
        $urlParams->scalarNode('refresh')->defaultValue('data-refresh');
        $urlParams->scalarNode('redirect')->defaultValue('data-redirect');

        return $treeBuilder;
    }
}
