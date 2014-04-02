<?php
/**
 * JSON response class.
 * 
 * @package api-framework
 * @author  Martin Bean <martin@martinbean.co.uk>
 */
class ResponseJson
{
    /**
     * Response data.
     *
     * @var string
     */
    protected $data;
    
    /**
     * Constructor.
     *
     * @param string $data
     */
    public function __construct($data)      //este data corresponde a response_str de index.php, que es lo que devuelva el metodo del controlador especificado
    {
        $this->data = $data;
        return $this;
    }
    
    /**
     * Render the response as JSON.
     * 
     * @return string
     */
    public function render()
    {
        header('Content-Type: application/json');
        return json_encode($this->data);    //Devuelve un string JSON codificado en caso de éxito o FALSE en caso de error.
    }
}