<?php

/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace YnloFramework\YnloFrameworkBundle\Assetic;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;

/**
 * This assetic filter is used for YnloFramework pace javascript plugin
 * to dump settings just before load the script.
 */
class PaceSettingsDumper implements FilterInterface
{
    /**
     * @var array
     */
    protected $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function filterLoad(AssetInterface $asset)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function filterDump(AssetInterface $asset)
    {
        if (preg_match('/pace_settings\.js$/', $asset->getSourcePath())) {
            $ajax = $this->config['pace']['ajax'] ? 'true' : 'false';
            $document = $this->config['pace']['document'] ? 'true' : 'false';
            $eventLag = $this->config['pace']['eventLag'] ? 'true' : 'false';
            $restartOnPushState = $this->config['pace']['restartOnPushState'] ? 'true' : 'false';
            $restartOnRequestAfter = $this->config['pace']['restartOnRequestAfter'] ? 'true' : 'false';

            $settings
                = <<<JAVASCRIPT
var paceOptions = {
  ajax: $ajax, 
  document: $document, 
  eventLag: $eventLag,
  restartOnPushState: $restartOnPushState,
  restartOnRequestAfter: $restartOnRequestAfter,
};
require(['pace'], function (pace) {
  pace.start();
});

JAVASCRIPT;
            $asset->setContent($settings);
        }
    }
}
