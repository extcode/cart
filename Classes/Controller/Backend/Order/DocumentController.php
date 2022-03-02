<?php

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
use Extcode\CartPdf\Service\PdfService;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class DocumentController extends ActionController
{
    /**
     * @var PersistenceManager
     */
    protected $persistenceManager;

    /**
     * @var ItemRepository
     */
    protected $itemRepository;

    /**
     * @param PersistenceManager $persistenceManager
     */
    public function injectPersistenceManager(
        PersistenceManager $persistenceManager
    ) {
        $this->persistenceManager = $persistenceManager;
    }

    /**
     * @param ItemRepository $itemRepository
     */
    public function injectItemRepository(
        ItemRepository $itemRepository
    ) {
        $this->itemRepository = $itemRepository;
    }

    public function createAction(Item $orderItem, string $pdfType): void
    {
        $getNumber = 'get' . ucfirst($pdfType) . 'Number';

        if (!$orderItem->$getNumber()) {
            $dummyCart = new Cart([]);
            $createEvent = new NumberGeneratorEvent($dummyCart, $orderItem, $this->pluginSettings);
            $createEvent->setOnlyGenerateNumberOfType([$pdfType]);
            $this->eventDispatcher->dispatch($createEvent);
            $orderItem = $createEvent->getOrderItem();

            $msg = LocalizationUtility::translate(
                'tx_cart.controller.order.action.generate_number_action.' . $pdfType . '.success',
                'Cart',
                [
                    0 => $orderItem->getInvoiceNumber(),
                ]
            );

            $this->addFlashMessage($msg);

            $this->itemRepository->update($orderItem);

            $this->persistenceManager->persistAll();
        }

        $this->generatePdfDocument($orderItem, $pdfType);

        $this->itemRepository->update($orderItem);
        $this->persistenceManager->persistAll();

        $msg = ucfirst($pdfType) . '-PDF-Document was generated.';
        $this->addFlashMessage($msg);

        $this->redirect('show', 'Backend\Order\Order', null, ['orderItem' => $orderItem]);
    }

    public function downloadAction(Item $orderItem, string $pdfType)
    {
        $getter = 'get' . ucfirst($pdfType) . 'Pdfs';
        $pdfs = $orderItem->$getter();
        $originalPdf = end($pdfs->toArray())->getOriginalResource();
        $file = Environment::getPublicPath() . '/' . $originalPdf->getPublicUrl();

        $fileName = $originalPdf->getName();

        if (is_file($file)) {
            $fileLen = filesize($file);

            if ($this->responseFactory) {
                return $this->responseFactory->createResponse()
                    ->withAddedHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
                    ->withAddedHeader('Content-Description', 'File Transfer')
                    ->withAddedHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
                    ->withAddedHeader('Content-Length', $fileLen)
                    ->withAddedHeader('Content-Transfer-Encoding', 'binary')
                    ->withAddedHeader('Content-Type', 'application/pdf')
                    ->withAddedHeader('Expires', '0')
                    ->withAddedHeader('Pragma', 'public')
                    ->withBody($this->streamFactory->createStream(@readfile($file)));
            }

            if ($this->response) {
                // set response header and handle response in TYPO3 v10

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

                exit();
            }
        }
    }

    protected function generatePdfDocument(Item $orderItem, string $pdfType): void
    {
        if (ExtensionManagementUtility::isLoaded('cart_pdf')) {
            if (class_exists(PdfService::class)) {
                $pdfService = GeneralUtility::makeInstance(
                    PdfService::class
                );

                $pdfService->createPdf($orderItem, $pdfType);
            }
        }
    }
}
