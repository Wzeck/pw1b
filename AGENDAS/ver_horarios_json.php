<?php
session_start();

if (!isset($_SESSION["usuario_id"])) {
    die("Acesso negado.");
}

$conn = new mysqli("localhost:3306", "root", "", "agenda_etec");
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

$id_sala = $_GET["id_sala"];
$data_reserva = $_GET["data_reserva"];
$id_usuario = $_SESSION["usuario_id"];

// Cria lista de horários fixos (exemplo: 08h às 18h, de hora em hora)
$horarios = [];
for ($h = 8; $h <= 18; $h++) {
    $horaFormatada = str_pad($h, 2, "0", STR_PAD_LEFT) . ":00";
    $horarios[$horaFormatada] = [
        "hora" => $horaFormatada,
        "status" => "Disponível",
        "usuario" => "-",
        "class" => "livre"
    ];
}

// Pega reservas existentes
$sql = "SELECT r.hora_inicio, r.hora_fim, u.nome, r.id_usuario
        FROM reservas r
        JOIN usuarios u ON r.id_usuario = u.id
        WHERE r.id_sala = ? AND r.data_reserva = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $id_sala, $data_reserva);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $inicio = intval(substr($row["hora_inicio"], 0, 2));
    $fim = intval(substr($row["hora_fim"], 0, 2));

    for ($h = $inicio; $h < $fim; $h++) {
        $horaFormatada = str_pad($h, 2, "0", STR_PAD_LEFT) . ":00";

        $horarios[$horaFormatada] = [
            "hora" => $horaFormatada,
            "status" => ($row["id_usuario"] == $id_usuario ? "Reservado por você" : "Reservado por {$row['nome']}"),
            "usuario" => $row["nome"],
            "class" => ($row["id_usuario"] == $id_usuario ? "meu" : "ocupado")
        ];
    }
}

echo json_encode(array_values($horarios));

$conn->close();
?>
