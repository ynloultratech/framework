<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace YnloFramework\YnloFrameworkBundle\Assetic;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;

/**
 * This assetic filter is used for YnloFramework pace javascript plugin
 * to dump settings just before load the script
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
     * @inheritDoc
     */
    public function filterLoad(AssetInterface $asset)
    {
    }

    /**
     * @inheritDoc
     */
    public function filterDump(AssetInterface $asset)
    {
        if (preg_match('/pace\.js$/', $asset->getSourcePath())) {
            $content = $asset->getContent();

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

JAVASCRIPT;
            //https://github.com/HubSpot/pace/issues/188
            $content = str_replace('["GET"]', "[\"GET\", \"POST\"]", $content);
            $asset->setContent($settings . $content);
        }
    }
}