<?php
if (TYPO3_MODE=='BE')    {
    $TYPO3_CONF_VARS['SC_OPTIONS']['GLOBAL']['cliKeys'][$_EXTKEY] = array('EXT:'.$_EXTKEY.'/classes/class.tx_caretaker_Cli.php','_CLI_caretaker');
}
?> 