<?php
session_start();
require_once '../models/db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../views/login.php");
    exit();
}

// Check if post_id was provided
if (!isset($_POST['id_post'])) {
    header("Location: ../views/dashboard.php?error=post_invalido");
    exit();
}

$id_post = intval($_POST['id_post']);
$id_usuario = $_SESSION['user_id'];
$fecha = date('Y-m-d H:i:s');

$conn = getConnection();

$check_stmt = $conn->prepare("SELECT * FROM likes WHERE id_usuario = ? AND id_post = ?");
$check_stmt->bind_param("ii", $id_usuario, $id_post);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows > 0) {
    $check_stmt->close();
    $delete_stmt = $conn->prepare("DELETE FROM likes WHERE id_usuario = ? AND id_post = ?");
    $delete_stmt->bind_param("ii", $id_usuario, $id_post);
    
    if ($delete_stmt->execute()) {
        $delete_stmt->close();
        $conn->close();
        header("Location: " . $_SERVER['HTTP_REFERER'] . "#post-" . $id_post);
        exit();
    }
} else {
    $check_stmt->close();
    $insert_stmt = $conn->prepare("INSERT INTO likes (id_usuario, id_post, fecha) VALUES (?, ?, ?)");
    $insert_stmt->bind_param("iis", $id_usuario, $id_post, $fecha);
    
    try {
        if ($insert_stmt->execute()) {
            $insert_stmt->close();
            $conn->close();
            header("Location: " . $_SERVER['HTTP_REFERER'] . "#post-" . $id_post);
            exit();
        }
    } catch (mysqli_sql_exception $e) {
        // Handle trigger error (self-like attempt)
        if ($e->getCode() == 1644) { // SQLSTATE 45000 maps to error 1644
            $insert_stmt->close();
            $conn->close();
            header("Location: " . $_SERVER['HTTP_REFERER'] . "?error=auto_like#post-" . $id_post);
            exit();
        }
        throw $e; // Re-throw if it's a different error
    }
}

$conn->close();
header("Location: ../views/dashboard.php?error=error_like");
exit();
?>

