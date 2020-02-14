<?php
declare(strict_types = 1);

namespace Extcode\Cart\Service;

use Extcode\Cart\Domain\Model\Cart\TaxClass;

interface TaxClassServiceInterface
{
    /**
     * @param string $countryCode
     *
     * @return TaxClass[]
     */
    public function getTaxClasses(string $countryCode): array;
}
