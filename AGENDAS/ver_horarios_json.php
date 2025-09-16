<?php
session_start();
if(!isset($_SESSION["usuario_id"])) die("Acesso negado");

$conn = new mysqli("localhost:3306", "root", "", "agenda_etec");
if($conn->connect_error) die("Falha na conexão: ".$conn->connect_error);

$id_sala = $_GET['id_sala'] ?? "";
$data_reserva = $_GET['data_reserva'] ?? "";

$horarios_padrao = ["08:00","09:00","10:00","11:00","12:00","13:00","14:00","15:00","16:00","17:00","18:00"];
$ocupados = [];

if($id_sala && $data_reserva){
    $stmt = $conn->prepare("SELECT hora_inicio, hora_fim, id_usuario FROM reservas WHERE id_sala=? AND data_reserva=?");
    $stmt->bind_param("is", $id_sala, $data_reserva);
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc()){
        $ocupados[] = $row;
    }
}

$conn->close();

$resposta = [];
foreach($horarios_padrao as $hora){
$status = "Livre";
$class = "livre";
foreach($ocupados as $r){
if($hora >= $r['hora_inicio'] && $hora < $r['hora_fim']){
if($r['id_usuario'] == $_SESSION["usuario_id"]){
$status = "Meu horário";
$class = "meu";
} else {
$status = "Ocupado";
$class = "ocupado";
}
break;
}
}
$resposta[] = ["hora"=>$hora,"status"=>$status,"class"=>$class];
}

header('Content-Type: application/json');
echo json_encode($resposta);
?>
