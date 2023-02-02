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
use Extcode\CartPdf\Service\PdfService;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class DocumentController extends ActionController
{
    protected PersistenceManager $persistenceManager;

    protected ItemRepository $itemRepository;

    public function injectPersistenceManager(PersistenceManager $persistenceManager): void
    {
        $this->persistenceManager = $persistenceManager;
    }

    public function injectItemRepository(ItemRepository $itemRepository): void
    {
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

    public function downloadAction(Item $orderItem, string $pdfType): ResponseInterface
    {
        $getter = 'get' . ucfirst($pdfType) . 'Pdfs';
        $pdfs = $orderItem->$getter();
        $originalPdf = end($pdfs->toArray())->getOriginalResource();
        $file = $originalPdf->getForLocalProcessing(false);

        $fileName = $originalPdf->getName();

        if (is_file($file)) {
            $fileLen = filesize($file);

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

        return $this->htmlResponse();
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
