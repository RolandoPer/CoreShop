/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.resource');
coreshop.resource = Class.create({
    resources: {},

    initialize: function () {
        Ext.Ajax.request({
            url: '/admin/coreshop/resource/class-map',
            success: function (response) {
                var resp = Ext.decode(response.responseText);

                coreshop.class_map = resp;

                coreshop.broker.fireEvent("afterClassMap", coreshop.class_map);
            }.bind(this)
        });

        coreshop.broker.addListener('resource.register', this.resourceRegistered, this);
    },

    resourceRegistered: function (name, resource) {
        this.resources[name] = resource;
    },

    open: function (module, resource) {
        this.resources[module].openResource(resource);
    }
});

coreshop.global.resource = new coreshop.resource();