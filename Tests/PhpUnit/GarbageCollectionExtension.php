<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\PhpUnit;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use PHPUnit\Event\Test\Finished;
use PHPUnit\Event\Test\FinishedSubscriber;
use PHPUnit\Event\Test\Prepared;
use PHPUnit\Event\Test\PreparedSubscriber;
use PHPUnit\Runner\Extension\Extension;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;

/**
 * Collects garbage cycles only between tests, never in the middle of a test.
 *
 * Objects of earlier test instances, e.g. the Extbase ReflectionService, are
 * part of reference cycles and are destroyed by the garbage collector. The
 * destructor of the ReflectionService writes the class schemata to the cache,
 * which calls serialize(). If the garbage collector runs while another
 * serialize() is in progress, the nested serialize() corrupts the state of the
 * outer one: on PHP 8.2 this crashes with a segmentation fault or writes wrong
 * references into the cache entry.
 *
 * The automatic garbage collection is disabled, the cycles are collected
 * explicitly after the set up of each test and after each test.
 */
final class GarbageCollectionExtension implements Extension
{
    public function bootstrap(Configuration $configuration, Facade $facade, ParameterCollection $parameters): void
    {
        gc_disable();

        $facade->registerSubscribers(
            new class implements PreparedSubscriber {
                public function notify(Prepared $event): void
                {
                    gc_collect_cycles();
                }
            },
            new class implements FinishedSubscriber {
                public function notify(Finished $event): void
                {
                    gc_collect_cycles();
                }
            },
        );
    }
}
