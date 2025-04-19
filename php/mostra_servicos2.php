<?php
include 'identifica.cripto.php';

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexao falhou: " . $conn->connect_error);
}

$sql = "
SELECT
    s.nome_servico,
    a.nome_area_mte AS area,
    p.nome_pessoa_alvo,
    s.comentarios AS descricao,
    GROUP_CONCAT(DISTINCT IF(tc.nome_tipo_campo = 'link_mte', vc.nome_valor_campo, NULL) SEPARATOR '|') AS links_mte,
    MAX(IF(tc.nome_tipo_campo = 'link_denuncia_cadastramento', vc.nome_valor_campo, NULL)) AS link_denuncia,
    MAX(IF(tc.nome_tipo_campo = 'telefone_denuncia_informacoes', vc.nome_valor_campo, NULL)) AS telefone_denuncia,
    MAX(IF(tc.nome_tipo_campo = 'email_solicitacoes', vc.nome_valor_campo, NULL)) AS email_solicitacoes
FROM servicos s
JOIN areas_mte a ON a.id_chave_area_mte = s.id_area_mte
JOIN pessoas_alvo p ON p.id_chave_pessoa_alvo = s.id_pessoa_alvo
LEFT JOIN campos_servicos cs ON cs.id_servico = s.id_chave_servico
LEFT JOIN tipos_campos tc ON tc.id_chave_tipo_campo = cs.id_tipo_campo
LEFT JOIN valores_campos vc ON vc.id_chave_valor_campo = cs.id_valor_campo
GROUP BY s.id_chave_servico
ORDER BY s.nome_servico
LIMIT 50;
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Serviços Disponíveis</title>
    <style>
        body {
            font-family: sans-serif;
            background: #f9f9f9;
            margin: 0;
            padding: 10px;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 16px;
        }
        .card {
            background: white;
            border-radius: 8px;
            padding: 16px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        .card h2 {
            font-size: 18px;
            margin: 0 0 10px;
        }
        .card p {
            margin: 4px 0;
        }
        .card a {
            color: #0056b3;
            text-decoration: none;
        }
        .card a:hover {
            text-decoration: underline;
        }
        .link-group {
            margin-top: 10px;
            font-size: 0.95em;
        }
    </style>
</head>
<body>
    <h1>Serviços Disponíveis</h1>
    <div class="grid">
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="card">
                <h2><?= htmlspecialchars($row['nome_servico']) ?></h2>
                <p><strong>Área:</strong> <?= htmlspecialchars($row['area']) ?></p>
                <p><strong>Público-alvo:</strong> <?= htmlspecialchars($row['nome_pessoa_alvo']) ?></p>
                <?php if ($row['descricao']): ?>
                    <p><strong>Descrição:</strong> <?= nl2br(htmlspecialchars($row['descricao'])) ?></p>
                <?php endif; ?>

                <?php if (!empty($row['links_mte'])): ?>
                    <div class="link-group">
                        <strong>Venha conhecer:</strong>
                        <ul>
                            <?php foreach (explode('|', $row['links_mte']) as $url): ?>
                                <?php if (!empty($url)): ?>
                                    <li><a href="<?= htmlspecialchars($url) ?>" target="_blank"><?= htmlspecialchars(parse_url($url, PHP_URL_HOST) ?: $url) ?></a></li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if (!empty($row['link_denuncia'])): ?>
                    <div class="link-group">
                        <strong>Canal de Denúncia:</strong><br>
                        <a href="<?= htmlspecialchars($row['link_denuncia']) ?>" target="_blank">Acessar canal de denúncia</a>
                    </div>
                <?php endif; ?>

                <?php if (!empty($row['telefone_denuncia'])): ?>
                    <div class="link-group">
                        <strong>Telefone:</strong> <?= htmlspecialchars($row['telefone_denuncia']) ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($row['email_solicitacoes'])): ?>
                    <div class="link-group">
                        <strong>Email para solicitações:</strong> <a href="mailto:<?= htmlspecialchars($row['email_solicitacoes']) ?>"><?= htmlspecialchars($row['email_solicitacoes']) ?></a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
