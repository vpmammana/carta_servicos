<?php
include 'identifica.cripto.php';

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Função recursiva para montar a árvore
function exibirArvore($conn, $pai = null, $nivel = 0) {
    $stmt = $conn->prepare("SELECT id_chave_area_mte, nome_area_mte FROM areas_mte WHERE pai " . 
                           (is_null($pai) ? "IS NULL" : "= ?") . " ORDER BY nome_area_mte");
    if (!is_null($pai)) {
        $stmt->bind_param("i", $pai);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        echo str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;", $nivel) . "↳ " . htmlspecialchars($row['nome_area_mte']) . "<br>";
        // Chamada recursiva para os filhos
        exibirArvore($conn, $row['id_chave_area_mte'], $nivel + 1);
    }

    $stmt->close();
}

echo "<h2>Árvore de Áreas do MTE</h2>";
exibirArvore($conn);

$conn->close();
?>

