<?php
/**
 * API framework front controller.
 * 
 * @package api-framework
 * @author  Martin Bean <martin@martinbean.co.uk>
 */

/**
 * Generic class autoloader.
 * 
 * @param string $class_name
 */
function autoload_class($class_name) {
    $directories = array(
        'classes/',
        'classes/controllers/',
        'classes/models/'
    );
    foreach ($directories as $directory) {
        $filename = $directory . $class_name . '.php';
        if (is_file($filename)) {
            require($filename);
            break;
        }
    }
}

/**
 * Register autoloader functions.
 */
spl_autoload_register('autoload_class');

/**
 * Parse the incoming request.
 */
$request = new Request();
if (isset($_SERVER['PATH_INFO'])) {     //path info contiene /alex/neri de www.google.com/alex/neri 
    $request->url_elements = explode('/', trim($_SERVER['PATH_INFO'], '/'));    //llenara un array con valores alex y neri
}
$request->method = strtoupper($_SERVER['REQUEST_METHOD']);  //esta variable contendra GET, POST, HEAD o PUT
switch ($request->method) {
    case 'GET':
        $request->parameters = $_GET;
    break;
    case 'POST':
        $request->parameters = $_POST;
    break;
    case 'PUT':
        parse_str(file_get_contents('php://input'), $request->parameters);      //lee el string y guarda los valores en parameters 
    break;
}
/**
 * Route the request.
 */
if (!empty($request->url_elements)) {
    $controller_name = ucfirst($request->url_elements[0]) . 'Controller'; //url_elements[0] es alex, pasa a ser Alex, siguiendo el ejemplo de mas arriba
    if (class_exists($controller_name)) {
        $controller = new $controller_name;
        $action_name = strtolower($request->method);    //recoge el metodo directamente de la cabecera, si se envia desde un formulario es post, si no es get
        $response_str = call_user_func_array(array($controller, $action_name), array($request));    //en este framework, el $action_name equivale a la accion a llamar en el controlador
    }
    else {
        header('HTTP/1.1 404 Not Found');
        $response_str = 'Unknown request: ' . $request->url_elements[0];
    }
}
else {
    $response_str = 'Unknown request';  //es el que sortira per defecte
}

/**
 * Send the response to the client.
 */
$response_obj = Response::create($response_str, $_SERVER['HTTP_ACCEPT']);   //http_accept Contenido de la cabecera Accept: de la petición actual, si existe, puede ser application/json, application/xml etc... 
ob_clean();
echo $response_obj->render();
//echo "<br>"."Hola";