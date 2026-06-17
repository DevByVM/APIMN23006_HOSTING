<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');

if ($basePath !== '' && $basePath !== '/') {
    $app->setBasePath($basePath);
}

$app->addBodyParsingMiddleware();

function agregarCors(Response $response): Response
{
    return $response
        ->withHeader('Content-Type', 'application/json; charset=utf-8')
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
}

function responderJson(Response $response, array $data, int $statusCode = 200): Response
{
    $response->getBody()->write(
        json_encode($data, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE)
    );

    return agregarCors($response)->withStatus($statusCode);
}

function obtenerConexion(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $host = getenv('DB_HOST') ?: 'localhost';
        $port = getenv('DB_PORT') ?: '3306';
        $dbname = getenv('DB_NAME') ?: 'bd_p3_clave2';
        $user = getenv('DB_USER') ?: 'api_p3';
        $password = getenv('DB_PASSWORD') ?: 'ApiP3_2026';

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

$app->options('/{routes:.+}', function (Request $request, Response $response): Response {
    return agregarCors($response);
});

$app->get('/', function (Request $request, Response $response): Response {
    return responderJson($response, [
        'estado' => true,
        'mensaje' => 'API MN23006 funcionando correctamente'
    ]);
});

$app->get('/hospitales', function (Request $request, Response $response): Response {
    $stmt = obtenerConexion()->query('SELECT * FROM hospitales');
    $hospitales = $stmt->fetchAll();

    return responderJson($response, [
        'estado' => true,
        'mensaje' => 'Hospitales obtenidos correctamente',
        'data' => $hospitales
    ]);
});

$app->get('/hospitales/{id}', function (Request $request, Response $response, array $args): Response {
    $idHospital = $args['id'];

    $stmt = obtenerConexion()->prepare(
        'SELECT * FROM hospitales WHERE IdHospital = :IdHospital'
    );

    $stmt->execute([
        ':IdHospital' => $idHospital
    ]);

    $hospital = $stmt->fetch();

    if (!$hospital) {
        return responderJson($response, [
            'estado' => false,
            'mensaje' => 'Hospital no encontrado'
        ], 404);
    }

    return responderJson($response, [
        'estado' => true,
        'mensaje' => 'Hospital obtenido correctamente',
        'data' => $hospital
    ]);
});

$app->post('/hospitales', function (Request $request, Response $response): Response {
    $data = $request->getParsedBody();

    if (!is_array($data)) {
        return responderJson($response, [
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

    return responderJson($response, [
        'estado' => true,
        'mensaje' => 'Hospital registrado correctamente'
    ], 201);
});

$app->get('/doctores', function (Request $request, Response $response): Response {
    $stmt = obtenerConexion()->query('SELECT * FROM doctores');
    $doctores = $stmt->fetchAll();

    return responderJson($response, [
        'estado' => true,
        'mensaje' => 'Doctores obtenidos correctamente',
        'data' => $doctores
    ]);
});

$app->post('/doctores', function (Request $request, Response $response): Response {
    $data = $request->getParsedBody();

    if (!is_array($data)) {
        return responderJson($response, [
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

    return responderJson($response, [
        'estado' => true,
        'mensaje' => 'Doctor registrado correctamente'
    ], 201);
});

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$errorMiddleware->setDefaultErrorHandler(
    function (
        Request $request,
        Throwable $exception,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails
    ) use ($app): Response {
        $response = $app->getResponseFactory()->createResponse();

        return responderJson($response, [
            'estado' => false,
            'mensaje' => 'Error en la API',
            'error' => $exception->getMessage()
        ], 500);
    }
);

$app->run();
