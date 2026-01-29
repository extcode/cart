<?php

declare(strict_types=1);

namespace Extcode\Cart\Controller\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Controller\ActionController;
use Extcode\Cart\Domain\Model\Order\Item;
use Extcode\Cart\Domain\Repository\Order\ItemRepository;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Pagination\SimplePagination;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Annotation\IgnoreValidation;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Pagination\QueryResultPaginator;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class OrderController extends ActionController
{
    protected array $searchArguments = [];
    protected array $pluginSettings;

    public function __construct(
        private readonly Context $context,
        protected ItemRepository $itemRepository
    ) {}

    protected function initializeAction(): void
    {
        $this->pluginSettings
            = $this->configurationManager->getConfiguration(
                ConfigurationManager::CONFIGURATION_TYPE_FRAMEWORK
            );
    }

    public function listAction(int $currentPage = 1): ResponseInterface
    {
        $this->view->assign('searchArguments', $this->searchArguments);

        $feUserUid = $this->context->getPropertyFromAspect('frontend.user', 'id');
        $orderItems = $this->itemRepository->findBy(['feUser' => $feUserUid]);

        $itemsPerPage = (isset($this->settings['itemsPerPage']) && is_numeric($this->settings['itemsPerPage'])) ? (int)$this->settings['itemsPerPage'] : 20;

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

        $this->dispatchModifyViewEvent();

        return $this->htmlResponse();
    }

    #[IgnoreValidation(['value' => 'orderItem'])]
    public function showAction(Item $orderItem): ResponseInterface
    {
        $feUserUid = $this->context->getPropertyFromAspect('frontend.user', 'id');
        if ($orderItem->getFeUser()->getUid() !== $feUserUid) {
            $this->addFlashMessage(
                'Access denied.',
                '',
                ContextualFeedbackSeverity::ERROR
            );
            return $this->redirect('list');
        }

        $this->view->assign('orderItem', $orderItem);

        $paymentStatusOptions = [];
        $items = $GLOBALS['TCA']['tx_cart_domain_model_order_payment']['columns']['status']['config']['items'];
        foreach ($items as $item) {
            $paymentStatusOptions[$item['value']] = LocalizationUtility::translate(
                $item['label'],
                'Cart'
            );
        }
        $this->view->assign('paymentStatusOptions', $paymentStatusOptions);

        $shippingStatusOptions = [];
        $items = $GLOBALS['TCA']['tx_cart_domain_model_order_shipping']['columns']['status']['config']['items'];
        foreach ($items as $item) {
            $shippingStatusOptions[$item['value']] = LocalizationUtility::translate(
                $item['label'],
                'Cart'
            );
        }
        $this->view->assign('shippingStatusOptions', $shippingStatusOptions);

        $pdfRendererInstalled = ExtensionManagementUtility::isLoaded('cart_pdf');
        $this->view->assign('pdfRendererInstalled', $pdfRendererInstalled);

        $this->dispatchModifyViewEvent();

        return $this->htmlResponse();
    }
}
