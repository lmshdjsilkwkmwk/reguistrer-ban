<?php
// Conexión a la base de datos
$conn = new mysqli('localhost', 'limber01', '5oc9UI]WU_1VRHMF', 'banco');

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener los datos del formulario
$destino = $_POST['destino'];
$monto = $_POST['monto'];
$nombre_usuario_origen = "nombre_usuario_actual"; // Aquí pondrías el nombre de usuario actual, por ejemplo, guardado en sesión

// Buscar al usuario remitente (quien envía)
$sql_origen = "SELECT * FROM usuarios WHERE nombre_usuario = '$nombre_usuario_origen'";
$result_origen = $conn->query($sql_origen);
$usuario_origen = $result_origen->fetch_assoc();

// Verificar si el usuario tiene saldo suficiente
if ($usuario_origen['saldo'] < $monto) {
    die("No tienes suficiente saldo.");
}

// Buscar al destinatario por teléfono o nombre de usuario
$sql_destino = "SELECT * FROM usuarios WHERE telefono = '$destino' OR nombre_usuario = '$destino'";
$result_destino = $conn->query($sql_destino);

if ($result_destino->num_rows > 0) {
    $usuario_destino = $result_destino->fetch_assoc();

    // Iniciar una transacción
    $conn->begin_transaction();

    try {
        // Restar saldo al remitente
        $nuevo_saldo_origen = $usuario_origen['saldo'] - $monto;
        $sql_update_origen = "UPDATE usuarios SET saldo = $nuevo_saldo_origen WHERE id = " . $usuario_origen['id'];
        $conn->query($sql_update_origen);

        // Sumar saldo al destinatario
        $nuevo_saldo_destino = $usuario_destino['saldo'] + $monto;
        $sql_update_destino = "UPDATE usuarios SET saldo = $nuevo_saldo_destino WHERE id = " . $usuario_destino['id'];
        $conn->query($sql_update_destino);

        // Registrar la transacción
        $sql_transaccion = "INSERT INTO transacciones (id_origen, id_destino, monto)
                            VALUES (" . $usuario_origen['id'] . ", " . $usuario_destino['id'] . ", $monto)";
        $conn->query($sql_transaccion);

        // Confirmar la transacción
        $conn->commit();

        echo "Transferencia exitosa. Tu nuevo saldo es: " . $nuevo_saldo_origen . " bs";
    } catch (Exception $e) {
        // Si ocurre algún error, revertimos la transacción
        $conn->rollback();
        echo "Error en la transferencia: " . $e->getMessage();
    }
} else {
    echo "No se encontró un usuario con ese número de teléfono o nombre de usuario.";
}

$conn->close();
?>