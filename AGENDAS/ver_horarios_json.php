<?php
session_start();

// Só acessa se estiver logado
if (!isset($_SESSION["usuario_id"])) {
    die("Acesso negado. Faça login primeiro.");
}

$id_usuario = $_SESSION["usuario_id"];

// Conexão com banco
$conn = new mysqli("localhost:3306", "root", "", "agenda_etec");
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

$id_sala = $_GET["id_sala"];
$data_reserva = $_GET["data_reserva"];

// Cria lista de horários fixos (08:00 até 22:00, de 30 em 30 min)
$horarios = [];
for ($h = 8; $h <= 22; $h++) {
    foreach (["00", "30"] as $min) {
        if ($h == 22 && $min == "30") continue; // evita 22:30
        $horaFormatada = str_pad($h, 2, "0", STR_PAD_LEFT) . ":$min";
        $horarios[$horaFormatada] = [
            "hora" => $horaFormatada,
            "status" => "Disponível",
            "usuario" => "-",
            "class" => "livre"
        ];
    }
}

// Busca reservas já feitas nesse dia/sala
$sql = "SELECT r.hora_inicio, r.hora_fim, r.id_usuario, u.nome 
        FROM reservas r
        JOIN usuarios u ON r.id_usuario = u.id
        WHERE r.id_sala = ? AND r.data_reserva = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $id_sala, $data_reserva);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $inicio = strtotime($row["hora_inicio"]);
    $fim = strtotime($row["hora_fim"]);

    // Marca os horários ocupados de 30 em 30 min
    for ($t = $inicio; $t < $fim; $t += 30 * 60) {
        $horaFormatada = date("H:i", $t);
        if (isset($horarios[$horaFormatada])) {
            $horarios[$horaFormatada] = [
                "hora" => $horaFormatada,
                "status" => ($row["id_usuario"] == $id_usuario 
                            ? "Reservado por você" 
                            : "Reservado por {$row['nome']}"),
                "usuario" => $row["nome"],
                "class" => ($row["id_usuario"] == $id_usuario ? "meu" : "ocupado")
            ];
        }
    }
}

$stmt->close();
$conn->close();

// Retorna os horários em JSON para o JavaScript
echo json_encode(array_values($horarios));
?>
