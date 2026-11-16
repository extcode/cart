<?php

declare(strict_types=1);

namespace Extcode\Cart\Event\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Psr\EventDispatcher\StoppableEventInterface;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;

/**
 * Dispatched by the export action of the backend order module.
 *
 * An EventListener checks whether it is responsible for the requested format
 * (see getFormat()) and sets the response with the exported orders. The first
 * response set stops the propagation, so following EventListeners are not
 * called anymore.
 */
final class ExportEvent implements StoppableEventInterface
{
    private ?ResponseInterface $response = null;

    /**
     * @param array<array-key, mixed> $settings
     * @param array<string, string> $searchArguments
     */
    public function __construct(
        private readonly RequestInterface $request,
        private readonly array $settings,
        private readonly array $searchArguments,
    ) {
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    public function getFormat(): string
    {
        return $this->request->getFormat();
    }

    /**
     * @return array<array-key, mixed>
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    /**
     * @return array<string, string> the filters of the order list, see ItemRepository::findAll()
     */
    public function getSearchArguments(): array
    {
        return $this->searchArguments;
    }

    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }

    public function setResponse(ResponseInterface $response): void
    {
        $this->response = $response;
    }

    public function isPropagationStopped(): bool
    {
        return $this->response instanceof ResponseInterface;
    }
}
