<?php
session_start();

// Conexão com o banco
$conn = new mysqli("localhost:3306", "root", "", "agenda_etec");
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Verifica se foi enviado via POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
// Verifica se todos os campos obrigatórios foram enviados
if (isset($_POST["nome"]) && isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["tipo"])) {
$nome = $_POST["nome"];
$email = $_POST["email"];
$password = $_POST["password"];
$tipo = $_POST["tipo"];

// Hashear a senha antes de salvar
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Prepara a inserção
$stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, ?)");
if ($stmt) {
$stmt->bind_param("ssss", $nome, $email, $hashedPassword, $tipo);

// Executa e verifica sucesso
if ($stmt->execute()) {
echo "Usuário cadastrado com sucesso: $nome ($email)";
} else {
// Verifica se foi erro de e-mail duplicado
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