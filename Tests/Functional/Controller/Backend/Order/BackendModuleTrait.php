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
     * Submits a form of the HTML with its hidden fields and the given values, like a click on its submit button does.
     *
     * @param array<string, string> $values e.g. ['status' => 'paid']
     */
    protected static function submitForm(string $html, string $formName, string $routeIdentifier, array $values): ResponseInterface
    {
        preg_match('/<form action="([^"]*)" method="post" name="' . preg_quote($formName, '/') . '">(.*?)<\/form>/s', $html, $formMatches);
        self::assertArrayHasKey(2, $formMatches, 'No form "' . $formName . '" has been rendered.');

        $queryParameters = self::getQueryParametersOfHref($formMatches[1]);
        unset($queryParameters['token']);

        preg_match_all('/<input type="hidden" name="([^"]*)" value="([^"]*)"/', $formMatches[2], $inputMatches, PREG_SET_ORDER);
        $fields = [];
        foreach ($inputMatches as [, $name, $value]) {
            $fields[] = rawurlencode(html_entity_decode($name)) . '=' . rawurlencode(html_entity_decode($value));
        }
        foreach ($values as $name => $value) {
            $fields[] = rawurlencode($name) . '=' . rawurlencode($value);
        }
        parse_str(implode('&', $fields), $parsedBody);

        return self::dispatchRequestWithQueryParameters($queryParameters, $routeIdentifier, $parsedBody);
    }

    /**
     * @return array<array-key, mixed>
     */
    protected static function getQueryParametersOfHref(string $href): array
    {
        parse_str((string) parse_url(html_entity_decode($href), PHP_URL_QUERY), $queryParameters);

        return $queryParameters;
    }

    /**
     * @param array<array-key, mixed> $queryParameters
     * @param null|array<array-key, mixed> $parsedBody the body of a POST request, a GET request without
     */
    protected static function dispatchRequestWithQueryParameters(array $queryParameters, string $routeIdentifier = 'cart_cart_orders', ?array $parsedBody = null): ResponseInterface
    {
        $request = self::buildRequest($queryParameters, $routeIdentifier);
        if ($parsedBody !== null) {
            $request = $request->withMethod('POST')->withParsedBody($parsedBody);
        }

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
     * @param null|string $formName only the select of this form, e.g. if several forms have a select with this name
     *
     * @return array<string, string> the labels of the options by value in the order they are rendered
     */
    protected static function getOptionsOfSelect(string $html, string $name, ?string $formName = null): array
    {
        preg_match_all(
            '/<option value="([^"]*)"[^>]*>([^<]*)<\/option>/',
            self::getContentOfSelect($html, $name, $formName),
            $matches,
            PREG_SET_ORDER
        );

        $options = [];
        foreach ($matches as [, $value, $label]) {
            $options[html_entity_decode($value)] = html_entity_decode($label);
        }

        return $options;
    }

    /**
     * @param null|string $formName only the select of this form, e.g. if several forms have a select with this name
     */
    protected static function getSelectedOptionOfSelect(string $html, string $name, ?string $formName = null): ?string
    {
        preg_match('/<option value="([^"]*)" selected="selected">/', self::getContentOfSelect($html, $name, $formName), $matches);

        return isset($matches[1]) ? html_entity_decode($matches[1]) : null;
    }

    protected static function getContentOfSelect(string $html, string $name, ?string $formName = null): string
    {
        if ($formName !== null) {
            $html = self::getContentOfForm($html, $formName);
        }

        preg_match('/<select [^>]*name="' . preg_quote($name, '/') . '"[^>]*>(.*?)<\/select>/s', $html, $matches);
        self::assertArrayHasKey(1, $matches, 'No select "' . $name . '" has been rendered.');

        return $matches[1];
    }

    protected static function getContentOfForm(string $html, string $formName): string
    {
        preg_match('/<form [^>]*name="' . preg_quote($formName, '/') . '"[^>]*>(.*?)<\/form>/s', $html, $matches);
        self::assertArrayHasKey(1, $matches, 'No form "' . $formName . '" has been rendered.');

        return $matches[1];
    }
}
