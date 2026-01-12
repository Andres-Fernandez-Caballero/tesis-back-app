<?php

foreach (glob(__DIR__ . '/*.php') as $file) {
    if (basename($file) !== '_index.php') {
        require $file;
    }
}
