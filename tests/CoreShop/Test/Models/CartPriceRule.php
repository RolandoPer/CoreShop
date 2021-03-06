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

namespace CoreShop\Test\Models;

use Carbon\Carbon;
use CoreShop\Bundle\CoreBundle\Form\Type\Rule\Action\FreeShippingConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Rule\Condition\CarriersConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Rule\Condition\CountriesConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Rule\Condition\CurrenciesConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Rule\Condition\CustomerGroupsConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Rule\Condition\CustomersConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Rule\Condition\StoresConfigurationType;
use CoreShop\Bundle\CoreBundle\Form\Type\Rule\Condition\ZonesConfigurationType;
use CoreShop\Bundle\OrderBundle\Form\Type\CartPriceRuleActionType;
use CoreShop\Bundle\OrderBundle\Form\Type\CartPriceRuleConditionType;
use CoreShop\Bundle\OrderBundle\Form\Type\Rule\Action\DiscountAmountConfigurationType;
use CoreShop\Bundle\OrderBundle\Form\Type\Rule\Action\DiscountPercentConfigurationType;
use CoreShop\Bundle\OrderBundle\Form\Type\Rule\Condition\NestedConfigurationType;
use CoreShop\Bundle\OrderBundle\Form\Type\Rule\Condition\TimespanConfigurationType;
use CoreShop\Component\Order\Cart\Calculator\CartDiscountCalculatorInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Test\Data;
use CoreShop\Test\RuleTest;

class CartPriceRule extends RuleTest
{
    /**
     * @var CartPriceRuleInterface
     */
    protected $priceRule;

    /**
     * @var CartInterface
     */
    protected $cart;

    /**
     * Setup.
     */
    public function setUp()
    {
        parent::setUp();

        $this->cart = Data::createCartWithProducts();
        $this->cart->setCustomer(Data::$customer1);
        $this->cart->setShippingAddress(Data::$customer1->getAddresses()[0]);
        $this->cart->setInvoiceAddress(Data::$customer1->getAddresses()[0]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getConditionFormRegistryName()
    {
        return 'coreshop.form_registry.cart_price_rule.conditions';
    }

    /**
     * {@inheritdoc}
     */
    protected function getConditionValidatorName()
    {
        return 'coreshop.cart_price_rule.rule_validation.processor';
    }

    /**
     * {@inheritdoc}
     */
    protected function getConditionFormClass()
    {
        return CartPriceRuleConditionType::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getActionFormRegistryName()
    {
        return 'coreshop.form_registry.cart_price_rule.actions';
    }

    /**
     * {@inheritdoc}
     */
    protected function getActionProcessorName()
    {
        return 'coreshop.cart_price_rule.processor';
    }

    /**
     * {@inheritdoc}
     */
    protected function getActionFormClass()
    {
        return CartPriceRuleActionType::class;
    }

    /**
     * @return CartDiscountCalculatorInterface
     */
    protected function getPriceCalculator()
    {
        return $this->get('coreshop.cart.discount_calculator.price_rules');
    }

    /**
     * @return CartPriceRuleInterface
     */
    protected function createRule()
    {
        /**
         * @var CartPriceRuleInterface
         */
        $priceRule = $this->getFactory('cart_price_rule')->createNew();
        $priceRule->setName('test-rule');

        return $priceRule;
    }

    /**
     * Test Price Rule Condition Customer.
     */
    public function testPriceRuleConditionCustomer()
    {
        $this->printTestName();
        $this->assertConditionForm(CustomersConfigurationType::class, 'customers');

        $condition = $this->createConditionWithForm('customers', [
            'customers' => [Data::$customer1->getId()],
        ]);

        $this->assertRuleCondition($this->cart, $condition);
    }

    /**
     * Test Price Rule Condition Time Span.
     */
    public function testPriceRuleConditionTimeSpan()
    {
        $this->printTestName();

        $this->assertConditionForm(TimespanConfigurationType::class, 'timespan');

        $today = strtotime('12:00:00');
        $yesterday = strtotime('-1 day', $today);
        $tomorrow = strtotime('1 day', $today);

        $yesterday = Carbon::createFromTimestamp($yesterday);
        $tomorrow = Carbon::createFromTimestamp($tomorrow);

        $condition = $this->createConditionWithForm('timespan', [
            'dateFrom' => $yesterday->getTimestamp() * 1000,
            'dateTo' => $tomorrow->getTimestamp() * 1000,
        ]);

        $this->assertRuleCondition($this->cart, $condition);

        $condition = $this->createConditionWithForm('timespan', [
            'dateFrom' => $yesterday->getTimestamp() * 1000,
            'dateTo' => $yesterday->getTimestamp() * 1000,
        ]);

        $this->assertRuleCondition($this->cart, $condition, false);
    }

    /**
     * Test Price Rule Condition Country.
     */
    public function testPriceRuleConditionCountry()
    {
        $this->printTestName();
        $this->assertConditionForm(CountriesConfigurationType::class, 'countries');

        $condition = $this->createConditionWithForm('countries', [
            'countries' => [Data::$store->getBaseCountry()->getId()],
        ]);

        $this->assertRuleCondition($this->cart, $condition);
    }

    /**
     * Test Price Rule Condition Zone.
     */
    public function testPriceRuleConditionZone()
    {
        $this->printTestName();
        $this->assertConditionForm(ZonesConfigurationType::class, 'zones');

        $condition = $this->createConditionWithForm('zones', [
            'zones' => [Data::$store->getBaseCountry()->getZone()->getId()],
        ]);

        $this->assertRuleCondition($this->cart, $condition);
    }

    /**
     * Test Price Rule Condition Customer Group.
     */
    public function testPriceRuleConditionCustomerGroup()
    {
        $this->printTestName();
        $this->assertConditionForm(CustomerGroupsConfigurationType::class, 'customerGroups');

        $condition = $this->createConditionWithForm('customerGroups', [
            'customerGroups' => [Data::$customerGroup1->getId()],
        ]);

        $this->assertRuleCondition($this->cart, $condition);
    }

    /**
     * Test Price Rule Condition Carrier.
     */
    public function testPriceRuleConditionCarriers()
    {
        $this->printTestName();
        $this->assertConditionForm(CarriersConfigurationType::class, 'carriers');

        $condition = $this->createConditionWithForm('carriers', [
            'carriers' => [Data::$carrier1->getId()],
        ]);

        $cart = Data::createCartWithProducts();
        $cart->setCarrier(Data::$carrier1);

        $this->assertRuleCondition($cart, $condition);
    }

    /**
     * Test Price Rule Condition Stores.
     */
    public function testPriceRuleConditionStores()
    {
        $this->printTestName();
        $this->assertConditionForm(StoresConfigurationType::class, 'stores');

        $condition = $this->createConditionWithForm('stores', [
            'stores' => [Data::$store->getId()],
        ]);

        $this->assertRuleCondition($this->cart, $condition);
    }

    /**
     * Test Price Rule Condition Currencies.
     */
    public function testPriceRuleConditionCurrencies()
    {
        $this->printTestName();
        $this->assertConditionForm(CurrenciesConfigurationType::class, 'currencies');

        $condition = $this->createConditionWithForm('currencies', [
            'currencies' => [Data::$store->getCurrency()->getId()],
        ]);

        $this->assertRuleCondition($this->cart, $condition);
    }

    /**
     * Test Price Rule Condition Nested.
     */
    public function testPriceRuleConditionNested()
    {
        $this->printTestName();
        $this->assertConditionForm(NestedConfigurationType::class, 'nested');

        $categoriesCondition = $this->createConditionWithForm('categories', [
            'categories' => [Data::$category1->getId()],
        ]);

        $currencyCondition = $this->createConditionWithForm('currencies', [
            'currencies' => [Data::$store->getCurrency()->getId()],
        ]);

        $condition = $this->createConditionWithForm('nested', [
            'nested' => [$categoriesCondition, $currencyCondition],
            'operator' => 'AND',
        ]);

        $this->assertRuleCondition($this->cart, $condition);
    }

    /**
     * Test Price Rule Action Discount Amount.
     */
    public function testPriceRuleActionDiscountAmount()
    {
        $this->printTestName();
        $this->assertActionForm(DiscountAmountConfigurationType::class, 'discountAmount');

        $action = $this->createActionWithForm('discountAmount', [
            'amount' => 5,
        ]);

        $rule = $this->createRule();
        $rule->addAction($action);

        $this->getEntityManager()->persist($rule);
        $this->getEntityManager()->flush();

        $cart = Data::createCartWithProducts();

        $this->assertTrue($this->get('coreshop.cart_price_rule.processor')->process($rule, '', $cart));

        $discount = $this->getPriceCalculator()->getDiscount($cart, false);
        $discountWt = $this->getPriceCalculator()->getDiscount($cart, true);

        $this->assertEquals(500, $discount);
        $this->assertEquals(600, $discountWt);

        $this->getEntityManager()->remove($rule);
        $this->getEntityManager()->flush();
    }

    /**
     * Test Price Rule Action Discount Percent.
     */
    public function testPriceRuleActionDiscountPercent()
    {
        $this->printTestName();
        $this->assertActionForm(DiscountPercentConfigurationType::class, 'discountPercent');

        $action = $this->createActionWithForm('discountPercent', [
            'percent' => 10,
        ]);

        $rule = $this->createRule();
        $rule->addAction($action);

        $this->getEntityManager()->persist($rule);
        $this->getEntityManager()->flush();

        $cart = Data::createCartWithProducts();

        $this->assertTrue($this->get('coreshop.cart_price_rule.processor')->process($rule, '', $cart));

        $discount = $this->getPriceCalculator()->getDiscount($cart, false);
        $discountWt = $this->getPriceCalculator()->getDiscount($cart, true);

        $this->assertEquals(2400, $discount);
        $this->assertEquals(2880, $discountWt);

        $this->getEntityManager()->remove($rule);
        $this->getEntityManager()->flush();
    }

    /**
     * Test Price Rule Action Discount Percent.
     */
    public function testPriceRuleActionFreeShipping()
    {
        $this->printTestName();
        $this->assertActionForm(FreeShippingConfigurationType::class, 'freeShipping');

        $action = $this->createActionWithForm('freeShipping');

        $rule = $this->createRule();
        $rule->addAction($action);

        $this->getEntityManager()->persist($rule);
        $this->getEntityManager()->flush();

        $cart = Data::createCartWithProducts();
        $cart->setCarrier(Data::$carrier1);
        $cart->setCustomer(Data::$customer1);
        $cart->setShippingAddress(Data::$customer1->getAddresses()[0]);
        $cart->save();

        $shipping = $cart->getShipping(false);
        $shippingWt = $cart->getShipping(true);

        $this->assertEquals(1000, $shipping);
        $this->assertEquals(1200, $shippingWt);

        $this->assertTrue($this->get('coreshop.cart_price_rule.processor')->process($rule, '', $cart));

        $discount = $this->getPriceCalculator()->getDiscount($cart, false);
        $discountWt = $this->getPriceCalculator()->getDiscount($cart, true);

        $this->assertEquals(0, $discount);
        $this->assertEquals(0, $discountWt);

        $this->assertEquals(0, $cart->getShipping());
        $this->assertEquals(0, $cart->getShipping(false));

        $this->getEntityManager()->remove($rule);
        $this->getEntityManager()->flush();
    }
}
