<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Functional\Configuration\Loader\SiteSets;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Codappix\Typo3PhpDatasets\TestingFramework;
use Extcode\Cart\Configuration\Loader\MailConfigurationLoaderInterface;
use Extcode\Cart\Configuration\Loader\SiteSets\MailConfigurationLoader;
use Extcode\Cart\Domain\Model\Order\Item;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Configuration\SiteWriter;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * Reads the settings of the site of the order, which depends on the site set
 * extcode/cart-mail.
 */
#[CoversClass(MailConfigurationLoader::class)]
final class MailConfigurationLoaderTest extends FunctionalTestCase
{
    use TestingFramework;

    protected array $testExtensionsToLoad = [
        'extcode/cart',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->importPHPDataSet(__DIR__ . '/../../../../Fixtures/BaseDatabase.php');
    }

    #[Test]
    public function theSiteSetIsTheDefaultConfiguration(): void
    {
        self::assertInstanceOf(MailConfigurationLoader::class, $this->get(MailConfigurationLoaderInterface::class));
    }

    #[Test]
    public function readsTheSettingsOfTheSite(): void
    {
        // without request, like on the command line
        unset($GLOBALS['TYPO3_REQUEST']);
        self::createSite([
            'extcode' => [
                'cart' => [
                    'mail' => [
                        'buyer' => [
                            'fromName' => 'Shop',
                            'fromAddress' => 'shop@example.com',
                            'bccAddress' => ['archive@example.com'],
                        ],
                        'seller' => [
                            'fromAddress' => 'shop@example.com',
                            'toAddress' => ['orders@example.com', 'archive@example.com'],
                        ],
                    ],
                ],
            ],
        ]);

        $buyerMailConfiguration = self::createSubject()->getBuyerMailConfiguration('Mail/Open/Buyer', self::createStubForOrderItem());
        $sellerMailConfiguration = self::createSubject()->getSellerMailConfiguration('Mail/Open/Seller', self::createStubForOrderItem());

        self::assertSame('Shop', $buyerMailConfiguration->fromName);
        self::assertSame('shop@example.com', $buyerMailConfiguration->fromAddress);
        self::assertSame(['archive@example.com'], $buyerMailConfiguration->bcc);
        self::assertSame([], $buyerMailConfiguration->cc, 'The default of the site set is used.');
        self::assertSame(['orders@example.com', 'archive@example.com'], $sellerMailConfiguration->to);
    }

    #[Test]
    public function aStringInsteadOfAListIsRevertedToTheDefault(): void
    {
        self::createSite([
            'extcode' => [
                'cart' => [
                    'mail' => [
                        'buyer' => [
                            'fromAddress' => 'shop@example.com',
                            'bccAddress' => 'archive@example.com',
                        ],
                    ],
                ],
            ],
        ]);

        $buyerMailConfiguration = self::createSubject()->getBuyerMailConfiguration('Mail/Open/Buyer', self::createStubForOrderItem());

        self::assertSame([], $buyerMailConfiguration->bcc);
    }

    /**
     * @param array<string, mixed> $settings
     */
    private static function createSite(array $settings): void
    {
        $siteWriter = GeneralUtility::makeInstance(SiteWriter::class);
        $siteWriter->createNewBasicSite('main', 1, 'https://example.com/', ['extcode/cart-mail']);
        $siteWriter->writeSettings('main', $settings);
        // reads the sites without cache, as the site of the previous test is cached
        GeneralUtility::makeInstance(SiteFinder::class)->getAllSites(false);
    }

    private static function createSubject(): MailConfigurationLoader
    {
        return new MailConfigurationLoader(GeneralUtility::makeInstance(SiteFinder::class));
    }

    /**
     * The order is stored on the root page 1 of the site "main".
     */
    private static function createStubForOrderItem(): Item
    {
        $orderItem = self::createStub(Item::class);
        $orderItem->method('getPid')->willReturn(1);

        return $orderItem;
    }
}
