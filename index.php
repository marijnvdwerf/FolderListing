<?php

require 'vendor/autoload.php';
require 'File.php';
require 'Folder.php';

$path = $_SERVER['DOCUMENT_ROOT'] . $_SERVER['REQUEST_URI'];
$path = str_replace('//', '/', $path);

$items = scandir($path);
sort($items, SORT_NATURAL | SORT_FLAG_CASE);
$folders = [];
$files = [];

foreach($items as $key => $fileName) {
    $filePath = $path . $fileName;

    if($fileName[0] === '.') {
        continue;
    }

    if(is_dir($filePath)) {
        $folders[] = new Folder($filePath);
    } else {
        $files[] = new File($filePath);
    }
}
?><!doctype html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Custom Directory Listing</title>
    <style type="text/css">
        <?= str_replace("\n", "\n            ", file_get_contents('style.css')); ?>
    </style>
</head>
<body>
    <h1><?php
        echo htmlentities(dirname($_SERVER['REQUEST_URI']));
        if(dirname($_SERVER['REQUEST_URI']) !== '/') {
            echo '/';
        };
        echo '<strong>' . htmlentities(basename($_SERVER['REQUEST_URI'])) . '</strong>';
        ?></h1>
    <ul>
        <?php foreach($folders as $folder) : ?>
            <li><a class="item item-folder" href="<?= htmlentities($folder->name); ?>">
                    <span><?= $folder->name; ?></span>
                    <span class="tags"><?php
                        foreach($folder->tags as $tag) {
                            echo '<strong class="tag tag-' . $tag . '"></strong>';
                        }
                        ?></span>
                    <?php if($folder->favicon) : ?>
                        <img src="<?= $folder->favicon; ?>" class="favicon"/>
                    <?php endif; ?>
                </a></li>
        <?php endforeach; ?>

        <?php foreach($files as $file) : ?>
            <li><a class="item item-file" href="<?= htmlentities($file->name); ?>" type="<?= $file->type; ?>">
                    <span><?= $file->name; ?></span>
                    <span class="tags"><?php
                        foreach($file->tags as $tag) {
                            echo '<strong class="tag tag-' . $tag . '"></strong>';
                        }
                        ?></span>
                    <i></i>
                </a></li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
