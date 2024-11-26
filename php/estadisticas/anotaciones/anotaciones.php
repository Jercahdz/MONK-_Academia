<?php
session_start();
include("../../conexion.php");

// Parámetros para la paginación
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$searchParam = '%' . $search . '%';

$recordsPerPage = 15;
$offset = ($page - 1) * $recordsPerPage;

// Contar el total de registros
$sqlCount = "
    SELECT COUNT(*) as total
    FROM Jugadores j
    LEFT JOIN Anotaciones a ON j.jugadorId = a.jugadorId
    WHERE CONCAT(j.nombreJugador, ' ', j.apellidos) LIKE ?
";
$stmtCount = $conn->prepare($sqlCount);
$stmtCount->bind_param("s", $searchParam);
$stmtCount->execute();
$resultCount = $stmtCount->get_result();
$totalRecords = $resultCount->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $recordsPerPage);

// Obtener registros para la página actual
$sql = "
    SELECT 
        j.jugadorId, j.nombreJugador, j.apellidos, j.edad, 
        IFNULL(a.cantidadAnotaciones, 0) AS cantidadAnotaciones
    FROM Jugadores j
    LEFT JOIN Anotaciones a ON j.jugadorId = a.jugadorId
    WHERE CONCAT(j.nombreJugador, ' ', j.apellidos) LIKE ?
    LIMIT ? OFFSET ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sii", $searchParam, $recordsPerPage, $offset);
$stmt->execute();
$result = $stmt->get_result();

// Generar las filas de la tabla
while ($row = $result->fetch_assoc()) {
    echo "<tr>
        <td>" . htmlspecialchars($row['nombreJugador']) . "</td>
        <td>" . htmlspecialchars($row['apellidos']) . "</td>
        <td>" . htmlspecialchars($row['edad']) . "</td>
        <td>" . htmlspecialchars($row['cantidadAnotaciones']) . "</td>
        <td>
            <button class='btn-agregar-goles btn-table btn-sm' data-jugador-id='" . htmlspecialchars($row['jugadorId']) . "'>Agregar Goles</button>
            <button class='btn-editar btn-table btn-sm' data-jugador-id='" . htmlspecialchars($row['jugadorId']) . "' data-cantidad-anotaciones='" . htmlspecialchars($row['cantidadAnotaciones']) . "'>Editar</button>
            <button class='btn-borrar-anotacion btn-table btn-sm' data-jugador-id='" . htmlspecialchars($row['jugadorId']) . "'>Borrar</button>
        </td>
    </tr>";
}

// Generar los controles de paginación
echo '<tr><td colspan="5"><nav><ul class="pagination justify-content-center">';

// Botón "Anterior"
if ($page > 1) {
    echo "<li class='page-item'><a class='page-link' href='#' data-page='" . ($page - 1) . "'>Anterior</a></li>";
}

// Números de página
for ($i = 1; $i <= $totalPages; $i++) {
    $active = $i == $page ? 'active' : '';
    echo "<li class='page-item $active'><a class='page-link' href='#' data-page='$i'>$i</a></li>";
}

// Botón "Siguiente"
if ($page < $totalPages) {
    echo "<li class='page-item'><a class='page-link' href='#' data-page='" . ($page + 1) . "'>Siguiente</a></li>";
}

echo '</ul></nav></td></tr>';

// Cerrar conexión
$stmt->close();
$stmtCount->close();
$conn->close();
?>