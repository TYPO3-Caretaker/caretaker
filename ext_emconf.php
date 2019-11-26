<?php
$EM_CONF[$_EXTKEY] = array (
  'title' => 'Caretaker',
  'description' => 'Server for caretaker observation system',
  'category' => 'misc',
  'author' => 'Martin Ficzel, Thomas Hempel, Christopher Hlubek, Tobias Liebig, Jan Haffner',
  'author_email' => 'ficzel@work.de,hempel@work.de,hlubek@networkteam.com,typo3@etobi.de',
  'state' => 'stable',
  'uploadfolder' => 0,
  'createDirs' => 'typo3temp/caretaker/charts',
  'clearCacheOnLoad' => 0,
  'author_company' => '',
  'version' => '1.0.2',
  'constraints' => 
  array (
    'depends' => 
    array (
      'caretaker_instance' => '1.0.0-2.99.99',
      'typo3' => '7.6.0-8.7.99',
    ),
    'conflicts' => 
    array (
    ),
    'suggests' => 
    array (
    ),
  ),
  'autoload' => 
  array (
    'classmap' => 
    array (
      0 => 'Classes',
      1 => 'interfaces',
      2 => 'scheduler',
      3 => 'pi_abstract',
      4 => 'pi_base',
      5 => 'pi_graphreport',
      6 => 'pi_overview',
      7 => 'pi_singleview',
    ),
  ),
  '_md5_values_when_last_written' => '',
);
