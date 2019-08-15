<?php

header( 'Content-Type: application/json' );

/*if ( !array_key_exists( 'HTTP_X_TOKEN', $_SERVER ) ) {
  die;
}

$url = 'http://localhost:8001';

$ch = curl_init( $url );
curl_setopt( $ch, CURLOPT_HTTPHEADER, [
  "X-Token: {$_SERVER['HTTP_X_TOKEN']}"
] );
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

$ret = curl_exec( $ch );

if ( $ret !== 'true' ) {
  die;
}*/

// Definimos los recursos disponibles.
$allowedResourceTypes = [
  'books',
  'authors',
  'genres'
];

// Validamos que el recurso este disponible.
$resourceType = $_GET['resource_type'];

if( !in_array( $resourceType, $allowedResourceTypes ) ) {
  http_response_code( 400 );
  
  header( 'Status-Code: 400' );
  echo json_encode([
    'error' => "Resource type '$resourceType' is unknown",
  ]);

  die;
}

// Defino los recursos
$books = [
  1 => [
    'titulo' => 'Lo que el viento se llevo',
    'autor_id' => 2,
    'genero_id' => 2,
  ],
  2 => [
    'titulo' => 'La Iliada',
    'autor_id' => 1,
    'genero_id' => 1,
  ],
  3 => [
    'titulo' => 'La Odisea',
    'autor_id' => 1,
    'genero_id' => 1,
  ],
];

// Levantamos el id del recurso buscado
$resourceId = array_key_exists('resource_id', $_GET) ? $_GET['resource_id'] : '';
$method = $_SERVER['REQUEST_METHOD'];

switch ( strtoupper( $method) ) {
  case 'GET':
    if ( "books" !== $resourceType ) {
      header( 'Status-Code: 404 ');

      echo json_encode([
        'error' => $resourceType.' not yet implemented :(',
      ]);

      die;
    }

    if ( !empty( $resourceId ) ) {
      if ( array_key_exists( $resourceId, $books ) ) {
        echo json_encode( $books[ $resourceId ] );
      } else {
        http_response_code( 404 );
        
        header( 'Status-Code: 404' );
        
        echo json_encode([
          'error' => 'Book '.$resourceId.' not found :(',
        ]);
      }
    } else {
      echo json_encode( $books );
    }

    die;
    
    break;
  case "POST":
    // Tomamos la entrada "cruda"
    $json = file_get_contents('php://input');
    // Transformamos el json recibido a un nuevo elemento del arrey
    $books[] = json_decode( $json, true );
    // Emitimos hacia la salida la ultima clave del arrglo de la
    echo array_keys( $books )[ count($books) -1 ];
    
		break;
  case "PUT":
    // Validamos que el recurso buscado exista
    if( !empty($resourceId) && array_key_exists( $resourceId, $books ) ) {
      // Tomamos la entrada cruda
      $json = file_get_contents('php://input');

      // Transformamos el json recibido a un nuevo elemento del array
      $books[ $resourceId ] = json_decode( $json, true );

      // Retornamos la colección modificado en formato json
      echo json_encode( $books );
    }
    break;
  case "DELETE":
    // Validamos que el recurso exista
    if( !empty($resourceId) && array_key_exists( $resourceId, $books ) ) {
      // Eliminamos el recurso
      unset ( $books[ $resourceId ] );

      echo json_encode( $books );
    }
    break;
  default:
    header( 'Status-Code: 404' );

    echo json_encode([
      'error' => $method.' not yet implemented :(',
    ]);
    break;
}