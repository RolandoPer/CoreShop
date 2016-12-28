<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model\Mail\Rule\Condition\Order;

use CoreShop\Model;
use CoreShop\Model\Mail\Rule;
use Pimcore\Model\AbstractModel;

/**
 * Class Invoice
 * @package CoreShop\Model\Mail\Rule\Condition\Order
 */
class InvoiceState extends Rule\Condition\AbstractCondition
{
    /**
     * @var string
     */
    public $type = 'invoiceState';

    /**
     *
     */
    const INVOICE_TYPE_PARTIAL = 1;

    /**
     *
     */
    const INVOICE_TYPE_FULL = 2;

    /**
     *
     */
    const INVOICE_TYPE_ALL = 3;

    /**
     * @var int
     */
    public $invoiceState;

    /**
     * @param AbstractModel $object
     * @param array $params
     * @param Rule $rule
     *
     * @return boolean
     */
    public function checkCondition(AbstractModel $object, $params = [], Rule $rule)
    {
        if($object instanceof Model\Order) {
            if($this->getInvoiceState() === self::INVOICE_TYPE_ALL) {
                return true;
            }
            else if($this->getInvoiceState() === self::INVOICE_TYPE_FULL) {
                if(count($object->getInvoiceAbleItems()) === 0) {
                    return true;
                }
            }
            else if($this->getInvoiceState() === self::INVOICE_TYPE_PARTIAL) {
                if(count($object->getInvoiceAbleItems()) > 0) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return int
     */
    public function getInvoiceState()
    {
        return $this->invoiceState;
    }

    /**
     * @param int $invoiceState
     */
    public function setInvoiceState($invoiceState)
    {
        $this->invoiceState = $invoiceState;
    }
}