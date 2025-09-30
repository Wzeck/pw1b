<?php
session_start(); // Inicia sessão para manter login

// Conexão com o banco
$conn = new mysqli("localhost:3306", "root", "", "agenda_etec");
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Só aceita se for via POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Procura usuário pelo e-mail
    $stmt = $conn->prepare("SELECT id, nome, senha, tipo FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    // Se encontrou o usuário
    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();

        // Verifica se a senha está correta
        if (password_verify($password, $usuario["senha"])) {
            // Guarda informações do usuário na sessão
            $_SESSION["usuario_id"] = $usuario["id"];
            $_SESSION["usuario_nome"] = $usuario["nome"];
            $_SESSION["usuario_tipo"] = $usuario["tipo"];

            echo "Login realizado com sucesso. Bem-vindo, " . $usuario["nome"];
            // Aqui você pode usar: header("Location: agendar.html"); para redirecionar
        } else {
            echo "Senha incorreta.";
        }
    } else {
        echo "E-mail não encontrado.";
    }

    $stmt->close();
} else {
    echo "Acesso inválido.";
}

$conn->close();
?>
