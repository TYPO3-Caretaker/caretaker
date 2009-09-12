<?php

	// DO NOT REMOVE OR CHANGE THESE 3 LINES:
define('TYPO3_MOD_PATH', '../typo3conf/ext/caretaker/mod_nav/');
$BACK_PATH='../../../../typo3/';
$MCONF['name'] = 'txcaretakerNav';

$MCONF['access'] = 'user,group';
$MCONF['script'] = 'index.php';
$MCONF['navFrameScript'] = 'index.php';
$MCONF['default']['tabs_images']['tab'] = 'moduleicon.gif';
$MCONF['default']['ll_ref'] = 'LLL:EXT:caretaker/mod_nav/locallang_mod.xml';
?>