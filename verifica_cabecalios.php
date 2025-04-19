<?php

function encontrarCSV($dir) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $file) {
        if ($file->isDir() && preg_match('/\/csv_[^\/]+$/', $file->getPathname())) {
            foreach (glob($file->getPathname() . '/*.csv') as $csvFile) {
                imprimirCabecalhoCSV($csvFile);
            }
        }
    }
}

function imprimirCabecalhoCSV($arquivo) {
    if (($handle = fopen($arquivo, "r")) !== false) {
        $cabecalho = fgetcsv($handle);
        fclose($handle);
        echo "🔹 Cabeçalho: " . implode(" | ", $cabecalho) . "\t<-$arquivo \n";
    } else {
        echo "⚠️ Não foi possível abrir: $arquivo\n";
    }
}

encontrarCSV(getcwd());

