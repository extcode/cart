<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Functional\Controller\Backend\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Backend\Middleware\BackendModuleValidator;
use TYPO3\CMS\Backend\Routing\Router;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\NormalizedParams;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Core\Bootstrap;

/**
 * Dispatches requests to the orders backend module of cart and reads its HTML.
 */
trait BackendModuleTrait
{
    /**
     * @param array<array-key, mixed> $queryParameters
     */
    protected static function dispatchRequestWithQueryParameters(array $queryParameters, string $routeIdentifier = 'cart_cart_orders'): ResponseInterface
    {
        $request = self::buildRequest($queryParameters, $routeIdentifier);

        // the middleware adds the module data to the request and saves the search arguments of the filter form
        return GeneralUtility::makeInstance(BackendModuleValidator::class)->process(
            $request,
            new class implements RequestHandlerInterface {
                public function handle(ServerRequestInterface $request): ResponseInterface
                {
                    $GLOBALS['TYPO3_REQUEST'] = $request;

                    return GeneralUtility::makeInstance(Bootstrap::class)->handleBackendRequest($request);
                }
            }
        );
    }

    /**
     * @param array<array-key, mixed> $queryParameters
     */
    protected static function buildRequest(array $queryParameters, string $routeIdentifier): ServerRequestInterface
    {
        $route = GeneralUtility::makeInstance(Router::class)->getRoute($routeIdentifier);
        self::assertNotNull($route, 'The route ' . $routeIdentifier . ' of the backend module is not registered.');

        $queryParameters['route'] = $route->getPath();

        $request = (new ServerRequest(
            'https://localhost/typo3' . $route->getPath() . '?' . http_build_query($queryParameters),
            'GET'
        ))
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_BE)
            ->withAttribute('route', $route)
            ->withAttribute('module', $route->getOption('module'))
            ->withQueryParams($queryParameters)
        ;

        return $request->withAttribute('normalizedParams', NormalizedParams::createFromRequest($request));
    }

    /**
     * @return array<string, string> the labels of the options by value in the order they are rendered
     */
    protected static function getOptionsOfSelect(string $html, string $name): array
    {
        preg_match_all(
            '/<option value="([^"]*)"[^>]*>([^<]*)<\/option>/',
            self::getContentOfSelect($html, $name),
            $matches,
            PREG_SET_ORDER
        );

        $options = [];
        foreach ($matches as [, $value, $label]) {
            $options[html_entity_decode($value)] = html_entity_decode($label);
        }

        return $options;
    }

    protected static function getSelectedOptionOfSelect(string $html, string $name): ?string
    {
        preg_match('/<option value="([^"]*)" selected="selected">/', self::getContentOfSelect($html, $name), $matches);

        return isset($matches[1]) ? html_entity_decode($matches[1]) : null;
    }

    protected static function getContentOfSelect(string $html, string $name): string
    {
        preg_match('/<select [^>]*name="' . preg_quote($name, '/') . '"[^>]*>(.*?)<\/select>/s', $html, $matches);
        self::assertArrayHasKey(1, $matches, 'No select "' . $name . '" has been rendered.');

        return $matches[1];
    }
}
