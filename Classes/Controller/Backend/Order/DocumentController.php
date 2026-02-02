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
use Extcode\CartPdf\Service\FileWriterService;
use Extcode\CartPdf\Service\PdfService;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class DocumentController extends ActionController
{
    public function __construct(
        protected readonly PersistenceManager $persistenceManager,
        protected readonly ItemRepository $itemRepository
    ) {}

    public function createAction(Item $orderItem, string $pdfType): ResponseInterface
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

        return $this->redirect('show', 'Backend\Order\Order', null, ['orderItem' => $orderItem]);
    }

    public function downloadAction(Item $orderItem, string $pdfType): ResponseInterface
    {
        $getter = 'get' . ucfirst($pdfType) . 'Pdfs';
        $pdfs = $orderItem->$getter();
        $pdfs = $pdfs->toArray();
        $originalPdf = end($pdfs)->getOriginalResource();

        if ($originalPdf) {
            return $this->responseFactory->createResponse()
                ->withHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
                ->withHeader('Content-Description', 'File Transfer')
                ->withHeader('Content-Disposition', 'attachment; filename="' . $originalPdf->getName() . '"')
                ->withHeader('Content-Length', (string)$originalPdf->getSize())
                ->withHeader('Content-Transfer-Encoding', 'binary')
                ->withHeader('Content-Type', 'application/pdf')
                ->withHeader('Expires', '0')
                ->withHeader('Pragma', 'public')
                ->withBody($this->streamFactory->createStream($originalPdf->getContents()));
        }

        return $this->htmlResponse();
    }

    protected function generatePdfDocument(Item $orderItem, string $pdfType): void
    {
        if (ExtensionManagementUtility::isLoaded('cart_pdf') && (class_exists(PdfService::class) && class_exists(FileWriterService::class))) {
            $documentRenderService = GeneralUtility::makeInstance(
                PdfService::class
            );
            $fileWriterService = GeneralUtility::makeInstance(
                FileWriterService::class
            );
            $fileWriterService->writeContentToFile(
                $orderItem,
                $pdfType,
                $documentRenderService->renderDocument($orderItem, $pdfType)
            );
        }
    }
}
