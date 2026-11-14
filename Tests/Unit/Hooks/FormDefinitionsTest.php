<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Unit\Hooks;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Hooks\FormDefinitions;
use LogicException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Form\Domain\DTO\FormMetadata;
use TYPO3\CMS\Form\Domain\DTO\SearchCriteria;
use TYPO3\CMS\Form\Mvc\Configuration\ConfigurationManagerInterface as ExtFormConfigurationManagerInterface;
use TYPO3\CMS\Form\Mvc\Persistence\Exception\PersistenceManagerException;
use TYPO3\CMS\Form\Mvc\Persistence\FormPersistenceManagerInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(FormDefinitions::class)]
final class FormDefinitionsTest extends UnitTestCase
{
    private FormPersistenceManagerInterface&MockObject $formPersistenceManager;

    private ExtFormConfigurationManagerInterface&MockObject $extFormConfigurationManager;

    private FormDefinitions $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->formPersistenceManager = $this->createMock(FormPersistenceManagerInterface::class);
        $this->extFormConfigurationManager = $this->createMock(ExtFormConfigurationManagerInterface::class);

        $this->subject = new FormDefinitions(
            $this->formPersistenceManager,
            self::createStub(ConfigurationManagerInterface::class),
            $this->extFormConfigurationManager,
        );
    }

    #[Test]
    public function doesNothingForOtherFields(): void
    {
        $this->formPersistenceManager->expects(self::never())->method('listForms');

        $config = [
            'field' => 'title',
            'items' => [],
        ];

        $this->subject->getItems($config);

        self::assertSame([], $config['items']);
    }

    #[Test]
    public function addsTheValidFormsOfThePrototypeCart(): void
    {
        $this->extFormConfigurationManager
            ->method('getYamlConfiguration')
            ->willReturn(['formManager' => []])
        ;
        $this->formPersistenceManager
            ->expects(self::once())
            ->method('listForms')
            ->with(['formManager' => []], self::isInstanceOf(SearchCriteria::class))
            ->willReturn([
                self::createFormMetadata('Order Form', 'cart', '1:/forms/order.form.yaml'),
                self::createFormMetadata('Contact Form', 'standard', '1:/forms/contact.form.yaml'),
                self::createFormMetadata('Broken Form', 'cart', '1:/forms/broken.form.yaml', invalid: true),
            ])
        ;
        $this->formPersistenceManager->expects(self::never())->method('load');

        $config = [
            'field' => 'form_definition',
            'config' => [
                'itemsProcFuncConfig' => [],
            ],
            'items' => [],
        ];

        $this->subject->getItems($config);

        self::assertSame(
            [
                [
                    'label' => 'Order Form',
                    'value' => '1:/forms/order.form.yaml',
                ],
            ],
            $config['items']
        );
    }

    #[Test]
    public function addsTheFormsOfTheConfiguredPrototype(): void
    {
        $this->extFormConfigurationManager
            ->method('getYamlConfiguration')
            ->willReturn(['formManager' => []])
        ;
        $this->formPersistenceManager
            ->method('listForms')
            ->willReturn([
                self::createFormMetadata('Order Form', 'cart', '1:/forms/order.form.yaml'),
                self::createFormMetadata('Event Form', 'cart-events', '1:/forms/event.form.yaml'),
            ])
        ;

        $config = [
            'field' => 'form_definition',
            'config' => [
                'itemsProcFuncConfig' => [
                    'prototypeName' => 'cart-events',
                ],
            ],
            'items' => [],
        ];

        $this->subject->getItems($config);

        self::assertSame(
            [
                [
                    'label' => 'Event Form',
                    'value' => '1:/forms/event.form.yaml',
                ],
            ],
            $config['items']
        );
    }

    /**
     * The database storage lists every form with the prototype "standard",
     * the prototype is only part of the complete form definition.
     */
    #[Test]
    public function loadsTheFormsOfTheDatabaseStorageToCheckThePrototype(): void
    {
        $this->extFormConfigurationManager
            ->method('getYamlConfiguration')
            ->willReturn(['formManager' => []])
        ;
        $this->formPersistenceManager
            ->method('listForms')
            ->willReturn([
                self::createFormMetadata('Order Form', 'standard', '12', storageType: 'database'),
                self::createFormMetadata('Contact Form', 'standard', '13', storageType: 'database'),
                self::createFormMetadata('Broken Form', 'standard', '14', storageType: 'database'),
            ])
        ;
        $this->formPersistenceManager
            ->expects(self::exactly(3))
            ->method('load')
            ->willReturnCallback(static fn (string $persistenceIdentifier): array => match ($persistenceIdentifier) {
                '12' => ['prototypeName' => 'cart'],
                '13' => ['prototypeName' => 'standard'],
                default => throw new PersistenceManagerException('The form definition is invalid.', 1767199423),
            })
        ;

        $config = [
            'field' => 'form_definition',
            'config' => [
                'itemsProcFuncConfig' => [],
            ],
            'items' => [],
        ];

        $this->subject->getItems($config);

        self::assertSame(
            [
                [
                    'label' => 'Order Form',
                    'value' => '12',
                ],
            ],
            $config['items']
        );
    }

    #[Test]
    public function throwsExceptionIfTheFormManagerConfigurationIsMissing(): void
    {
        $this->extFormConfigurationManager
            ->method('getYamlConfiguration')
            ->willReturn([])
        ;

        $this->expectExceptionObject(new LogicException('Configuration could not be loaded', 1723717461));

        $config = [
            'field' => 'form_definition',
            'config' => [
                'itemsProcFuncConfig' => [],
            ],
            'items' => [],
        ];

        $this->subject->getItems($config);
    }

    private static function createFormMetadata(
        string $name,
        string $prototypeName,
        string $persistenceIdentifier,
        bool $invalid = false,
        string $storageType = 'filemount',
    ): FormMetadata {
        return new FormMetadata(
            identifier: mb_strtolower(str_replace(' ', '-', $name)),
            type: 'Form',
            name: $name,
            prototypeName: $prototypeName,
            persistenceIdentifier: $persistenceIdentifier,
            invalid: $invalid,
            storageType: $storageType,
        );
    }
}
