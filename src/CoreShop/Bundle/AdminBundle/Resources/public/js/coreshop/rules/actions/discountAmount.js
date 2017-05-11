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

pimcore.registerNS('pimcore.plugin.coreshop.rules.actions.discountAmount');

pimcore.plugin.coreshop.rules.actions.discountAmount = Class.create(pimcore.plugin.coreshop.rules.actions.abstract, {

    type : 'discountAmount',

    getForm : function () {
        var amountValue = 0;
        var me = this;

        if (this.data) {
            amountValue = this.data.amount;
        }

        var amount = new Ext.form.NumberField({
            fieldLabel:t('coreshop_action_discountAmount_amount'),
            name:'amount',
            value : amountValue,
            decimalPrecision : 2
        });

        this.form = new Ext.form.Panel({
            items : [
                amount
            ]
        });

        return this.form;
    }
});