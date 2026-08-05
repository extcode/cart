<?php

declare(strict_types=1);

namespace Extcode\Cart\Event\Document;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Order\Item;

final readonly class GenerateDocumentEvent
{
    public function __construct(public Item $orderItem, public string $pdfType)
    {
    }
}
