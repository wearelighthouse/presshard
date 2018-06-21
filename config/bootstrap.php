<?php

if (!defined('ABSPATH')) {
    exit;
}

require __DIR__ . '/paths.php';

$entityTypes = ['Form', 'Menu', 'Page', 'PostType', 'Route', 'Taxonomy'];

foreach ($entityTypes as $entityType) {
    $directoryPath = THEME . DS . 'src' . DS . $entityType;

    if (!file_exists($directoryPath)) {
        continue;
    }

    $directory = new DirectoryIterator($directoryPath);

    foreach ($directory as $file) {
        if (!$file->isDir()) {
            $className = 'Theme\\' . $entityType . '\\' . $file->getFileName();
            $className::instance()->register();
        }
    }
}

require PRESS_HARD . DS . 'vendor' . DS . 'autoload.php';
