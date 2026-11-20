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
use Extcode\Cart\Configuration\Loader\SiteSets\AbstractConfigurationLoader;
use Extcode\Cart\Configuration\Loader\SiteSets\PaymentMethodsLoader;
use Extcode\Cart\Configuration\Loader\SiteSets\ShippingMethodsLoader;
use Extcode\Cart\Domain\Model\Cart\Cart;
use Extcode\Cart\Domain\Model\Cart\ServiceFactory;
use Extcode\Cart\Domain\Model\Cart\ServiceInterface;
use Extcode\Cart\Domain\Model\Cart\TaxClass;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\IgnoreDeprecations;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Configuration\SiteWriter;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * Reads the payment and shipping methods of a site, which depends on the site
 * sets extcode/cart-payments and extcode/cart-shippings.
 */
#[CoversClass(AbstractConfigurationLoader::class)]
#[CoversClass(PaymentMethodsLoader::class)]
#[CoversClass(ShippingMethodsLoader::class)]
final class ServiceMethodsLoaderTest extends FunctionalTestCase
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

    protected function tearDown(): void
    {
        unset($GLOBALS['TYPO3_REQUEST']);
        parent::tearDown();
    }

    #[DataProvider('countries')]
    #[Test]
    public function readsThePaymentMethodsOfTheSiteSet(string $country): void
    {
        self::createSite(['extcode/cart-payments']);

        $paymentMethods = (new PaymentMethodsLoader(new ServiceFactory()))->getPaymentMethods(self::createCart($country));

        self::assertSame([1 => 'Vorkasse'], self::getNames($paymentMethods));
        self::assertSame(0.0, $paymentMethods[1]->getGross());
    }

    #[DataProvider('countries')]
    #[Test]
    public function readsTheShippingMethodsOfTheSiteSet(string $country): void
    {
        self::createSite(['extcode/cart-shippings']);

        $shippingMethods = (new ShippingMethodsLoader(new ServiceFactory()))->getShippingMethods(self::createCart($country));

        self::assertSame([1 => 'Standard'], self::getNames($shippingMethods));
        self::assertSame(7.95, $shippingMethods[1]->getGross());
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function countries(): iterable
    {
        yield 'de' => ['de'];
        yield 'at, reference to de' => ['at'];
        yield 'ch, reference to de' => ['ch'];
    }

    #[Test]
    public function theShippingMethodsDependOnTheCountryOfTheShippingAddress(): void
    {
        $this->createSite([], self::createShippingSettingsForDeAndAt());
        $cart = self::createCart('de');
        $cart->setShippingCountry('at');

        self::assertSame(
            [1 => 'Shipping AT'],
            self::getNames((new ShippingMethodsLoader(new ServiceFactory()))->getShippingMethods($cart))
        );
    }

    #[Test]
    public function withoutShippingCountryTheShippingMethodsDependOnTheCountryOfTheBillingAddress(): void
    {
        $this->createSite([], self::createShippingSettingsForDeAndAt());
        $cart = self::createCart('at');
        $cart->setShippingCountry('');

        self::assertSame(
            [1 => 'Shipping AT'],
            self::getNames((new ShippingMethodsLoader(new ServiceFactory()))->getShippingMethods($cart))
        );
    }

    #[IgnoreDeprecations]
    #[Test]
    public function readsTheDeprecatedSettingsOfAnOwnSiteSet(): void
    {
        $this->createSite([], [
            'extcode' => [
                'cart-payments' => self::createServiceSettings('Own payment'),
                'cart-shippings' => self::createServiceSettings('Own shipping'),
            ],
        ]);

        $this->expectUserDeprecationMessage(
            'The site setting "extcode.cart-payments" is deprecated since cart 12.4 and will stop working in cart 13.0.'
            . ' Use "extcode.cart.payments" instead.'
        );
        $this->expectUserDeprecationMessage(
            'The site setting "extcode.cart-shippings" is deprecated since cart 12.4 and will stop working in cart 13.0.'
            . ' Use "extcode.cart.shippings" instead.'
        );

        self::assertSame(
            [1 => 'Own payment'],
            self::getNames((new PaymentMethodsLoader(new ServiceFactory()))->getPaymentMethods(self::createCart('de')))
        );
        self::assertSame(
            [1 => 'Own shipping'],
            self::getNames((new ShippingMethodsLoader(new ServiceFactory()))->getShippingMethods(self::createCart('de')))
        );
    }

    #[Test]
    public function theDeprecatedSettingsAreIgnoredWithTheNewSettings(): void
    {
        $this->createSite(['extcode/cart-payments'], [
            'extcode' => [
                'cart-payments' => self::createServiceSettings('Own payment'),
            ],
        ]);

        self::assertSame(
            [1 => 'Vorkasse'],
            self::getNames((new PaymentMethodsLoader(new ServiceFactory()))->getPaymentMethods(self::createCart('de')))
        );
    }

    /**
     * @return array<string, mixed> the site settings with different shipping methods for de and at
     */
    private static function createShippingSettingsForDeAndAt(): array
    {
        return [
            'extcode' => [
                'cart' => [
                    'shippings' => [
                        'countries' => [
                            'de' => self::createCountrySettings('Shipping DE'),
                            'at' => self::createCountrySettings('Shipping AT'),
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed> the settings of one service for de
     */
    private static function createServiceSettings(string $title): array
    {
        return [
            'countries' => [
                'de' => self::createCountrySettings($title),
            ],
        ];
    }

    /**
     * @return array<string, mixed> the settings of one country with one service
     */
    private static function createCountrySettings(string $title): array
    {
        return [
            'preset' => 1,
            'options' => [
                1 => ['title' => $title, 'extra' => 0.0, 'taxClassId' => 1, 'status' => 'open'],
            ],
        ];
    }

    private static function createCart(string $country): Cart
    {
        $cart = new Cart([1 => new TaxClass(1, '19', 0.19, 'normal')]);
        $cart->setBillingCountry($country);
        $cart->setShippingCountry($country);

        return $cart;
    }

    /**
     * @param array<array-key, ServiceInterface> $services
     *
     * @return array<array-key, string>
     */
    private static function getNames(array $services): array
    {
        return array_map(static fn (ServiceInterface $service): string => $service->getName(), $services);
    }

    /**
     * @param list<string> $dependencies the site sets of the site
     * @param array<string, mixed> $settings the settings of the site
     */
    private static function createSite(array $dependencies, array $settings = []): void
    {
        $siteWriter = GeneralUtility::makeInstance(SiteWriter::class);
        $siteWriter->createNewBasicSite('main', 1, 'https://example.com/', $dependencies);
        $siteWriter->writeSettings('main', $settings);

        // reads the sites without cache, as the site of the previous test is cached
        $site = GeneralUtility::makeInstance(SiteFinder::class)->getAllSites(false)['main'];
        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest('https://example.com/'))->withAttribute('site', $site);
    }
}
