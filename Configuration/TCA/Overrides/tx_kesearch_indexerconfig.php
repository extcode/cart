<?php

defined('TYPO3_MODE') or die();

$GLOBALS['TCA']['tx_kesearch_indexerconfig']['columns']['startingpoints_recursive']['displayCond'] .= ',cartproductindexer';
