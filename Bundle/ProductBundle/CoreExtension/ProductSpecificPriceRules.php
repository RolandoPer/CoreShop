<?php

namespace CoreShop\Bundle\ProductBundle\CoreExtension;

use CoreShop\Bundle\ProductBundle\Form\Type\ProductSpecificPriceRuleType;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Product\Model\ProductSpecificPriceRuleInterface;
use CoreShop\Component\Product\Repository\ProductSpecificPriceRuleRepositoryInterface;
use CoreShop\Component\Rule\Model\RuleInterface;
use JMS\Serializer\SerializationContext;
use Pimcore\Model\Object\ClassDefinition\Data;
use Pimcore\Model\Object\Concrete;
use Webmozart\Assert\Assert;

class ProductSpecificPriceRules extends Data
{
    /**
     * Static type of this element.
     *
     * @var string
     */
    public $fieldtype = 'coreShopProductSpecificPriceRules';

    /**
     * @var integer
     */
    public $height;

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private function getContainer() {
        return \Pimcore::getContainer();
    }

    /**
     * @return ProductSpecificPriceRuleRepositoryInterface
     */
    private function getProductSpecificPriceRuleRepository() {
        return $this->getContainer()->get('coreshop.repository.product_specific_price_rule');
    }

    /**
     * @return \Symfony\Component\Form\FormFactoryInterface
     */
    private function getFormFactory() {
        return $this->getContainer()->get('form.factory');
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    private function getEntityManager() {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }

    /**
     * @return \JMS\Serializer\SerializerInterface
     */
    private function getSerializer() {
        return $this->getContainer()->get('jms_serializer');
    }

    /**
     * @param $object
     * @return ProductSpecificPriceRuleInterface[]
     */
    public function preGetData($object) {
        Assert::isInstanceOf($object, ProductInterface::class);

        return $this->getProductSpecificPriceRuleRepository()->findForProduct($object);
    }

    /**
     * @param mixed $data
     * @param null $object
     * @param array $params
     * @return ProductSpecificPriceRuleInterface[]
     */
    public function getDataForEditmode($data, $object = null, $params = [])
    {
        if ($object instanceof ProductInterface) {
            $prices = $this->load($object, $params);

            $context = SerializationContext::create();
            $context->setSerializeNull(true);
            $context->setGroups(['Default', 'Detailed']);

            $serializedData = $this->getSerializer()->serialize($prices, 'json', $context);

            $data = json_decode($serializedData, true);

            return $data;
        }

        return [];
    }

    /**
     * @param mixed $data
     * @param null $object
     * @param array $params
     * @return ProductSpecificPriceRuleInterface[]
     */
    public function getDataFromEditmode($data, $object = null, $params = [])
    {
        $prices = [];

        if ($data) {
            foreach ($data as $dataRow) {
                $form = $this->getFormFactory()->createNamed('', ProductSpecificPriceRuleType::class);

                $form->submit($dataRow);

                if ($form->isValid()) {
                    $formData = $form->getData();
                    $formData->setProduct($object->getId());

                    $prices[] = $formData;
                }
            }
        }

        return $prices;
    }

    /**
     * @param Concrete $object
     * @param array $params
     */
    public function save($object, $params = [])
    {
        if ($object instanceof ProductInterface) {
            $getter = "get" . ucfirst($this->getName());
            $existingPriceRules =$object->$getter();

            $all = $this->load($object, $params);
            $founds = [];

            if (is_array($existingPriceRules)) {
                foreach ($existingPriceRules as $price) {
                    if ($price instanceof ProductSpecificPriceRuleInterface) {
                        $price->setProduct($object->getId());

                        $this->getEntityManager()->persist($price);
                        $this->getEntityManager()->flush();

                        $founds[] = $price->getId();
                    }
                }
            }

            foreach ($all as $price) {
                if (!in_array($price->getId(), $founds)) {
                    $this->getEntityManager()->remove($price);
                }
            }

            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param $object
     * @param array $params
     * @return ProductSpecificPriceRuleInterface[]
     */
    public function load($object, $params = [])
    {
        return $this->getProductSpecificPriceRuleRepository()->findForProduct($object);
    }
    /**
     * Returns the data which should be stored in the query columns
     *
     * @param mixed $data
     * @return string
    */
    public function getDataForQueryResource($data)
    {
        return "not_supported";
    }

    /**
     * @param mixed $data
     * @param null $relatedObject
     * @param mixed $params
     * @param null $idMapper
     * @return ProductSpecificPriceRuleInterface[]
     * @throws \Exception
     */
    public function getFromWebserviceImport($data, $relatedObject = null, $params = [], $idMapper = null)
    {
        return $this->getDataFromEditmode($this->arrayCastRecursive($data), $relatedObject, $params);
    }

    /**
     * @param \stdClass[]
     * @return array
     */
    protected function arrayCastRecursive($array)
    {
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $array[$key] = $this->arrayCastRecursive($value);
                }
                if ($value instanceof \stdClass) {
                    $array[$key] = $this->arrayCastRecursive((array)$value);
                }
            }
        }
        if ($array instanceof \stdClass) {
            return $this->arrayCastRecursive((array)$array);
        }
        return $array;
    }
}