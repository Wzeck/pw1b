<?php
session_start();
$conn = new mysqli("localhost:3306", "root", "", "agenda_etec");
if ($conn->connect_error) die("Falha na conexão: ".$conn->connect_error);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $tipo = $_POST["tipo"];

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nome, $email, $hashedPassword, $tipo);
    if ($stmt->execute()) {
        echo "Usuário cadastrado com sucesso!";
        header("Refresh:2; url=login.html"); // Redireciona para login
    } else {
        if ($conn->errno === 1062) echo "Erro: E-mail já cadastrado.";
        else echo "Erro ao cadastrar: ".$stmt->error;
    }
    $stmt->close();
} else {
    echo "Acesso inválido.";
}
$conn->close();
?>
