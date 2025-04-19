<?php

function gerar_inserts_para_csv($caminho_csv, $saida) {
    $nome_arquivo = basename($caminho_csv);
    fwrite($saida, "-- Arquivo: $nome_arquivo\n");
    fwrite($saida, "INSERT IGNORE INTO arquivos_fonte (nome_arquivo_fonte_CSV) VALUES (\"$nome_arquivo\");\n");

    $handle = fopen($caminho_csv, "r");
    if (!$handle) return;

    $cabecalho = fgetcsv($handle, 0, '|');
    if ($cabecalho === false) return;

    // INSERTs tipos_campos
    foreach ($cabecalho as $i => $campo) {
        $campo = trim($campo);
        $ordem = $i + 1;
        fwrite(
            $saida,
            "INSERT IGNORE INTO tipos_campos (nome_tipo_campo, id_arquivo_fonte, ordem) VALUES (\"$campo\", (SELECT id_chave_arquivo_fonte FROM arquivos_fonte WHERE nome_arquivo_fonte_CSV = \"$nome_arquivo\"), $ordem);\n"
        );
    }

    // Índices obrigatórios
    $index_servico = array_search("servico", $cabecalho);
    $index_area = array_search("area", $cabecalho);
    $index_publico = array_search("publico_alvo", $cabecalho);

    if ($index_servico === false || $index_area === false || $index_publico === false) {
        fwrite($saida, "-- ⚠️ Arquivo $nome_arquivo sem colunas obrigatórias: servico, area, publico_alvo\n\n");
        return;
    }

    $linhas_buffer = [];
    $linha_id = 0;

    // Leitura das linhas, gera INSERTs em servicos e valores_campos
    while (($linha = fgetcsv($handle, 0, '|')) !== false) {
        $linha_id++;
        $valor_servico = trim($linha[$index_servico]);
        $valor_area = trim($linha[$index_area]);
        $valor_publico = trim($linha[$index_publico]);

        if ($valor_servico === '' || $valor_area === '' || $valor_publico === '') {
            fwrite($saida, "-- ⚠️ Linha $linha_id ignorada por dados obrigatórios vazios em $nome_arquivo\n");
            continue;
        }

        // INSERT servicos
        fwrite($saida,
            "INSERT INTO servicos (nome_servico, id_area_mte, id_pessoa_alvo) VALUES (\n" .
            "  \"$valor_servico\",\n" .
            "  (SELECT id_chave_area_mte FROM areas_mte WHERE nome_area_mte = \"$valor_area\"),\n" .
            "  (SELECT id_chave_pessoa_alvo FROM pessoas_alvo WHERE nome_pessoa_alvo = \"$valor_publico\")\n" .
            ");\n"
        );

        // INSERT valores_campos (não depende de ordem)
        foreach ($linha as $valor) {
            $valor = trim($valor);
            if ($valor !== '') {
                fwrite($saida, "INSERT IGNORE INTO valores_campos (nome_valor_campo) VALUES (\"$valor\");\n");
            }
        }

        // Armazena para depois gerar campos_servicos
        $linhas_buffer[] = [
            'valores' => $linha,
            'cabecalho' => $cabecalho,
            'servico' => $valor_servico,
            'arquivo' => $nome_arquivo
        ];
    }

    fclose($handle);

    // Agora sim: gera INSERTs para campos_servicos (após servicos e valores_campos)
    foreach ($linhas_buffer as $dados) {
        $linha = $dados['valores'];
        $cabecalho = $dados['cabecalho'];
        $valor_servico = $dados['servico'];
        $nome_arquivo = $dados['arquivo'];

        foreach ($linha as $i => $valor) {
            $valor = trim($valor);
            if ($valor === '') continue;

            $campo = trim($cabecalho[$i]);
            $nome_campo_servico = "{$valor_servico}_{$campo}_$i";

            fwrite($saida,
                "INSERT INTO campos_servicos (nome_campo_servico, id_arquivo_fonte, id_tipo_campo, id_valor_campo, id_servico)\n" .
                "VALUES (\n" .
                "  \"$nome_campo_servico\",\n" .
                "  (SELECT id_chave_arquivo_fonte FROM arquivos_fonte WHERE nome_arquivo_fonte_CSV = \"$nome_arquivo\"),\n" .
                "  (SELECT id_chave_tipo_campo FROM tipos_campos WHERE nome_tipo_campo = \"$campo\" AND id_arquivo_fonte = (SELECT id_chave_arquivo_fonte FROM arquivos_fonte WHERE nome_arquivo_fonte_CSV = \"$nome_arquivo\")),\n" .
                "  (SELECT id_chave_valor_campo FROM valores_campos WHERE nome_valor_campo = \"$valor\"),\n" .
                "  (SELECT id_chave_servico FROM servicos WHERE nome_servico = \"$valor_servico\")\n" .
                ");\n"
            );
        }
    }

    fwrite($saida, "\n-- Fim do processamento de $nome_arquivo\n\n");
}

function processar_todos_csvs($saida) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator('.', FilesystemIterator::SKIP_DOTS)
    );

    foreach ($iterator as $arquivo) {
        if (
            $arquivo->isFile() &&
            strtolower($arquivo->getExtension()) === 'csv' &&
            strpos($arquivo->getPath(), '/csv_') !== false
        ) {
            $caminho = $arquivo->getPathname();
            fwrite($saida, "-- 📂 Processando arquivo: $caminho\n");
            gerar_inserts_para_csv($caminho, $saida);
        }
    }
}

// Execução
$saida = fopen("saida_inserts.sql", "w");
processar_todos_csvs($saida);
fclose($saida);

echo "✅ Script concluído. Saída gerada em 'saida_inserts.sql'\n";

