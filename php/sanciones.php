<?php
session_start();
include("conexion.php");

// Consulta SQL para obtener los datos de los jugadores y sus sanciones (tarjetas rojas y amarillas)
$sql = "
    SELECT 
        j.jugadorId, j.nombreJugador, j.apellidos, j.edad, 
        IFNULL(s.rojas, 0) AS rojas,
        IFNULL(s.amarillas, 0) AS amarillas
    FROM Jugadores j
    LEFT JOIN Sanciones s ON j.jugadorId = s.jugadorId
";

// Ejecutar la consulta
$result = $conn->query($sql);

// Comprobar si hay resultados
if ($result->num_rows > 0) {
    // Recorrer cada fila de los resultados y generar las filas de la tabla
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row['nombreJugador']) . "</td>
                <td>" . htmlspecialchars($row['apellidos']) . "</td>
                <td>" . htmlspecialchars($row['edad']) . "</td>
                <td>" . htmlspecialchars($row['rojas']) . "</td>
                <td>" . htmlspecialchars($row['amarillas']) . "</td>
                <td>
                    <button class='btn-table btn-sm btn-agregar-sancion' data-jugador-id='" . $row['jugadorId'] . "'>Agregar Sanciones</button>
                    <button class='btn-table btn-sm btn-editar' data-jugador-id='" . $row['jugadorId'] . "' data-rojas='" . $row['rojas'] . "' data-amarillas='" . $row['amarillas'] . "'>Editar</button>
                    <button class='btn-table btn-sm btn-borrar' data-jugador-id='" . $row['jugadorId'] . "'>Borrar</button>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='6'>No hay datos disponibles.</td></tr>";
}

$conn->close();
?>