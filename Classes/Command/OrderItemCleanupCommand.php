<?php

declare(strict_types=1);

namespace Extcode\Cart\Command;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use DateTimeImmutable;
use Extcode\Cart\Service\OrderItemCleanupService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Core\Bootstrap;

class OrderItemCleanupCommand extends Command
{
    public function __construct(
        private readonly OrderItemCleanupService $orderItemCleanupService,
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Will remove all old orders');
        $this->addArgument(
            'cutOffDate',
            InputArgument::REQUIRED,
            'cutOffDate'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $cutOffDate = $input->getArgument('cutOffDate');

        if (is_string($cutOffDate) === false || $this->isCutOffDateValid($cutOffDate) === false) {
            $output->writeln('The cutOffDate argument must follow the pattern YYYY-MM-DD.');

            return Command::FAILURE;
        }

        Bootstrap::initializeBackendAuthentication();

        $this->orderItemCleanupService->run(
            new DateTimeImmutable(
                $cutOffDate
            )
        );

        return Command::SUCCESS;
    }

    private function isCutOffDateValid(string $cutOffDate): bool
    {
        $pattern = '/^\d{4}-\d{2}-\d{2}$/';

        return preg_match($pattern, $cutOffDate) === 1;
    }
}
