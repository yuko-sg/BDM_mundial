<?php
session_start();
require_once '../models/db_connection.php';

// same
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) {
    header("Location: ../views/dashboard.php");
    exit();
}

//  same
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_post = intval($_POST['id_post']);
    $accion = $_POST['accion'];
    
    // inputs
    if ($id_post <= 0 || !in_array($accion, ['aprobar', 'rechazar'])) {
        header("Location: ../views/aprobar_posts.php?error=datos_invalidos");
        exit();
    }
    
    $conn = getConnection();
    
    if ($accion === 'aprobar') {
        // aprobar usando sp
        $fecha_aprobacion = date('Y-m-d H:i:s'); 
        $stmt = $conn->prepare("CALL sp_aprobar_post(?, ?)");
        $stmt->bind_param("is", $id_post, $fecha_aprobacion);
        
        if ($stmt->execute()) {
            $stmt->close();
            // limpiar resultados
            while ($conn->more_results()) {
                $conn->next_result();
            }
            $conn->close();
            header("Location: ../views/aprobar_posts.php?success=aprobado");
            exit();
        } else {
            $stmt->close();
            while ($conn->more_results()) {
                $conn->next_result();
            }
            $conn->close();
            header("Location: ../views/aprobar_posts.php?error=error_aprobar");
            exit();
        }
    } else if ($accion === 'rechazar') {
        // rechazar post
        $stmt = $conn->prepare("CALL sp_rechazar_post(?)");
        $stmt->bind_param("i", $id_post);
        
        if ($stmt->execute()) {
            $stmt->close();
            while ($conn->more_results()) {
                $conn->next_result();
            }
            $conn->close();
            header("Location: ../views/aprobar_posts.php?success=rechazado");
            exit();
        } else {
            $stmt->close();
            while ($conn->more_results()) {
                $conn->next_result();
            }
            $conn->close();
            header("Location: ../views/aprobar_posts.php?error=error_rechazar");
            exit();
        }
    }
} else {
    // redirecctionar si se accede directamente sin POST
    header("Location: ../views/aprobar_posts.php");
    exit();
}
?>

