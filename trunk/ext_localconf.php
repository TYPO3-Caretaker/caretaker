<?php
if (TYPO3_MODE=='BE')    {
    $TYPO3_CONF_VARS['SC_OPTIONS']['GLOBAL']['cliKeys'][$_EXTKEY] = array('EXT:'.$_EXTKEY.'/classes/class.tx_caretaker_Cli.php','_CLI_caretaker');
}

t3lib_extMgm::addPItoST43($_EXTKEY,'pi_overview/class.tx_caretaker_pi_overview.php','_pi_overview','list_type',0);

?> 