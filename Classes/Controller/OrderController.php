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
     * @inject
     */
    protected $persistenceManager;

    /**
     * Order Item Repository
     *
     * @var \Extcode\Cart\Domain\Repository\Order\ItemRepository
     * @inject
     */
    protected $itemRepository;

    /**
     * Order Utility
     *
     * @var \Extcode\Cart\Utility\OrderUtility
     * @inject
     */
    protected $orderUtility;

    /**
     * Page Id
     *
     * @var int
     */
    protected $pageId;

    /**
     * PiVars
     *
     * @var array
     */
    protected $piVars;

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
        $this->arguments->
        getArgument('orderItem')->
        getPropertyMappingConfiguration()->
        forProperty('birthday')->
        setTypeConverterOption(
            'TYPO3\\CMS\\Extbase\\Property\\TypeConverter\\DateTimeConverter',
            DateTimeConverter::CONFIGURATION_DATE_FORMAT, 'd.m.Y'
        );
    }

    /**
     * Initialize Action
     *
     * @return void
     */
    protected function initializeAction()
    {
        if (!$this->persistenceManager) {
            $this->persistenceManager = $this->objectManager->get('TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager');
        }

        $this->pageId = (int)(GeneralUtility::_GET('id')) ? Utility\GeneralUtility::_GET('id') : 1;

        if (TYPO3_MODE === 'BE') {
            $this->pageinfo = \TYPO3\CMS\Backend\Utility\BackendUtility::readPageAccess($this->id, $GLOBALS['BE_USER']->getPagePermsClause(1));

            $configurationManager = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManagerInterface');

            $frameworkConfiguration =
                $configurationManager->getConfiguration(
                    \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
                );
            $persistenceConfiguration = array('persistence' => array('storagePid' => $this->pageId));
            $configurationManager->setConfiguration(array_merge($frameworkConfiguration, $persistenceConfiguration));

            $this->settings = $configurationManager->getConfiguration(
                \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
                $this->request->getControllerExtensionName(),
                $this->request->getPluginName()
            );
        }

        $this->piVars = $this->request->getArguments();
    }

    /**
     * Statistic Action
     *
     * @return void
     */
    public function statisticAction()
    {
        $orderItems = $this->itemRepository->findAll($this->piVars);

        $this->view->assign('piVars', $this->piVars);

        $statistics = array(
            'gross' => 0.0,
            'net' => 0.0,
            'orderItemCount' => count($orderItems),
            'orderProductCount' => 0,
        );

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
            $orderItems = $this->itemRepository->findAll($this->piVars);
        } else {
            $feUser = (int) $GLOBALS['TSFE']->fe_user->user['uid'];
            $orderItems = $this->itemRepository->findByFeUser($feUser);
        }
        $this->view->assign('piVars', $this->piVars);
        $this->view->assign('orderItems', $orderItems);

        $this->view->assign('paymentStatus', $this->getPaymentStatus());
        $this->view->assign('shippingStatus', $this->getShippingStatus());

        $pdfRendererInstalled = Utility\ExtensionManagementUtility::isLoaded('wt_cart_pdf');
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

            $this->response->setHeader('Content-Type', 'text/' . $format, true);
            $this->response->setHeader('Content-Description', 'File transfer', true);
            $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $title . '.' . $format . '"',
                true);
        }

        $orderItems = $this->itemRepository->findAll($this->piVars);

        $this->view->assign('piVars', $this->piVars);
        $this->view->assign('orderItems', $orderItems);

        $pdfRendererInstalled = Utility\ExtensionManagementUtility::isLoaded('wt_cart_pdf');
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
            $feUser = (int) $GLOBALS['TSFE']->fe_user->user['uid'];
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

        $pdfRendererInstalled = Utility\ExtensionManagementUtility::isLoaded('wt_cart_pdf');
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

        $this->redirect('show', null, null, array('orderItem' => $orderItem));
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

        $this->redirect('list');
    }

    /**
     * Generate Invoice Document Action
     *
     * @param \Extcode\Cart\Domain\Model\Order\Item $orderItem
     *
     * @return void
     */
    public function generateInvoiceDocumentAction(\Extcode\Cart\Domain\Model\Order\Item $orderItem)
    {
        if (!$orderItem->getInvoiceNumber()) {
            $invoiceNumber = $this->generateInvoiceNumber($orderItem);
            $orderItem->setInvoiceNumber($invoiceNumber);

            $msg = 'Invoice Number was generated.';
            $this->flashMessageContainer->add($msg);
        }

        if ($orderItem->getInvoiceNumber()) {
            $this->generateInvoiceDocument($orderItem);

            $this->itemRepository->update($orderItem);
            $this->persistenceManager->persistAll();

            $msg = 'Invoice Document was generated.';
            $this->flashMessageContainer->add($msg);
        }

        $this->redirect('list');
    }

    /**
     * Download Invoice Document Action
     *
     * @param \Extcode\Cart\Domain\Model\Order\Item $orderItem
     *
     * @return void
     */
    public function downloadInvoiceDocumentAction(\Extcode\Cart\Domain\Model\Order\Item $orderItem)
    {
        $file = PATH_site . $orderItem->getInvoicePdf();
        $fileName = 'Invoice.pdf';

        if (is_file($file)) {
            $fileLen = filesize($file);

            $headers = array(
                'Pragma' => 'public',
                'Expires' => 0,
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Cache-Control' => 'public',
                'Content-Description' => 'File Transfer',
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                'Content-Transfer-Encoding' => 'binary',
                'Content-Length' => $fileLen
            );

            foreach ($headers as $header => $data) {
                $this->response->setHeader($header, $data);
            }

            $this->response->sendHeaders();
            @readfile($file);
        }

        $this->redirect('list');
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
        $typoScriptService = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Service\\TypoScriptService');
        $pluginTypoScriptSettings = $typoScriptService->convertTypoScriptArrayToPlainArray($cartConf);

        $invoiceNumber = $this->orderUtility->getInvoiceNumber($pluginTypoScriptSettings);

        return $invoiceNumber;
    }

    /**
     * Generate Invoice Document
     *
     * @param \Extcode\Cart\Domain\Model\Order\Item $orderItem
     *
     * @return void
     */
    protected function generateInvoiceDocument(\Extcode\Cart\Domain\Model\Order\Item $orderItem)
    {

        $this->buildTSFE($orderItem->getPid());

        $renderer = Utility\GeneralUtility::makeInstance('Tx_WtCartPdf_Utility_Renderer');

        $files = array();
        $errors = array();

        $params = array(
            'orderItem' => $orderItem,
            'type' => 'invoice',
            'files' => &$files,
            'errors' => &$errors
        );

        $renderer->createPdf($params);

        if ($params['files']['invoice']) {
            $orderItem->setInvoicePdf($params['files']['invoice']);
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
        $GLOBALS['TSFE']->determineId();
        $GLOBALS['TSFE']->initTemplate();
        $GLOBALS['TSFE']->getConfigArray();
    }

    /**
     * prepare payment status for select box
     *
     * @return array
     */
    public function getPaymentStatus()
    {
        $paymentStatusArray = array();

        $paymentStatus = new \stdClass();
        $paymentStatus->key = '';
        $paymentStatus->value = LocalizationUtility::translate(
            'tx_cart_domain_model_order_payment.status.all',
            'Cart'
        );
        $paymentStatusArray[] = $paymentStatus;

        $entries = array('open', 'pending', 'paid', 'canceled');
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
        $shippingStatusArray = array();

        $shippingStatus = new \stdClass();
        $shippingStatus->key = '';
        $shippingStatus->value = LocalizationUtility::translate(
            'tx_cart_domain_model_order_shipping.status.all',
            'Cart'
        );
        $shippingStatusArray[] = $shippingStatus;

        $entries = array('open', 'on_hold', 'in_process', 'shipped');
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
}
