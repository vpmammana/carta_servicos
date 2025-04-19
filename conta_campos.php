<?php

function contar_campos_csv($caminho) {
    $handle = fopen($caminho, "r");
    if (!$handle) return false;

    $contagens = [];
    while (($linha = fgetcsv($handle, 0, '|')) !== false) {
        $contagens[] = count($linha);
    }
    fclose($handle);
    return $contagens;
}

function verificar_csvs_recursivamente($pasta) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($pasta, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($iterator as $arquivo) {
        if ($arquivo->isFile() && strtolower($arquivo->getExtension()) === 'csv') {
            $caminho = $arquivo->getPathname();
            $contagens = contar_campos_csv($caminho);

            if ($contagens === false || count($contagens) === 0) {
                continue;
            }

            $primeiro = $contagens[0];
            $todos_iguais = count(array_unique($contagens)) === 1;

            if ($todos_iguais) {
                echo "$caminho -> OK\n";
            } else {
                $contagens_str = implode(' ', $contagens);
                echo "\033[31m$caminho\033[0m $contagens_str \n";
            }
        }
    }
}

// Executa a verificação a partir do diretório atual
verificar_csvs_recursivamente('.');

