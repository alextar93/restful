<?php
/**
 * Sample news controller.
 * 
 * @package api-framework
 * @author  Alex Neri <alex_n_93@hotmail.com>
 */
//require 'config.php';

class UserController extends AbstractController
{
   
    /**
     * GET method.
     * 
     * @param  Request $request
     * @return string
     */
    protected $bd =null;
    
    function __construct(){
        $this->bd=  usersPDO::singleton();
    }

    public function get($request)   //esta funcion es la de mostrar usuarios
    {
        
        $users = $this->readUsers();
        switch (count($request->url_elements)) {
            case 1: //sin filtrar
                return $users;
            break;
            case 2: //para filtrar por id
                $id = $request->url_elements[1];    //agafo el parametre, que será el id
                $users = $this->readUsers($id);
                return $users;
           
            break;
        }
    }
    
    /**
     * POST action.
     *
     * @param  $request
     * @return null
     */
    public function post($request)
    {
        $nom_usuario=$request->parameters['nom'];
        $pass_usuario=$request->parameters['contrasenya'];
        //FUNCION LOGIN
        if($request->url_elements[1]=='login'){     //funcionara si ponesmos la url http://localhost/restful/user/login
            $result=$this->searchUser($nom_usuario, $pass_usuario);
            return $result;
            //llamar a una funcion que busque usuario por nombre y pass, y si existe mostrar bienvenida
        }else{  //FUNCION PARA CREAR USUARIO
            //Tengo que hacer un formulario, en el que tendra dos campos, nom y contrasenya, y esos valores los recojo con $request->parameters
            //de momento lo pruebo con la aplicacion de google chrome 'advanced rest client'
            /*echo $request->parameters['nom'];
            echo $request->parameters['contrasenya'];*/
            //ejecutar addusers
            $result=$this->addUsers($nom_usuario, $pass_usuario);
            return $result;            
        }        
    }
    
    public function put($request){
        $id_usuario=$request->url_elements[2];
        $nom_usuario=$request->parameters['nom'];
        $pass_usuario=$request->parameters['contrasenya'];
        
        if($request->url_elements[1]=='actualitzarNom'){    //la url es tal que asi http://localhost/restful/user/actualitzarNom/4, siendo 4 el id del usuario a modificar
            $result=$this->modifyUser($id_usuario, $nom_usuario, $pass_usuario);
            return $result;            
        }       
    }
    
    public function delete($request){
        $id_usuario=$request->url_elements[2];
        
        if($request->url_elements[1]=='esborrarUsuari'){    //la url es tal que asi http://localhost/restful/user/esborrarUsuari/4, siendo 4 el id del usuario a eliminar
            $result=$this->deleteUser($id_usuario);
            return $result;            
        } 
    }

        protected function readUsers($id = NULL)   //no es obligatorio pasar el id
    {
        try{
            if(!empty($id)){
                $sql="SELECT * FROM users WHERE id = ?";
		$query=$this->bd->prepare($sql);
                //bind param
                $query->bindParam(1,$id);	
                //ejecucion del $query
                $query->execute();
                $res=$query->fetchAll(PDO::FETCH_OBJ);
            
            }else{
                $conexio = $this->bd->query('select * from users');
                $res = $conexio->fetchAll(PDO::FETCH_OBJ); //mete todos los datos en un array asociativo, con los campos de la base de datos
                /*var_dump($users);
                die;*/
            }            

        } catch (PDOException $e){
            echo 'ERROR: '.$e->getMessage();
        }
        return $res;
    }
    
    protected function addUsers($name, $pass){
        try{
            $sql="insert into users(nom, password) VALUES (?, ?)";
		$query=$this->bd->prepare($sql);
                $query->bindParam(1,$name);
                $query->bindParam(2,$pass);
                $query->execute();
            
            $res=$query->fetchAll();
            if($query->rowCount()==1){
                $result = array('msg'=>'Usuari afegit');
            }else{
                $result = array('msg'=>'Usuari no afegit');
            }               
            
        } catch (PDOException $e){
            echo 'ERROR: '.$e->getMessage();            
        }
        return $result;
    }
    
    protected function searchUser($name, $pass){
       try{        
        $sql="SELECT * FROM users WHERE nom = ? AND password = ?";
		$query=$this->bd->prepare($sql);
                $query->bindParam(1,$name);
                $query->bindParam(2,$pass);
                $query->execute();
                
            if($query->rowCount()==1){
                $result = array('msg'=>'Benvingut '.$name);
            }else{
                $result = array('msg'=>'Usuari '.$name.' no existeix');
            }
                
    }catch (PDOException $e){
            echo 'ERROR: '.$e->getMessage();            
    }    
    return $result;    
  }
  
  protected function modifyUser($id, $name, $pass){
      try{
            $sql="UPDATE users SET nom=?, password=? WHERE id=?";
		$query=$this->bd->prepare($sql);
                $query->bindParam(1,$name);
                $query->bindParam(2,$pass);
                $query->bindParam(3,$id);
                $query->execute();
            
            if($query->rowCount()==1){
                $result = array('msg'=>'Usuari modificat');
            }else{
                $result = array('msg'=>'Usuari no modificat');
            }            
        } catch (PDOException $e){
            echo 'ERROR: '.$e->getMessage();            
        }
        return $result;
  }
  
  protected function deleteUser($id){
      try{
            $sql="DELETE FROM users WHERE id = ?";
		$query=$this->bd->prepare($sql);
                $query->bindParam(1,$id);
                $query->execute();
            
            if($query->rowCount()==1){
                $result = array('msg'=>'Usuari eliminat');
            }else{
                $result = array('msg'=>'Usuari no eliminat');
            }            
        } catch (PDOException $e){
            echo 'ERROR: '.$e->getMessage();            
        }
        return $result;
  }

}