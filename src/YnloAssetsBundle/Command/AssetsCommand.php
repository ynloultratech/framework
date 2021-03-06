<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloAssetsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use YnloFramework\YnloAssetsBundle\Assets\AssetBundle;
use YnloFramework\YnloAssetsBundle\Assets\AssetRegistry;
use YnloFramework\YnloAssetsBundle\Assets\JavascriptModule;

class AssetsCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ynlo:assets')
            ->setDescription('List of all named assets registered');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $assets = AssetRegistry::getAssets();
        $table = new Table($output);
        $table->setHeaders(['Name', 'Assets', 'RequireJS']);
        $names = array_keys($assets);
        $lastName = array_pop($names);
        foreach ($assets as $name => $namedAsset) {
            if ($namedAsset instanceof AssetBundle) {
                foreach ($namedAsset->getAssets() as $index => $asset) {
                    $table->addRow([$name, $asset->getPath()]);
                    $name = '';
                }
            } else {
                $table->addRow([$namedAsset->getName(), $namedAsset->getPath(), $namedAsset instanceof JavascriptModule ? $namedAsset->getModuleName() : '']);
            }
            if ($name !== $lastName) {
                $table->addRow(new TableSeparator());
            }
        }
        $table->render();
    }
}
