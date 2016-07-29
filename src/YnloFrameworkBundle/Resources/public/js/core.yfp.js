/*
 * This file is part of the YNLOFramework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

YnloFramework.Core = {
    init: function () {
        YnloFramework.debug =  this.config.debug;
    },
    config: {
        debug: false
    }
};
YnloFramework.register('Core');