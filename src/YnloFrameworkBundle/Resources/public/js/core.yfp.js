/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
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