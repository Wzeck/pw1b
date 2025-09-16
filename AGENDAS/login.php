<?php
session_start();

$conn = new mysqli("localhost:3306", "root", "", "agenda_etec");
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
if (isset($_POST["email"], $_POST["password"])) {
$email = $_POST["email"];
$password = $_POST["password"];

$stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
$user = $result->fetch_assoc();

if (password_verify($password, $user["senha"])) {
$_SESSION["usuario_id"] = $user["id"];
$_SESSION["nome"] = $user["nome"];
$_SESSION["tipo"] = $user["tipo"];

header("Location: agendar.html");
exit;
} else {
echo "Senha incorreta.";
}
} else {
echo "Usuário não encontrado.";
}
} else {
echo "Campos obrigatórios não enviados.";
}
} else {
    echo "Acesso inválido.";
}

$conn->close();
?>