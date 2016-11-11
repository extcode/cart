<?php

namespace Extcode\Cart\Controller;

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

use TYPO3\CMS\Core\Utility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Order Controller
 *
 * @package cart
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class OrderController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * Persistence Manager
     *
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     */
    protected $persistenceManager;

    /**
     * Order Item Repository
     *
     * @var \Extcode\Cart\Domain\Repository\Order\ItemRepository
     */
    protected $itemRepository;

    /**
     * Order Payment Repository
     *
     * @var \Extcode\Cart\Domain\Repository\Order\PaymentRepository
     */
    protected $paymentRepository;

    /**
     * Cart Repository
     *
     * @var \Extcode\Cart\Domain\Repository\CartRepository
     */
    protected $cartRepository;

    /**
     * @var \Extcode\Cart\Domain\Model\Cart
     */
    protected $cart = null;

    /**
     * Order Utility
     *
     * @var \Extcode\Cart\Utility\OrderUtility
     */
    protected $orderUtility;

    /**
     * Search Arguments
     *
     * @var array
     */
    protected $searchArguments;

    /**
     * Plugin Settings
     *
     * @var array
     */
    protected $pluginSettings;

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager $persistenceManager
     */
    public function injectPersistenceManager(
        \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager $persistenceManager
    ) {
        $this->persistenceManager = $persistenceManager;
    }

    /**
     * @param \Extcode\Cart\Domain\Repository\Order\ItemRepository $itemRepository
     */
    public function injectItemRepository(
        \Extcode\Cart\Domain\Repository\Order\ItemRepository $itemRepository
    ) {
        $this->itemRepository = $itemRepository;
    }

    /**
     * @param \Extcode\Cart\Domain\Repository\Order\PaymentRepository $paymentRepository
     */
    public function injectPaymentRepository(
        \Extcode\Cart\Domain\Repository\Order\PaymentRepository $paymentRepository
    ) {
        $this->paymentRepository = $paymentRepository;
    }

    /**
     * @param \Extcode\Cart\Domain\Repository\CartRepository $cartRepository
     */
    public function injectCartRepository(
        \Extcode\Cart\Domain\Repository\CartRepository $cartRepository
    ) {
        $this->cartRepository = $cartRepository;
    }

    /**
     * @param \Extcode\Cart\Utility\OrderUtility $orderUtility
     */
    public function injectOrderUtility(
        \Extcode\Cart\Utility\OrderUtility $orderUtility
    ) {
        $this->orderUtility = $orderUtility;
    }

    /**
     * Initialize Action
     *
     * @return void
     */
    protected function initializeAction()
    {
        $this->pluginSettings =
            $this->configurationManager->getConfiguration(
                \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
            );

        if (TYPO3_MODE === 'BE') {
            $pageId = (int)(GeneralUtility::_GET('id')) ? Utility\GeneralUtility::_GET('id') : 1;

            $this->pageinfo = \TYPO3\CMS\Backend\Utility\BackendUtility::readPageAccess(
                $pageId,
                $GLOBALS['BE_USER']->getPagePermsClause(1)
            );

            $configurationManager = $this->objectManager->get(
                'TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManagerInterface'
            );

            $frameworkConfiguration =
                $configurationManager->getConfiguration(
                    \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
                );
            $persistenceConfiguration = ['persistence' => ['storagePid' => $pageId]];
            $configurationManager->setConfiguration(array_merge($frameworkConfiguration, $persistenceConfiguration));

            $this->settings = $configurationManager->getConfiguration(
                \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
                $this->request->getControllerExtensionName(),
                $this->request->getPluginName()
            );

            $arguments = $this->request->getArguments();
            if ($arguments['search']) {
                $this->searchArguments = $arguments['search'];
            }
        }
    }

    /**
     * Initialize Update Action
     *
     * @return void
     */
    public function initializeUpdateAction()
    {
        if ($this->request->hasArgument('orderItem')) {
            $orderItem = $this->request->getArgument('orderItem');

            $invoiceDateString = $orderItem['invoiceDate'];
            $orderItem['invoiceDate'] = \DateTime::createFromFormat('d.m.Y', $invoiceDateString);

            $this->request->setArgument('orderItem', $orderItem);
        }
    }

    /**
     * Statistic Action
     *
     * @return void
     */
    public function statisticAction()
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

    /**
     * List Action
     *
     * @return void
     */
    public function listAction()
    {
        if (TYPO3_MODE === 'BE') {
            $orderItems = $this->itemRepository->findAll($this->searchArguments);
        } else {
            $feUser = (int)$GLOBALS['TSFE']->fe_user->user['uid'];
            $orderItems = $this->itemRepository->findByFeUser($feUser);
        }
        $this->view->assign('searchArguments', $this->searchArguments);
        $this->view->assign('orderItems', $orderItems);

        $this->view->assign('paymentStatus', $this->getPaymentStatus());
        $this->view->assign('shippingStatus', $this->getShippingStatus());

        $pdfRendererInstalled = Utility\ExtensionManagementUtility::isLoaded('cart_pdf');
        $this->view->assign('pdfRendererInstalled', $pdfRendererInstalled);
    }

    /**
     * Export Action
     *
     * @return void
     */
    public function exportAction()
    {
        $format = $this->request->getFormat();

        if ($format == 'csv') {
            $title = 'Order-Export-' . date('Y-m-d_H-i');
            $filename = $title . '.' . $format;

            $this->response->setHeader('Content-Type', 'text/' . $format, true);
            $this->response->setHeader('Content-Description', 'File transfer', true);
            $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"', true);
        }

        $orderItems = $this->itemRepository->findAll($this->searchArguments);

        $this->view->assign('searchArguments', $this->searchArguments);
        $this->view->assign('orderItems', $orderItems);

        $pdfRendererInstalled = Utility\ExtensionManagementUtility::isLoaded('cart_pdf');
        $this->view->assign('pdfRendererInstalled', $pdfRendererInstalled);
    }

    /**
     * Show Action
     *
     * @param \Extcode\Cart\Domain\Model\Order\Item $orderItem
     *
     * @ignorevalidation $orderItem
     *
     * @return void
     */
    public function showAction(\Extcode\Cart\Domain\Model\Order\Item $orderItem)
    {
        if (TYPO3_MODE === 'FE') {
            $feUser = (int)$GLOBALS['TSFE']->fe_user->user['uid'];
            if ($orderItem->getFeUser() != $feUser) {
                $this->addFlashMessage(
                    'Access denied.',
                    '',
                    \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR
                );
                $this->redirect('list');
            }
        }

        $this->view->assign('orderItem', $orderItem);

        $pdfRendererInstalled = Utility\ExtensionManagementUtility::isLoaded('cart_pdf');
        $this->view->assign('pdfRendererInstalled', $pdfRendererInstalled);
    }

    /**
     * Edit Action
     *
     * @param \Extcode\Cart\Domain\Model\Order\Item $orderItem
     *
     * @return void
     */
    public function editAction(\Extcode\Cart\Domain\Model\Order\Item $orderItem)
    {
        $this->view->assign('orderItem', $orderItem);
    }

    /**
     * Update Action
     *
     * @param \Extcode\Cart\Domain\Model\Order\Item $orderItem
     *
     * @return void
     */
    public function updateAction(\Extcode\Cart\Domain\Model\Order\Item $orderItem)
    {
        $this->itemRepository->update($orderItem);
        $this->persistenceManager->persistAll();

        $this->redirect('show', null, null, ['orderItem' => $orderItem]);
    }

    /**
     * Generate Invoice Number Action
     *
     * @param \Extcode\Cart\Domain\Model\Order\Item $orderItem
     *
     * @return void
     */
    public function generateInvoiceNumberAction(\Extcode\Cart\Domain\Model\Order\Item $orderItem)
    {
        if (!$orderItem->getInvoiceNumber()) {
            $invoiceNumber = $this->generateInvoiceNumber($orderItem);
            $orderItem->setInvoiceNumber($invoiceNumber);
            $orderItem->setInvoiceDate(new \DateTime());

            $this->itemRepository->update($orderItem);
            $this->persistenceManager->persistAll();

            $msg = 'Invoice Number ' . $invoiceNumber . ' was generated.';

            $this->addFlashMessage($msg);
        }

        $this->redirect('show', null, null, ['orderItem' => $orderItem]);
    }

    /**
     * Generate Invoice Document Action
     *
     * @param \Extcode\Cart\Domain\Model\Order\Item $orderItem
     * @param string $pdfType
     *
     * @return void
     */
    public function generatePdfDocumentAction(\Extcode\Cart\Domain\Model\Order\Item $orderItem, $pdfType)
    {
        if ($pdfType == 'invoice') {
            if (!$orderItem->getInvoiceNumber()) {
                $invoiceNumber = $this->generateInvoiceNumber($orderItem);
                $orderItem->setInvoiceNumber($invoiceNumber);
                $orderItem->setInvoiceDate(new \DateTime());

                $this->addFlashMessage(
                    'Invoice Number was generated.',
                    $messageTitle = '',
                    $severity = \TYPO3\CMS\Core\Messaging\AbstractMessage::OK,
                    $storeInSession = true
                );

                $this->itemRepository->update($orderItem);

                $this->persistenceManager->persistAll();
            }
        }

        $this->generatePdfDocument($orderItem, $pdfType);

        $this->itemRepository->update($orderItem);
        $this->persistenceManager->persistAll();

        $msg = ucfirst($pdfType) . '-PDF-Document was generated.';
        $this->addFlashMessage($msg);

        $this->redirect('show', null, null, ['orderItem' => $orderItem]);
    }

    /**
     * Download Pdf Document Action
     *
     * @param \Extcode\Cart\Domain\Model\Order\Item $orderItem
     * @param string $pdfType
     *
     * @return void
     */
    public function downloadPdfDocumentAction(\Extcode\Cart\Domain\Model\Order\Item $orderItem, $pdfType)
    {
        $getter = 'get' . ucfirst($pdfType) . 'Pdfs';
        $pdfs = $orderItem->$getter();
        $originalPdf = end($pdfs->toArray())->getOriginalResource();
        $file = PATH_site . $originalPdf->getPublicUrl();

        $fileName = $originalPdf->getName();

        if (is_file($file)) {
            $fileLen = filesize($file);

            $headers = [
                'Pragma' => 'public',
                'Expires' => 0,
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Content-Description' => 'File Transfer',
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                'Content-Transfer-Encoding' => 'binary',
                'Content-Length' => $fileLen
            ];

            foreach ($headers as $header => $data) {
                $this->response->setHeader($header, $data);
            }

            $this->response->sendHeaders();
            @readfile($file);
        }

        //$this->redirect('list');
    }

    /**
     * Generate an Invoice Number
     *
     * @param \Extcode\Cart\Domain\Model\Order\Item $orderItem
     *
     * @return int
     */
    protected function generateInvoiceNumber(\Extcode\Cart\Domain\Model\Order\Item $orderItem)
    {
        $this->buildTSFE();
        $cartConf = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_cart.'];

        /**
         * @var \TYPO3\CMS\Extbase\Service\TypoScriptService $typoScriptService
         */
        $typoScriptService = $this->objectManager->get(
            \TYPO3\CMS\Extbase\Service\TypoScriptService::class
        );

        $configurationManager = $this->objectManager->get(
            \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::class
        );

        $cartConfiguration =
            $configurationManager->getConfiguration(
                \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
            );

        if ($cartConfiguration) {
            $pluginTypoScriptSettings = $typoScriptService->convertTypoScriptArrayToPlainArray($cartConfiguration);
        }

        //TODO replace it width dynamic var
        $pluginTypoScriptSettings['settings'] = [
            'cart' => [
                'pid' => $orderItem->getCartPid(),
            ],
        ];

        $invoiceNumber = $this->orderUtility->getInvoiceNumber($pluginTypoScriptSettings);

        return $invoiceNumber;
    }

    /**
     * Generate Pdf Document
     *
     * @param \Extcode\Cart\Domain\Model\Order\Item $orderItem
     * @param string $pdfType
     *
     * @return void
     */
    protected function generatePdfDocument(\Extcode\Cart\Domain\Model\Order\Item $orderItem, $pdfType)
    {
        $extensionManagerUtility = $this->objectManager->get(
            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::class
        );

        if ($extensionManagerUtility->isLoaded('cart_pdf')) {
            $pdfService = $this->objectManager->get(
                \Extcode\CartPdf\Service\PdfService::class
            );

            $pdfService->createPdf($orderItem, $pdfType);
        }
    }

    /**
     * Build TSFE
     *
     * @param int $pid Page Id
     *
     * @return void
     */
    protected function buildTSFE($pid = 1, $typeNum = 0)
    {
        if (!is_object($GLOBALS['TT'])) {
            $GLOBALS['TT'] = new \TYPO3\CMS\Core\TimeTracker\NullTimeTracker;
            $GLOBALS['TT']->start();
        }

        $GLOBALS['TSFE'] = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            'TYPO3\\CMS\\Frontend\\Controller\\TypoScriptFrontendController',
            $GLOBALS['TYPO3_CONF_VARS'],
            $pid,
            $typeNum
        );
        $GLOBALS['TSFE']->connectToDB();
        $GLOBALS['TSFE']->initFEuser();
        $GLOBALS['TSFE']->id = $pid;
        //$GLOBALS['TSFE']->determineId();
        //$GLOBALS['TSFE']->initTemplate();
        //$GLOBALS['TSFE']->getConfigArray();
    }

    /**
     * prepare payment status for select box
     *
     * @return array
     */
    public function getPaymentStatus()
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

    /**
     * prepare shipping status for select box
     *
     * @return array
     */
    public function getShippingStatus()
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

    /**
     * Payment Success Action
     *
     * @return void
     */
    public function paymentSuccessAction()
    {
        if ($this->request->hasArgument('hash') && !empty($this->request->getArgument('hash'))) {
            $hash = $this->request->getArgument('hash');

            $querySettings = $this->objectManager->get(
                \TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings::class
            );
            $querySettings->setStoragePageIds(array($this->settings['order']['pid']));
            $this->cartRepository->setDefaultQuerySettings($querySettings);

            $this->cart = $this->cartRepository->findOneBySHash($hash);

            if ($this->cart) {
                $orderItem = $this->cart->getOrderItem();
                $payment = $orderItem->getPayment();

                $payment->setStatus('paid');

                $this->paymentRepository->update($payment);
                $this->persistenceManager->persistAll();

                $this->addFlashMessage(
                    \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                        'tx_cart.controller.order.action.payment_cancel.successfully_paid',
                        $this->extensionName
                    ),
                    '',
                    \TYPO3\CMS\Core\Messaging\AbstractMessage::OK
                );

                $this->sendMails($orderItem, 'success', __CLASS__, __FUNCTION__);

                $this->view->assign('orderItem', $orderItem);
            } else {
                $this->view->assign('error', true);

                $this->addFlashMessage(
                    \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                        'tx_cart.controller.order.action.payment_cancel.error_occured',
                        $this->extensionName
                    ),
                    '',
                    \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR
                );
            }
        } else {
            $this->view->assign('error', true);

            $this->addFlashMessage(
                \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                    'tx_cart.controller.order.action.payment_cancel.access_denied',
                    $this->extensionName
                ),
                '',
                \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR
            );
        }
    }

    /**
     * Fail Action
     *
     * @return void
     */
    public function paymentCancelAction()
    {
        if ($this->request->hasArgument('hash') && !empty($this->request->getArgument('hash'))) {
            $hash = $this->request->getArgument('hash');

            $querySettings = $this->objectManager->get(
                \TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings::class
            );
            $querySettings->setStoragePageIds(array($this->settings['order']['pid']));
            $this->cartRepository->setDefaultQuerySettings($querySettings);

            $this->cart = $this->cartRepository->findOneByFHash($hash);

            if ($this->cart) {
                $orderItem = $this->cart->getOrderItem();
                $payment = $orderItem->getPayment();

                $payment->setStatus('canceled');

                $this->paymentRepository->update($payment);
                $this->persistenceManager->persistAll();

                $this->addFlashMessage(
                    \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                        'tx_cart.controller.order.action.payment_cancel.successfully_canceled',
                        $this->extensionName
                    ),
                    '',
                    \TYPO3\CMS\Core\Messaging\AbstractMessage::OK
                );

                $this->sendMails($orderItem, 'cancel', __CLASS__, __FUNCTION__);

                $this->view->assign('orderItem', $orderItem);
            } else {
                $this->addFlashMessage(
                    \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                        'tx_cart.controller.order.action.payment_cancel.error_occured',
                        $this->extensionName
                    ),
                    '',
                    \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR
                );
            }
        } else {
            $this->addFlashMessage(
                \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                    'tx_cart.controller.order.action.payment_cancel.access_denied',
                    $this->extensionName
                ),
                '',
                \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR
            );
        }
    }

    /**
     * Send Mails
     *
     * @param \Extcode\Cart\Domain\Model\Order\Item $orderItem
     * @paran string $type
     * @param string $class
     * @param string $function
     *
     * @return void
     */
    protected function sendMails(\Extcode\Cart\Domain\Model\Order\Item $orderItem, $type, $class, $function)
    {
        $billingAddress = $orderItem->getBillingAddress()->_loadRealInstance();
        $shippingAddress = null;
        if ($orderItem->getShippingAddress()) {
            $shippingAddress = $orderItem->getShippingAddress()->_loadRealInstance();
        }

        $data = [
            'orderItem' => $orderItem,
            'cart' => $this->cart,
            'billingAddress' => $billingAddress,
            'shippingAddress' => $shippingAddress,
        ];

        $signalSlotDispatcher = $this->objectManager->get('TYPO3\\CMS\\Extbase\\SignalSlot\\Dispatcher');
        $signalSlotDispatcher->dispatch(
            $class,
            $function . 'AfterUpdatePaymentAndBefore' . ucfirst($type) . 'Mail',
            $data
        );

        $paymentId = $orderItem->getPayment()->getServiceId();
        $paymentStatus = $orderItem->getPayment()->getStatus();
        if (intval($this->pluginSettings['payments']['options'][$paymentId]['sendBuyerEmail'][$paymentStatus]) == 1) {
            $this->sendBuyerMail($orderItem, $billingAddress, $shippingAddress);
        }
        if (intval($this->pluginSettings['payments']['options'][$paymentId]['sendSellerEmail'][$paymentStatus]) == 1) {
            $this->sendSellerMail($orderItem, $billingAddress, $shippingAddress);
        }

        $signalSlotDispatcher = $this->objectManager->get('TYPO3\\CMS\\Extbase\\SignalSlot\\Dispatcher');
        $signalSlotDispatcher->dispatch(
            $class,
            $function . 'AfterUpdatePaymentAndAfter' . ucfirst($type) . 'Mail',
            $data
        );
    }

    /**
     * Send a Mail to Buyer
     *
     * @param \Extcode\Cart\Domain\Model\Order\Item $orderItem Order Item
     * @param \Extcode\Cart\Domain\Model\Order\Address $billingAddress Billing Address
     * @param \Extcode\Cart\Domain\Model\Order\Address $shippingAddress Shipping Address
     *
     * @return void
     */
    protected function sendBuyerMail(
        \Extcode\Cart\Domain\Model\Order\Item $orderItem,
        \Extcode\Cart\Domain\Model\Order\Address $billingAddress,
        \Extcode\Cart\Domain\Model\Order\Address $shippingAddress = null
    ) {
        /* @var \Extcode\Cart\Service\MailHandler $mailHandler*/
        $mailHandler = $this->objectManager->get(
            \Extcode\Cart\Service\MailHandler::class
        );
        $mailHandler->setCart($this->cart->getCart());
        $mailHandler->sendBuyerMail($orderItem, $billingAddress, $shippingAddress);
    }

    /**
     * Send a Mail to Seller
     *
     * @param \Extcode\Cart\Domain\Model\Order\Item $orderItem Order Item
     * @param \Extcode\Cart\Domain\Model\Order\Address $billingAddress Billing Address
     * @param \Extcode\Cart\Domain\Model\Order\Address $shippingAddress Shipping Address
     *
     * @return void
     */
    protected function sendSellerMail(
        \Extcode\Cart\Domain\Model\Order\Item $orderItem,
        \Extcode\Cart\Domain\Model\Order\Address $billingAddress,
        \Extcode\Cart\Domain\Model\Order\Address $shippingAddress = null
    ) {
        /* @var \Extcode\Cart\Service\MailHandler $mailHandler*/
        $mailHandler = $this->objectManager->get(
            \Extcode\Cart\Service\MailHandler::class
        );
        $mailHandler->setCart($this->cart->getCart());
        $mailHandler->sendSellerMail($orderItem, $billingAddress, $shippingAddress);
    }
}
