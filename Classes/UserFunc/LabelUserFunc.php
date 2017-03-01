<?php

namespace Caretaker\Caretaker\UserFunc;

class LabelUserFunc
{
    public function getLabel(&$incomingParameters)
    {
        $table = $incomingParameters['table'];
        $row = $incomingParameters['row'];
        $titleParts = [];

        switch ($table) {
            case 'tx_caretaker_instance_override':
                $tableTitle = '';
                $type = $row['type'];
                if (is_array($type) && count($type) > 0) {
                    $type = $type[0];
                }
                foreach ($GLOBALS['TCA'][$table]['columns']['type']['config']['items'] as $item) {
                    if ($item[1] == $type) {
                        $tableTitle = $item[0];
                    }
                }
                if (substr($tableTitle, 0, 4) === 'LLL:') {
                    $tableTitle = $GLOBALS['LANG']->sL($tableTitle);
                }
                $titleParts[] = $tableTitle;
                if ($type == 'test_configuration') {
                    $test = $row['test'];
                    if (is_array($test) && count($test) > 0) {
                        $test = array_shift($test);
                    }
                    $testRecord = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('title', 'tx_caretaker_test', 'uid=' . (int)$test);
                    $titleParts[] = $testRecord['title'];
                } else if ($type == 'curl_option') {
                    $curlOption = $row['curl_option'];
                    if (is_array($curlOption) && count($curlOption) > 0) {
                        $curlOption = array_shift($curlOption);
                    }
                    $titleParts[] = $curlOption;
                }
                break;
        }

        $incomingParameters['title'] = implode(', ', $titleParts);
    }
}
