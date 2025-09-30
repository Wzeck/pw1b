<?php
session_start(); // Inicia sessão

// Conexão com banco
$conn = new mysqli("localhost:3306", "root", "", "agenda_etec");
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Só permite se o envio for POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Verifica se os campos foram enviados
    if (isset($_POST["nome"]) && isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["tipo"])) {
        $nome = $_POST["nome"];
        $email = $_POST["email"];
        $password = $_POST["password"];
        $tipo = $_POST["tipo"];

        // Criptografa a senha (segurança)
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Prepara a query para inserir usuário
        $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("ssss", $nome, $email, $hashedPassword, $tipo);

            // Tenta executar
            if ($stmt->execute()) {
                echo "Usuário cadastrado com sucesso: $nome ($email)";
            } else {
                // Se já existir e-mail igual
                if ($conn->errno === 1062) {
                    echo "Erro: Já existe um usuário com esse e-mail.";
                } else {
                    echo "Erro ao cadastrar usuário: " . $stmt->error;
                }
            }

            $stmt->close();
        } else {
            echo "Erro na preparação da query: " . $conn->error;
        }
    } else {
        echo "Todos os campos são obrigatórios (nome, email, senha, tipo).";
    }
} else {
    echo "Acesso inválido. Use um formulário para enviar os dados.";
}

$conn->close();
?>
