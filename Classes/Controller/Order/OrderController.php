<?php

namespace Extcode\Cart\Controller\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Order\Item;
use Extcode\Cart\Domain\Repository\Order\ItemRepository;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Pagination\SimplePagination;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Pagination\QueryResultPaginator;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class OrderController extends ActionController
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
    protected $searchArguments = [];

    /**
     * Plugin Settings
     *
     * @var array
     */
    protected $pluginSettings;

    /**
     * @param \Extcode\Cart\Domain\Repository\Order\ItemRepository $itemRepository
     */
    public function injectItemRepository(
        ItemRepository $itemRepository
    ) {
        $this->itemRepository = $itemRepository;
    }

    /**
     * Initialize Action
     */
    protected function initializeAction()
    {
        $this->pluginSettings =
            $this->configurationManager->getConfiguration(
                ConfigurationManager::CONFIGURATION_TYPE_FRAMEWORK
            );
    }

    public function listAction(int $currentPage = 1): void
    {
        $this->view->assign('searchArguments', $this->searchArguments);

        $feUser = (int)$GLOBALS['TSFE']->fe_user->user['uid'];
        $orderItems = $this->itemRepository->findByFeUser($feUser);

        $itemsPerPage = $this->settings['itemsPerPage'] ?? 20;

        $arrayPaginator = new QueryResultPaginator(
            $orderItems,
            $currentPage,
            $itemsPerPage
        );
        $pagination = new SimplePagination($arrayPaginator);
        $this->view->assignMultiple(
            [
                'orderItems' => $orderItems,
                'paginator' => $arrayPaginator,
                'pagination' => $pagination,
                'pages' => range(1, $pagination->getLastPageNumber()),
            ]
        );

        $this->view->assign('orderItems', $orderItems);
    }

    /**
     * Show Action
     *
     * @param \Extcode\Cart\Domain\Model\Order\Item $orderItem
     *
     * @TYPO3\CMS\Extbase\Annotation\IgnoreValidation("orderItem")
     */
    public function showAction(Item $orderItem)
    {
        $feUser = (int)$GLOBALS['TSFE']->fe_user->user['uid'];
        if ($orderItem->getFeUser()->getUid() !== $feUser) {
            $this->addFlashMessage(
                'Access denied.',
                '',
                AbstractMessage::ERROR
            );
            $this->redirect('list');
        }

        $this->view->assign('orderItem', $orderItem);

        $paymentStatusOptions = [];
        $items = $GLOBALS['TCA']['tx_cart_domain_model_order_payment']['columns']['status']['config']['items'];
        foreach ($items as $item) {
            $paymentStatusOptions[$item[1]] = LocalizationUtility::translate(
                $item[0],
                'Cart'
            );
        }
        $this->view->assign('paymentStatusOptions', $paymentStatusOptions);

        $shippingStatusOptions = [];
        $items = $GLOBALS['TCA']['tx_cart_domain_model_order_shipping']['columns']['status']['config']['items'];
        foreach ($items as $item) {
            $shippingStatusOptions[$item[1]] = LocalizationUtility::translate(
                $item[0],
                'Cart'
            );
        }
        $this->view->assign('shippingStatusOptions', $shippingStatusOptions);

        $pdfRendererInstalled = ExtensionManagementUtility::isLoaded('cart_pdf');
        $this->view->assign('pdfRendererInstalled', $pdfRendererInstalled);
    }
}
