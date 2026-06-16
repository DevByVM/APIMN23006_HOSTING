<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

function responder(array $data, int $statusCode = 200): void
{
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

function obtenerConexion(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $host = getenv('DB_HOST');
        $port = getenv('DB_PORT');
        $dbname = getenv('DB_NAME');
        $user = getenv('DB_USER');
        $password = getenv('DB_PASSWORD');

        $pdo = new PDO(
            "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
            $user,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
    }

    return $pdo;
}

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');

if ($basePath === '/') {
    $basePath = '';
}

$path = preg_replace('#^' . preg_quote($basePath, '#') . '#', '', $uri);
$path = trim($path, '/');

$partes = $path === '' ? [] : explode('/', $path);

try {
    if ($path === '') {
        responder([
            'estado' => true,
            'mensaje' => 'API MN23006 funcionando correctamente'
        ]);
    }

    if ($method === 'GET' && count($partes) === 1 && $partes[0] === 'hospitales') {
        $stmt = obtenerConexion()->query('SELECT * FROM hospitales');
        $hospitales = $stmt->fetchAll();

        responder([
            'estado' => true,
            'mensaje' => 'Hospitales obtenidos correctamente',
            'data' => $hospitales
        ]);
    }

    if ($method === 'GET' && count($partes) === 2 && $partes[0] === 'hospitales') {
        $idHospital = $partes[1];

        $stmt = obtenerConexion()->prepare('SELECT * FROM hospitales WHERE IdHospital = :IdHospital');
        $stmt->execute([
            ':IdHospital' => $idHospital
        ]);

        $hospital = $stmt->fetch();

        if (!$hospital) {
            responder([
                'estado' => false,
                'mensaje' => 'Hospital no encontrado'
            ], 404);
        }

        responder([
            'estado' => true,
            'mensaje' => 'Hospital obtenido correctamente',
            'data' => $hospital
        ]);
    }

    if ($method === 'POST' && count($partes) === 1 && $partes[0] === 'hospitales') {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!is_array($data)) {
            responder([
                'estado' => false,
                'mensaje' => 'No se recibió un JSON válido'
            ], 400);
        }

        $stmt = obtenerConexion()->prepare(
            'INSERT INTO hospitales
            (IdHospital, NomHospital, CapacidadAtencion, Especialidades)
            VALUES
            (:IdHospital, :NomHospital, :CapacidadAtencion, :Especialidades)'
        );

        $stmt->execute([
            ':IdHospital' => $data['IdHospital'],
            ':NomHospital' => $data['NomHospital'],
            ':CapacidadAtencion' => $data['CapacidadAtencion'],
            ':Especialidades' => $data['Especialidades']
        ]);

        responder([
            'estado' => true,
            'mensaje' => 'Hospital registrado correctamente'
        ], 201);
    }

    if ($method === 'GET' && count($partes) === 1 && $partes[0] === 'doctores') {
        $stmt = obtenerConexion()->query('SELECT * FROM doctores');
        $doctores = $stmt->fetchAll();

        responder([
            'estado' => true,
            'mensaje' => 'Doctores obtenidos correctamente',
            'data' => $doctores
        ]);
    }

    if ($method === 'POST' && count($partes) === 1 && $partes[0] === 'doctores') {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!is_array($data)) {
            responder([
                'estado' => false,
                'mensaje' => 'No se recibió un JSON válido'
            ], 400);
        }

        $stmt = obtenerConexion()->prepare(
            'INSERT INTO doctores
            (IdDoctor, NombresDoctor, ApellidosDoctor, Especialidad, TurnoAtencion, PacientesMinDiarios, Sueldo, IdHospital)
            VALUES
            (:IdDoctor, :NombresDoctor, :ApellidosDoctor, :Especialidad, :TurnoAtencion, :PacientesMinDiarios, :Sueldo, :IdHospital)'
        );

        $stmt->execute([
            ':IdDoctor' => $data['IdDoctor'],
            ':NombresDoctor' => $data['NombresDoctor'],
            ':ApellidosDoctor' => $data['ApellidosDoctor'],
            ':Especialidad' => $data['Especialidad'],
            ':TurnoAtencion' => $data['TurnoAtencion'],
            ':PacientesMinDiarios' => $data['PacientesMinDiarios'],
            ':Sueldo' => $data['Sueldo'],
            ':IdHospital' => $data['IdHospital']
        ]);

        responder([
            'estado' => true,
            'mensaje' => 'Doctor registrado correctamente'
        ], 201);
    }

    responder([
        'estado' => false,
        'mensaje' => 'Ruta no encontrada'
    ], 404);

} catch (Throwable $e) {
    responder([
        'estado' => false,
        'mensaje' => 'Error en la API',
        'error' => $e->getMessage()
    ], 500);
}