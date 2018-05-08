<?php

namespace Extcode\Cart\Service;

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
use TYPO3\CMS\Core\SingletonInterface;

/**
 * SessionHandler
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class SessionHandler implements SingletonInterface
{
    protected $prefixKey = 'cart_';

    /**
     * Returns the object stored in the user´s PHP session
     *
     * @param string $key
     *
     * @return \Extcode\Cart\Domain\Model\Cart\Cart
     */
    public function restore($key)
    {
        $sessionData = $GLOBALS['TSFE']->fe_user->getKey('ses', $this->prefixKey . $key);
        return unserialize($sessionData);
    }

    /**
     * Writes an object into the PHP session
     *
     * @param \Extcode\Cart\Domain\Model\Cart\Cart $cart Cart
     * @param string $key Session Key
     *
     * @return SessionHandler $this
     */
    public function write(\Extcode\Cart\Domain\Model\Cart\Cart $cart, $key)
    {
        $sessionData = serialize($cart);
        $GLOBALS['TSFE']->fe_user->setKey('ses', $this->prefixKey . $key, $sessionData);
        $GLOBALS['TSFE']->fe_user->storeSessionData();
        return $this;
    }

    /**
     * Cleans up the session: removes the stored object from the PHP session
     *
     * @param string $key
     *
     * @return SessionHandler $this
     */
    public function clear($key)
    {
        $GLOBALS['TSFE']->fe_user->setKey('ses', $this->prefixKey . $key, null);
        $GLOBALS['TSFE']->fe_user->storeSessionData();
        return $this;
    }

    /**
     * Sets own prefix key for session
     *
     * @param string $prefixKey
     */
    public function setPrefixKey($prefixKey)
    {
        $this->prefixKey = $prefixKey;
    }
}
