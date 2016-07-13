<?php

namespace YnloFramework\YnloFrameworkBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AsseticNamedAssetsCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('assetic:assets')
            ->setDescription('List of named assets registered');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $assets = $this->getContainer()->getParameter('ynlo.assetic.assets');
        $table = new Table($output);
        $table->setHeaders(['Name', 'Assets']);
        $names = array_keys($assets);
        $lastName = array_pop($names);
        foreach ($assets as $name => $namedAsset) {
            if (isset($namedAsset['inputs'])) {
                foreach ($namedAsset['inputs'] as $index => $asset) {
                    if ($index === 0) {
                        $table->addRow([new TableCell($name, ['rowspan' => count($namedAsset['inputs'])]), $asset]);
                    } else {
                        $table->addRow([$asset]);
                    }
                }
                if ($name !== $lastName) {
                    $table->addRow(new TableSeparator());
                }
            }
        }
        $table->render();
    }
}
