<?php
declare(strict_types=1);
namespace Extcode\Cart\Configuration;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

return function (ContainerConfigurator $configurator) {
    if (ExtensionManagementUtility::isLoaded('dashboard')) {
        $configurator->import('Backend/Provider/PaymentPaidShippingOpenProvider.php');
        $configurator->import('Backend/Widgets/PaymentPaidShippingOpenWidget.php');

        $configurator->import('Backend/Provider/OrdersPerDayProvider.php');
        $configurator->import('Backend/Widgets/OrdersPerDayWidget.php');

        $configurator->import('Backend/Provider/TurnoverPerDayProvider.php');
        $configurator->import('Backend/Widgets/TurnoverPerDayWidget.php');
    }
};
