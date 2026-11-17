<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Acceptance\Support;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Codeception\Actor;
use Extcode\Cart\Tests\Acceptance\Support\_generated\TesterActions;

/**
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause()
 */
class Tester extends Actor
{
    use TesterActions;

    protected int $retryNum = 2;

    public function loginAsAdmin(): void
    {
        $this->amOnUrl('http://127.0.0.1:8080/typo3/');
        $this->waitForElementVisible('#t3-username', 10);
        $this->fillField('#t3-username', 'admin');
        $this->fillField('#t3-password', 'password');
        $this->click('#t3-login-submit');
        $this->waitForElement('.t3js-scaffold-toolbar', 10);
    }
}
