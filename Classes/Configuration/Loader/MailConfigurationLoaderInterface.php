<?php

declare(strict_types=1);

namespace Extcode\Cart\Configuration\Loader;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Mail\BuyerMailConfiguration;
use Extcode\Cart\Mail\InvalidMailConfigurationException;
use Extcode\Cart\Mail\SellerMailConfiguration;

/**
 * Loads the configuration of the emails to the buyer and to the seller.
 */
interface MailConfigurationLoaderInterface
{
    /**
     * @param string $template e.g. "Mail/Open/Buyer"
     *
     * @throws InvalidMailConfigurationException with an invalid email address
     */
    public function getBuyerMailConfiguration(string $template): BuyerMailConfiguration;

    /**
     * @param string $template e.g. "Mail/Open/Seller"
     *
     * @throws InvalidMailConfigurationException without "toAddress" or with an invalid email address
     */
    public function getSellerMailConfiguration(string $template): SellerMailConfiguration;
}
