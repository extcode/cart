<?php

declare(strict_types=1);

namespace Extcode\Cart\Configuration\Loader;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Order\Item;
use Extcode\Cart\Mail\BuyerMailConfiguration;
use Extcode\Cart\Mail\InvalidMailConfigurationException;
use Extcode\Cart\Mail\SellerMailConfiguration;

/**
 * Loads the configuration of the emails to the buyer and to the seller of an
 * order, e.g. from the site of the order.
 */
interface MailConfigurationLoaderInterface
{
    /**
     * @param string $template e.g. "Mail/Open/Buyer"
     *
     * @throws InvalidMailConfigurationException with an invalid email address or without configuration, e.g. without site
     */
    public function getBuyerMailConfiguration(string $template, Item $orderItem): BuyerMailConfiguration;

    /**
     * @param string $template e.g. "Mail/Open/Seller"
     *
     * @throws InvalidMailConfigurationException without "toAddress", with an invalid email address or without configuration
     */
    public function getSellerMailConfiguration(string $template, Item $orderItem): SellerMailConfiguration;
}
