<?php

namespace Extcode\Cart\Domain\Finisher;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

abstract class AbstractFinisher implements FinisherInterface
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
     * @var FinisherContext
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
     * @param FinisherContext $finisherContext
     *
     * @return string|null
     */
    final public function execute(FinisherContext $finisherContext)
    {
        $this->finisherIdentifier = (new \ReflectionClass($this))->getShortName();
        $this->shortFinisherIdentifier = preg_replace('/Finisher$/', '', $this->finisherIdentifier);
        $this->finisherContext = $finisherContext;

        $this->settings = $this->finisherContext->getSettings();

        return $this->executeInternal();
    }

    /**
     * This method is called in the concrete finisher whenever self::execute() is called.
     *
     * Override and fill with your own implementation!
     *
     * @return string|null
     */
    abstract protected function executeInternal();
}
