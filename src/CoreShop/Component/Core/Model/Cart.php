<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
*/

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\Order\Model\Cart as BaseCart;
use CoreShop\Component\Resource\ImplementedByPimcoreException;
use CoreShop\Component\Shipping\Model\CarrierAwareTrait;

class Cart extends BaseCart implements CartInterface
{
    use CarrierAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function getShipping($withTax = true)
    {
        return $withTax ? $this->getShippingGross() : $this->getShippingNet();
    }

    /**
     * {@inheritdoc}
     */
    public function setShipping($shipping, $withTax = true)
    {
        $withTax ? $this->setShippingGross($shipping) : $this->setShippingNet($shipping);
    }

    /**
     * calculates the total without discount.
     *
     * @param bool $withTax
     *
     * @return float
     */
    protected function getTotalWithoutDiscount($withTax = true)
    {
        return parent::getTotalWithoutDiscount($withTax) + $this->getShipping($withTax);
    }


    /**
     * {@inheritdoc}
     */
    public function getShippingNet()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingNet($shippingNet)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingGross()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingGross($shippingGross)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingTaxRate()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingTaxRate($shippingTaxRate)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }
}
