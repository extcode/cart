<?php

namespace Extcode\Cart\Service;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Cart\Cart;
use TYPO3\CMS\Core\SingletonInterface;

class SessionHandler implements SingletonInterface
{
    protected $prefixKey = 'cart_';

    /**
     * Returns the object stored in the user´s PHP session
     *
     * @param string $key
     *
     * @return Cart
     */
    public function restore($key): ?Cart
    {
        $sessionData = $GLOBALS['TSFE']->fe_user->getKey('ses', $this->prefixKey . $key);

        return ($sessionData === null) ? null : unserialize($sessionData);
    }

    /**
     * Writes an object into the PHP session
     *
     * @param Cart $cart Cart
     * @param string $key Session Key
     *
     * @return SessionHandler $this
     */
    public function write(Cart $cart, $key)
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
