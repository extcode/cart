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
use Extcode\Cart\Configuration\Loader\SiteSets\TaxClassLoader;
use Extcode\Cart\Domain\Model\Cart\TaxClass;
use Extcode\Cart\Domain\Model\Cart\TaxClassFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\IgnoreDeprecations;
use PHPUnit\Framework\Attributes\Test;
use Psr\Log\NullLogger;
use TYPO3\CMS\Core\Configuration\SiteWriter;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * Reads the tax classes of a site, which depends on the site set extcode/cart-taxclasses.
 */
#[CoversClass(TaxClassLoader::class)]
final class TaxClassLoaderTest extends FunctionalTestCase
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

    #[Test]
    public function readsTheTaxClassesOfTheSiteSet(): void
    {
        self::createSite(['extcode/cart-taxclasses']);

        self::assertSame([1 => 'normal', 2 => 'reduced', 3 => 'free'], self::getTaxClassTitles());
    }

    #[IgnoreDeprecations]
    #[Test]
    public function readsTheDeprecatedSettingOfAnOwnSiteSet(): void
    {
        self::createSite([], [
            'extcode' => [
                'taxclasses' => [
                    1 => ['value' => 20, 'calc' => 0.20, 'name' => 'own'],
                ],
            ],
        ]);

        $this->expectUserDeprecationMessage(
            'The site setting "extcode.taxclasses" is deprecated since cart 12.4 and will stop working in cart 13.0.'
            . ' Use "extcode.cart.taxClasses" instead.'
        );

        self::assertSame([1 => 'own'], self::getTaxClassTitles());
    }

    #[Test]
    public function aSiteWithoutTaxClassesHasNoTaxClasses(): void
    {
        self::createSite([]);

        self::assertSame([], self::getTaxClassTitles());
    }

    /**
     * @return array<array-key, string>
     */
    private static function getTaxClassTitles(): array
    {
        $taxClasses = (new TaxClassLoader(new TaxClassFactory(new NullLogger())))->getTaxClasses();

        return array_map(static fn (TaxClass $taxClass): string => $taxClass->getTitle(), $taxClasses);
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
