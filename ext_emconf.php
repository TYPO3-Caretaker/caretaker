<?php
$EM_CONF[$_EXTKEY] = array (
  'title' => 'Caretaker',
  'description' => 'Server for caretaker observation system',
  'category' => 'misc',
  'author' => 'Martin Ficzel,Thomas Hempel,Christopher Hlubek,Tobias Liebig',
  'author_email' => 'ficzel@work.de,hempel@work.de,hlubek@networkteam.com,typo3@etobi.de',
  'shy' => '',
  'dependencies' => 'cms',
  'conflicts' => '',
  'priority' => '',
  'module' => '',
  'state' => 'stable',
  'internal' => '',
  'uploadfolder' => 0,
  'createDirs' => 'typo3temp/caretaker/charts',
  'modify_tables' => '',
  'clearCacheOnLoad' => 0,
  'lockType' => '',
  'author_company' => '',
  'version' => '0.8.0',
  'constraints' => 
  array (
    'depends' => 
    array (
      'caretaker_instance' => '0.7.0-0.0.0',
      'typo3' => '6.2.0-7.6.99',
      'php' => '5.3.0-7.0.99',
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
