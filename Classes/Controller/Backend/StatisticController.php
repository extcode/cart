<?php

namespace Extcode\Cart\Controller\Backend;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * Statistic Controller
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class StatisticController extends \Extcode\Cart\Controller\Backend\ActionController
{
    /**
     * Order Item Repository
     *
     * @var \Extcode\Cart\Domain\Repository\Order\ItemRepository
     */
    protected $itemRepository;

    /**
     * Search Arguments
     *
     * @var array
     */
    protected $searchArguments;

    /**
     * @param \Extcode\Cart\Domain\Repository\Order\ItemRepository $itemRepository
     */
    public function injectItemRepository(
        \Extcode\Cart\Domain\Repository\Order\ItemRepository $itemRepository
    ) {
        $this->itemRepository = $itemRepository;
    }

    /**
     * Initialize Action
     */
    protected function initializeAction()
    {
        parent::initializeAction();

        $arguments = $this->request->getArguments();
        if ($arguments['search']) {
            $this->searchArguments = $arguments['search'];
        }
    }

    /**
     * Show Action
     */
    public function showAction()
    {
        $orderItems = $this->itemRepository->findAll($this->searchArguments);

        $this->view->assign('searchArguments', $this->searchArguments);

        $statistics = [
            'gross' => 0.0,
            'net' => 0.0,
            'orderItemCount' => count($orderItems),
            'orderProductCount' => 0,
        ];

        foreach ($orderItems as $orderItem) {
            /** @var \Extcode\Cart\Domain\Model\Order\Item $orderItem */
            $statistics['orderItemGross'] += $orderItem->getGross();
            $statistics['orderItemNet'] += $orderItem->getNet();

            $orderProducts = $orderItem->getProducts();

            if ($orderProducts) {
                foreach ($orderProducts as $orderProduct) {
                    $statistics['orderProductCount'] += $orderProduct->getCount();
                }
            }
        }

        if ($statistics['orderItemCount'] > 0) {
            $statistics['orderItemAverageGross'] = $statistics['orderItemGross'] / $statistics['orderItemCount'];
            $statistics['orderItemAverageNet'] = $statistics['orderItemNet'] / $statistics['orderItemCount'];
        }

        $this->view->assign('statistics', $statistics);
    }
}
