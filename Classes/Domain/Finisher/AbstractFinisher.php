<?php

namespace Extcode\Cart\Domain\Finisher;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * Abstract Finisher
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
abstract class AbstractFinisher
{
    /**
     * Object Manager
     *
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
     */
    protected $objectManager;

    /**
     * Persistence Manager
     *
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     */
    protected $persistenceManager;

    /**
     * @var string
     */
    protected $finisherIdentifier = '';

    /**
     * @var string
     */
    protected $shortFinisherIdentifier = '';

    /**
     * @var \Extcode\Cart\Domain\Finisher\FinisherContext
     */
    protected $finisherContext;

    /**
     * @var array
     */
    protected $settings;

    /**
     * @param \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager
     * @internal
     */
    public function injectObjectManager(
        \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager $persistenceManager
     */
    public function injectPersistenceManager(
        \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager $persistenceManager
    ) {
        $this->persistenceManager = $persistenceManager;
    }

    /**
     * Executes the finisher
     *
     * @param FinisherContext $finisherContext The Finisher context that contains the current Form Runtime and Response
     * @api
     */
    final public function execute(FinisherContext $finisherContext)
    {
        $this->finisherIdentifier = (new \ReflectionClass($this))->getShortName();
        $this->shortFinisherIdentifier = preg_replace('/Finisher$/', '', $this->finisherIdentifier);
        $this->finisherContext = $finisherContext;

        $this->settings = $this->finisherContext->getSettings();

        $this->executeInternal();
    }

    /**
     * This method is called in the concrete finisher whenever self::execute() is called.
     *
     * Override and fill with your own implementation!
     *
     * @api
     */
    abstract protected function executeInternal();
}
