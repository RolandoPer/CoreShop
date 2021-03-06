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

pimcore.registerNS('coreshop.shippingrule.actions.price');
coreshop.shippingrule.actions.price = Class.create(coreshop.rules.actions.abstract, {
    type: 'price',

    getForm: function () {
        var priceValue = 0;
        var me = this;

        if (this.data) {
            priceValue = this.data.price / 100;
        }

        var price = new Ext.form.NumberField({
            fieldLabel: t('coreshop_action_price'),
            name: 'price',
            value: priceValue,
            decimalPrecision: 2
        });

        this.form = new Ext.form.FieldSet({
            items: [
                price
            ]
        });

        return this.form;
    }
});
