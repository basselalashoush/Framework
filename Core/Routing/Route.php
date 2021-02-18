<?php 

namespace Core\Routing;

class Route
{
    private $path;
    private $callable;
    private $matches = [];
    private $params = [];
    private $_route;

    public function __construct($path , $callable,$_route)
    {
        // enlever les slashs initiaux et finaux
        $this->path = trim($path, '/');
        $this->callable = $callable;
        $this->_route = $_route;
    }

    public function match($url){
        // enlever les slashs initiaux et finaux
        $url = trim($url , '/');
        $path = preg_replace_callback('#:([\w]+)#', [$this , 'paramMatch'],$this->path);
        $regex = "#^$path$#i";
        if(!preg_match($regex,$url,$matches)){
            return false;
        }
        array_shift($matches);
        $this->matches = $matches;
        return true;
    }

    public function paramMatch($match){
        if(isset($this->params[$match[1]])){
            return '(' . $this->params[$match[1]] . ')';
        }
        return '([^/]+)';
    }

    public function with($param,$regex){
        $this->params[$param] = str_replace('(','(?:',$regex) ;
        return $this;// On retourne tjrs l'objet pour enchainer les arguments
    }
/**
 * Gère un callback qui sera une chaine de caractère. Par exemple,
 *  on pourra faire appel à un controller en mettant courses.show 
 *  qui fera appel à la classe CoursesController et à la méthode show().
 */
    public function call(){
        if(is_string($this->callable)){
            $param = explode ('.',$this->callable);
            if($param[0] === 'admin'){
                if($param[1]=== 'category'){
                    $param[1] = 'categories';
                }
                $controller = '\App\Controller\Admin\\'.ucfirst($param[1]).'Controller';
                $action = $param[2];
            }else{
                $controller = '\App\Controller\\'.ucfirst($param[0]).'Controller';
                $action = $param[1];
            }
            $file =  ROOT.DS.$controller . '.php';
          
            $f = str_replace('\\', '/', $file);
             if(!file_exists($f)){
                return $this->_route->errors(404);
        }
        if (!isset($action) OR  !method_exists($controller,$action) ){
            // Penser à créer une méthode index dans le controller parent, en cas d'oubli dans les controller enfants !!
           return $this->_route->errors(404);
          }
            $controller = new $controller();
            return call_user_func_array([$controller, $action], $this->matches);
        }
        return call_user_func_array($this->callable, $this->matches);
    }
/**
 * une méthode qui permettra de générer une url en passant les paramètres.
 */
    public function getUrl($params){
        $path = $this->path;
        if(!empty($params)){
            foreach($params as $k => $v){
                $path = str_replace(":$k", $v, $path);
            }
        }
        return $path;
    }
}