<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Log;

use Extcode\Cart\Domain\Log\Model\LogInterface;
use Extcode\Cart\Domain\Log\Repository\LogRepository;
use Throwable;
use TYPO3\CMS\Core\Log\LogRecord;
use TYPO3\CMS\Core\Log\Writer\AbstractWriter;
use TYPO3\CMS\Core\Log\Writer\WriterInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class DatabaseWriter extends AbstractWriter
{
    public function writeLog(LogRecord $record): WriterInterface
    {
        $recordData = $record->getData();

        $log = $recordData['log'] ?? null;
        if (($log instanceof LogInterface) === false) {
            return $this;
        }
        unset($recordData['log']);

        $fieldValues = [
            'log_level' => $log->getLogLevel()->value,
            'item' => $log->getOrderItemId(),
            'type' => $log->getType(),
            'message' => $log->getMessage(),
            'arguments' => $this->jsonEncodeWithThrowable($log->getArguments()),
            'request_id' => $record->getRequestId(),
            'time_micro' => $record->getCreated(),
            'level' => $record->getLevel(),
            'data' => $this->jsonEncodeWithThrowable($recordData),
        ];

        $logRepository = GeneralUtility::makeInstance(LogRepository::class);
        $logRepository->insert($fieldValues);

        return $this;
    }

    public function jsonEncodeWithThrowable(array $dataToEncode): string
    {
        $data = '';
        if (!empty($dataToEncode)) {
            // Fold an exception into the message, and string-ify it into recordData so it can be jsonified.
            if (isset($dataToEncode['exception']) && $dataToEncode['exception'] instanceof Throwable) {
                $dataToEncode['exception'] = (string)$dataToEncode['exception'];
            }
            $data = '- ' . json_encode($dataToEncode);
        }

        return $data;
    }
}
