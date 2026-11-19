<?php

declare(strict_types=1);

namespace Extcode\Cart\Controller\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Configuration\OrderStatus\OrderStatusRegistry;
use Extcode\Cart\Controller\ActionController;
use Extcode\Cart\Domain\Model\Order\Item;
use Extcode\Cart\Domain\Model\Order\Status\ServiceType;
use Extcode\Cart\Domain\Repository\Order\ItemRepository;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Pagination\SimplePagination;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Attribute\IgnoreValidation;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Pagination\QueryResultPaginator;

class OrderController extends ActionController
{
    protected array $searchArguments = [];

    protected array $pluginSettings;

    protected OrderStatusRegistry $orderStatusRegistry;

    public function __construct(
        private readonly Context $context,
        protected ItemRepository $itemRepository
    ) {
    }

    public function injectOrderStatusRegistry(OrderStatusRegistry $orderStatusRegistry): void
    {
        $this->orderStatusRegistry = $orderStatusRegistry;
    }

    public function listAction(int $currentPage = 1): ResponseInterface
    {
        $this->view->assign('searchArguments', $this->searchArguments);

        $feUserUid = $this->context->getPropertyFromAspect('frontend.user', 'id');
        $orderItems = $this->itemRepository->findBy(['feUser' => $feUserUid]);

        $itemsPerPage = (isset($this->settings['itemsPerPage']) && is_numeric($this->settings['itemsPerPage'])) ? (int) $this->settings['itemsPerPage'] : 20;

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

    public function showAction(
        #[IgnoreValidation]
        Item $orderItem
    ): ResponseInterface {
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

        $this->view->assign(
            'paymentStatusOptions',
            $this->orderStatusRegistry->getLocalizedLabels(ServiceType::Payment, $orderItem->getPayment()?->getStatus())
        );
        $this->view->assign(
            'shippingStatusOptions',
            $this->orderStatusRegistry->getLocalizedLabels(ServiceType::Shipping, $orderItem->getShipping()?->getStatus())
        );

        $pdfRendererInstalled = ExtensionManagementUtility::isLoaded('cart_pdf');
        $this->view->assign('pdfRendererInstalled', $pdfRendererInstalled);

        $this->dispatchModifyViewEvent();

        return $this->htmlResponse();
    }

    protected function initializeAction(): void
    {
        $this->pluginSettings
            = $this->configurationManager->getConfiguration(
                ConfigurationManager::CONFIGURATION_TYPE_FRAMEWORK
            );
    }
}
