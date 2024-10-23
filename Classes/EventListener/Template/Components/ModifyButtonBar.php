<?php

namespace Extcode\Cart\EventListener\Template\Components;

use Extcode\Cart\Event\Template\Components\ModifyButtonBarEvent;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;

class ModifyButtonBar
{
    public function __construct(
        private readonly UriBuilder $uriBuilder
    ) {}

    public function __invoke(ModifyButtonBarEvent $event): void
    {
        $request = $event->getRequest();

        if ($request->getControllerName() !== 'Backend\Order\Order') {
            return;
        }

        if ($request->getControllerActionName() === 'list') {
            $this->modifyListActionButtons($event);
            return;
        }

        if ($request->getControllerActionName() === 'show') {
            $this->modifyShowActionButtons($event);
            return;
        }
    }

    private function modifyListActionButtons(ModifyButtonBarEvent $event)
    {
        $event->setButtons(
            array_merge(
                $event->getButtons(),
                [ 'action-export-csv' => [
                    'link' => $this->uriBuilder->reset()->setRequest($event->getRequest())
                        ->setArguments(['searchArguments' => $event->getSearchArguments()])
                        ->setFormat('csv')
                        ->uriFor('export'),
                    'title' => 'tx_cart.controller.order.action.export.csv',
                    'icon' => 'actions-file-csv-download',
                    'group' => 1,
                    'showLabel' => true,
                ],
                ]
            )
        );
    }

    private function modifyShowActionButtons(ModifyButtonBarEvent $event)
    {
        $event->setButtons(
            array_merge(
                $event->getButtons(),
                [ 'action-close' => [
                    'link' => $this->uriBuilder->reset()->setRequest($event->getRequest())
                        ->uriFor(
                            'list'
                        ),
                    'title' => 'tx_cart.controller.order.action.close',
                    'icon' => 'actions-close',
                    'group' => 1,
                    'showLabel' => true,
                ],
                ]
            )
        );

        $event->setButtons(
            array_merge(
                $event->getButtons(),
                $this->getDocumentButtons($event, 'order', 2)
            )
        );
        $event->setButtons(
            array_merge(
                $event->getButtons(),
                $this->getDocumentButtons($event, 'invoice', 3)
            )
        );
        $event->setButtons(
            array_merge(
                $event->getButtons(),
                $this->getDocumentButtons($event, 'delivery', 4)
            )
        );
    }

    private function getDocumentButtons(ModifyButtonBarEvent $event, string $type, int $groupId): array
    {
        $buttons = [];

        $numberGetter = 'get' . ucfirst($type) . 'Number';
        $documentGetter = 'get' . ucfirst($type) . 'Pdfs';

        $numberExists = $event->getOrderItem()->$numberGetter();
        $documentExists = $event->getOrderItem()->$documentGetter()->current();

        if (!$numberExists) {
            $buttons['action-generate-' . $type . '-number'] = [
                'link' => $this->uriBuilder->reset()->setRequest($event->getRequest())
                    ->uriFor(
                        'generateNumber',
                        ['orderItem' => $event->getOrderItem(), 'numberType' => $type]
                    ),
                'title' => 'tx_cart.controller.order.action.generate' . ucfirst($type) . 'Number',
                'icon' => 'actions-duplicates',
                'group' => $groupId,
                'showLabel' => true,
            ];
        }

        // ToDo: this could be moved to an EventListener to extcode/cart-pdf
        if ($numberExists && ExtensionManagementUtility::isLoaded('cart_pdf')) {
            $buttons['action-create-' . $type . '-pdf'] = [
                'link' => $this->uriBuilder->reset()->setRequest($event->getRequest())
                    ->uriFor(
                        'create',
                        ['orderItem' => $event->getOrderItem(), 'pdfType' => $type],
                        'Backend\Order\Document'
                    ),
                'title' => 'tx_cart.controller.order.action.generate' . ucfirst($type) . 'Document',
                'icon' => 'actions-file-pdf',
                'group' => $groupId,
                'showLabel' => true,
            ];
        }

        if ($documentExists) {
            $buttons['action-download-' . $type . '-pdf'] = [
                'link' => $this->uriBuilder->reset()->setRequest($event->getRequest())
                    ->uriFor(
                        'download',
                        ['orderItem' => $event->getOrderItem(), 'pdfType' => $type],
                        'Backend\Order\Document'
                    ),
                'title' => 'tx_cart.controller.order.action.download' . ucfirst($type) . 'Document',
                'icon' => 'actions-file-t3d-download',
                'group' => $groupId,
                'showLabel' => true,
            ];
        }

        return $buttons;
    }
}
