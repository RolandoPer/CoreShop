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

namespace CoreShop\Bundle\ProductBundle\EventListener;

use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Index\Service\IndexUpdaterServiceInterface;
use CoreShop\Component\Product\Model\CategoryInterface;
use Pimcore\Event\Model\ElementEventInterface;
use Pimcore\Event\Model\ObjectEvent;

final class CategoryPersistEventListener
{

    public function onPreUpdate(ElementEventInterface $event)
    {
        if ($event instanceof ObjectEvent) {
            $object = $event->getObject();

            if (!$object instanceof CategoryInterface) {
                return;
            }

            $parent = $object->getParent();

            if ($parent instanceof CategoryInterface) {
                $object->setParentCategory($parent);
            }
        }
    }
}
