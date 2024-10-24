<?php

declare(strict_types=1);

namespace Extcode\Cart\Controller\Backend\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Controller\Backend\ActionController;
use Extcode\Cart\Domain\Model\Cart\Cart;
use Extcode\Cart\Domain\Model\Order\Item;
use Extcode\Cart\Domain\Repository\Order\ItemRepository;
use Extcode\Cart\Event\Order\NumberGeneratorEvent;
use Extcode\Cart\Event\Template\Components\ModifyButtonBarEvent;
use Extcode\Cart\Event\Template\Components\ModifyModuleTemplateEvent;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Pagination\SimplePagination;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Annotation\IgnoreValidation;
use TYPO3\CMS\Extbase\Pagination\QueryResultPaginator;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class OrderController extends ActionController
{
    private const LANG_FILE = 'LLL:EXT:cart/Resources/Private/Language/locallang.xlf:';

    private ModuleTemplate $moduleTemplate;

    protected array $searchArguments = [];

    public function __construct(
        protected readonly ModuleTemplateFactory $moduleTemplateFactory,
        protected readonly IconFactory $iconFactory,
        protected readonly PersistenceManager $persistenceManager,
        protected readonly ItemRepository $itemRepository,
        private readonly PageRenderer $pageRenderer
    ) {}

    protected function initializeAction(): void
    {
        parent::initializeAction();

        if ($this->request->hasArgument('search')) {
            $this->searchArguments = $this->request->getArgument('search');
        }
    }

    public function listAction(int $currentPage = 1): ResponseInterface
    {
        $this->moduleTemplate = $this->moduleTemplateFactory->create($this->request);

        $this->setDocHeader($this->dispatchModifyButtonBarEvent());
        $this->addBackendAssets();

        $this->moduleTemplate->assign('settings', $this->settings);
        $this->moduleTemplate->assign('searchArguments', $this->searchArguments);

        $itemsPerPage = $this->settings['itemsPerPage'] ?? 20;

        $orderItems = $this->itemRepository->findAll($this->searchArguments);
        $arrayPaginator = new QueryResultPaginator(
            $orderItems,
            $currentPage,
            $itemsPerPage
        );
        $pagination = new SimplePagination($arrayPaginator);
        $this->moduleTemplate->assignMultiple(
            [
                'orderItems' => $orderItems,
                'paginator' => $arrayPaginator,
                'pagination' => $pagination,
                'pages' => range(1, $pagination->getLastPageNumber()),
            ]
        );

        $this->moduleTemplate->assign('paymentStatus', $this->getPaymentStatus());
        $this->moduleTemplate->assign('shippingStatus', $this->getShippingStatus());

        $pdfRendererInstalled = ExtensionManagementUtility::isLoaded('cart_pdf');
        $this->moduleTemplate->assign('pdfRendererInstalled', $pdfRendererInstalled);

        $this->dispatchModifyModuleTemplateEvent();

        return $this->moduleTemplate->renderResponse('List');
    }

    #[IgnoreValidation(['value' => 'orderItem'])]
    public function showAction(Item $orderItem): ResponseInterface
    {
        $this->moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        $this->setDocHeader($this->dispatchModifyButtonBarEvent($orderItem));

        $this->addBackendAssets();

        $this->moduleTemplate->assign('settings', $this->settings);
        $this->moduleTemplate->assign('orderItem', $orderItem);

        $paymentStatusOptions = [];
        $items = $GLOBALS['TCA']['tx_cart_domain_model_order_payment']['columns']['status']['config']['items'];
        foreach ($items as $item) {
            $paymentStatusOptions[$item['value']] = LocalizationUtility::translate(
                $item['label'],
                'Cart'
            );
        }
        $this->moduleTemplate->assign('paymentStatusOptions', $paymentStatusOptions);

        $shippingStatusOptions = [];
        $items = $GLOBALS['TCA']['tx_cart_domain_model_order_shipping']['columns']['status']['config']['items'];
        foreach ($items as $item) {
            $shippingStatusOptions[$item['value']] = LocalizationUtility::translate(
                $item['label'],
                'Cart'
            );
        }
        $this->moduleTemplate->assign('shippingStatusOptions', $shippingStatusOptions);

        $pdfRendererInstalled = ExtensionManagementUtility::isLoaded('cart_pdf');
        $this->moduleTemplate->assign('pdfRendererInstalled', $pdfRendererInstalled);

        $this->dispatchModifyModuleTemplateEvent();

        return $this->moduleTemplate->renderResponse('Show');
    }

    public function exportAction(): ResponseInterface
    {
        $format = $this->request->getFormat();
        $orderItems = $this->itemRepository->findAll($this->searchArguments);

        $this->view->assign('searchArguments', $this->searchArguments);
        $this->view->assign('orderItems', $orderItems);

        $pdfRendererInstalled = ExtensionManagementUtility::isLoaded('cart_pdf');
        $this->view->assign('pdfRendererInstalled', $pdfRendererInstalled);

        $title = 'Order-Export-' . date('Y-m-d_H-i');
        $filename = $title . '.' . $format;

        return $this->responseFactory->createResponse()
            ->withAddedHeader('Content-Type', 'text/' . $format)
            ->withAddedHeader('Content-Description', 'File transfer')
            ->withAddedHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->withBody($this->streamFactory->createStream($this->view->render()));
    }

    public function generateNumberAction(Item $orderItem, string $numberType): ResponseInterface
    {
        $getNumber = 'get' . ucfirst($numberType) . 'Number';

        if (!$orderItem->$getNumber()) {
            $dummyCart = new Cart([]);
            $createEvent = new NumberGeneratorEvent($dummyCart, $orderItem, $this->pluginSettings);
            $createEvent->setOnlyGenerateNumberOfType([$numberType]);
            $this->eventDispatcher->dispatch($createEvent);

            $msg = LocalizationUtility::translate(
                'tx_cart.controller.order.action.generate_number_action.' . $numberType . '.success',
                'Cart',
                [
                    0 => $createEvent->getOrderItem()->$getNumber(),
                ]
            );

            $this->addFlashMessage($msg);
        } else {
            $msg = LocalizationUtility::translate(
                'tx_cart.controller.order.action.generate_number_action.' . $numberType . '.already_generated',
                'Cart'
            );

            $this->addFlashMessage($msg, '', ContextualFeedbackSeverity::ERROR);
        }

        return $this->redirect('show', 'Backend\Order\Order', null, ['orderItem' => $orderItem]);
    }

    public function getPaymentStatus(): array
    {
        $paymentStatusArray = [];

        $paymentStatus = new \stdClass();
        $paymentStatus->key = '';
        $paymentStatus->value = LocalizationUtility::translate(
            'tx_cart_domain_model_order_payment.status.all',
            'Cart'
        );
        $paymentStatusArray[] = $paymentStatus;

        $entries = ['open', 'pending', 'paid', 'canceled'];
        foreach ($entries as $entry) {
            $paymentStatus = new \stdClass();
            $paymentStatus->key = $entry;
            $paymentStatus->value = LocalizationUtility::translate(
                'tx_cart_domain_model_order_payment.status.' . $entry,
                'Cart'
            );
            $paymentStatusArray[] = $paymentStatus;
        }
        return $paymentStatusArray;
    }

    public function getShippingStatus(): array
    {
        $shippingStatusArray = [];

        $shippingStatus = new \stdClass();
        $shippingStatus->key = '';
        $shippingStatus->value = LocalizationUtility::translate(
            'tx_cart_domain_model_order_shipping.status.all',
            'Cart'
        );
        $shippingStatusArray[] = $shippingStatus;

        $entries = ['open', 'on_hold', 'in_process', 'shipped'];
        foreach ($entries as $entry) {
            $shippingStatus = new \stdClass();
            $shippingStatus->key = $entry;
            $shippingStatus->value = LocalizationUtility::translate(
                'tx_cart_domain_model_order_shipping.status.' . $entry,
                'Cart'
            );
            $shippingStatusArray[] = $shippingStatus;
        }
        return $shippingStatusArray;
    }

    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }

    private function dispatchModifyButtonBarEvent(?Item $orderItem = null): array
    {
        $event = new ModifyButtonBarEvent(
            $this->request,
            $this->settings,
            $this->searchArguments,
            $orderItem
        );

        $this->eventDispatcher->dispatch($event);

        return $event->getButtons();
    }

    private function dispatchModifyModuleTemplateEvent(): void
    {
        $event = new ModifyModuleTemplateEvent(
            $this->request,
            $this->settings,
            $this->moduleTemplate
        );

        $this->eventDispatcher->dispatch($event);
    }

    private function setDocHeader(array $buttons): void
    {
        $buttonBar = $this->moduleTemplate->getDocHeaderComponent()->getButtonBar();

        foreach ($buttons as $button) {
            $title = $this->getLanguageService()->sL(self::LANG_FILE . $button['title']);
            $icon = $this->iconFactory->getIcon($button['icon'], Icon::SIZE_SMALL);

            $viewButton = $buttonBar->makeLinkButton()
                ->setHref($button['link'])
                ->setTitle($title)
                ->setShowLabelText($button['showLabel'])
                ->setIcon($icon);
            $buttonBar->addButton($viewButton, ButtonBar::BUTTON_POSITION_LEFT, $button['group']);
        }
    }

    private function addBackendAssets(): void
    {
        $this->pageRenderer->addCssFile('EXT:cart/Resources/Public/Stylesheets/Backend/style.css');

        $this->pageRenderer->loadJavaScriptModule('@extcode/cart/order-module.js');
    }
}
