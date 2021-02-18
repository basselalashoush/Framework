<?php 

namespace Core\Routing;

use Core\Controller\Controller;

class Router extends Controller
{
    private $url; // Contiendra l'URL sur laquelle on souhaite se rendre
    private $routes = []; // Contiendra la liste des routes
    private $routeNames = [] ;
    private static $_instance;
    private static $_namedRoutes = [];
    private $status = [
        303=>"HTTP/1.1 303 See Other",
        301=>"HTTP/1.1 301 Moved Permanently",
        302=>"HTTP/1.1 302 Moved Temporarily",
        404=>'HTTP/1.0 404 Not Found',
        410=>'HTTP/1.0 410 Gone'];

        /**
     * On envoie l'instance du router aux controllers
     * Les methodes referer redirect 404.... seraient plus à leur place dans la logique de routing
     **/
    public function __construct(){
        $url = isset($_GET['url']) ? $_GET['url'] : '/accueil';
        $this->url = $url;
        self::$_instance = $this;
    }

    public static function getInstance(){
        if(is_null(self::$_instance)){
            self::$_instance = new Router();
        }
        return self::$_instance;
    }

    public function get($path,$callable ,$name = null)
    {    
       
     return $this->add($path,$callable,$name,'GET');
    }

    public function post($path,$callable ,$name = null)
    {
       return $this->add($path,$callable,$name,'POST');
    }

    private function add($path, $callable , $name, $method){
        $route = new Route($path,$callable,self::$_instance);
        $this->routes[$method][] = $route;
        if($name){
            $this->routeNames[$name] = $route;
            self::$_namedRoutes[$name] = $route;
        }
        return $route;
    }

    public function run()
    {
        if(isset($this->routes[$_SERVER['REQUEST_METHOD']])){

            foreach ($this->routes[$_SERVER['REQUEST_METHOD']] as $route){
                if($route->match($this->url)){
                    return $route->call();
                }
            }
        }
        // Aucune route trouvées ? url non ré-écrite, pas routes
        return $this->dispatch();
    }

    private function dispatch(){
        //dispatch  permet l'affichage des url noms nommées (routing);
        // verifier l'existence d'admin en premier paramètre
        $params = explode('.', $this->url);
        if($params[0] === 'admin'){
            if($params[1]=== 'category'){
                $params[1] = 'categories';
            }
            $controller = '\App\Controller\Admin\\'.ucfirst($params[1]).'Controller';
            $action = $params[2] ?? 'index';
        }else{
            $controller = '\App\Controller\\'.ucfirst($params[0]).'Controller';
            $action = $params[1] ?? 'index';
        }
            $file =  ROOT.DS.$controller . '.php';
            $f = str_replace('\\', '/', $file);
        if(!file_exists($f)){
            // Pas d'instanciation du controller principal, classe abstraite
            return $this->errors(404);
        }
        $controller = new $controller();
        $controller->setRouter(self::$_instance);
        if (!isset($action) OR  !method_exists($controller,$action) ){
          // Penser à créer une méthode index dans le controller parent, en cas d'oubli dans les controller enfants !!
         return $this->errors(404);
        }
        // attention les actions ont besoins quelquefois d'un tableau de parametres
        return ($controller->{$action}());

    }

    public function url($name, $params = []){
        if(!isset($this->routeNames[$name])){
            throw new RouterException('No route matches this name');
        }
        return $this->routeNames[$name]->getUrl($params);
    }

     /* Gérer les erreurs, les 404 410 301 .... via RouterException */
     protected function errors($status = null) {
        if($status) {
            header($this->status[$status]);
            $this->notFound(APP.DS.'View/');
          
       }
       //header("Location: $url");
       exit($this->status[$status]);
   }
}