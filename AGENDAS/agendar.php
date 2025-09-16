<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION["usuario_id"])) {
    die("Acesso negado. Faça login primeiro.");
}

$conn = new mysqli("localhost:3306", "root", "", "agenda_etec");
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] === "POST") {
$id_sala = $_POST["id_sala"];
$data_reserva = $_POST["data_reserva"];
$hora_inicio = $_POST["hora_inicio"];
$hora_fim = $_POST["hora_fim"];
$id_usuario = $_SESSION["usuario_id"]; // ID do usuário logado

// Verifica se já existe uma reserva no mesmo horário
$verifica = $conn->prepare("SELECT * FROM reservas 
WHERE id_sala = ? AND data_reserva = ? 
AND (
(hora_inicio < ? AND hora_fim > ?) OR
(hora_inicio < ? AND hora_fim > ?) OR
(hora_inicio >= ? AND hora_fim <= ?)
)");

$verifica->bind_param("isssssss", $id_sala, $data_reserva, $hora_fim, $hora_fim, $hora_inicio, $hora_inicio, $hora_inicio, $hora_fim);
$verifica->execute();
$resultado = $verifica->get_result();

if ($resultado->num_rows > 0) {
echo "Erro: Já existe uma reserva para essa sala nesse horário.";
} else {
// Inserir nova reserva
$stmt = $conn->prepare("INSERT INTO reservas (id_sala, id_usuario, data_reserva, hora_inicio, hora_fim) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("iisss", $id_sala, $id_usuario, $data_reserva, $hora_inicio, $hora_fim);

if ($stmt->execute()) {
echo "Reserva feita com sucesso!";
} else {
echo "Erro ao agendar: " . $stmt->error;
}

$stmt->close();
}

    $verifica->close();
} else {
    echo "Acesso inválido.";
}

$conn->close();
?>