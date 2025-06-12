<?php
if (php_sapi_name() !== 'cli') {
    exit("This script must be run from the command line.\n");
}

if (!class_exists('ZipArchive')) {
    exit("The ZipArchive extension is required.\n");
}

$buildDir = __DIR__ . '/builds';
if (!is_dir($buildDir)) {
    mkdir($buildDir, 0755, true);
}

$version = date('Ymd-His');

function zip_folder($source, $destination) {
    $zip = new ZipArchive();
    if ($zip->open($destination, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        return false;
    }

    $source = realpath($source);
    if (!$source) {
        return false;
    }

    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($files as $file) {
        if ($file->isDir()) {
            continue;
        }
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen($source) + 1);
        $zip->addFile($filePath, $relativePath);
    }

    return $zip->close();
}

$modules = [
    'theme/dead-bastard-society-theme',
    'plugin-terminal/lucidus-terminal-pro',
    'plugin-members/dbs-members-plugin'
];

foreach ($modules as $module) {
    $modulePath = __DIR__ . '/' . $module;
    $moduleName = basename($module);
    $zipPath = $buildDir . '/' . $moduleName . '-' . $version . '.zip';
    if (!zip_folder($modulePath, $zipPath)) {
        fwrite(STDERR, "Failed to package $module\n");
    }
}

$log = [
    'version' => $version,
    'modules' => $modules,
    'timestamp' => date('c')
];
file_put_contents($buildDir . '/build-log-' . $version . '.json', json_encode($log, JSON_PRETTY_PRINT));

echo "Build completed for version $version\n";
