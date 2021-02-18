<?php


namespace Core\Controller;


class Controller
{
    protected $viewPath;
    protected $template;
    /**
     * @param string $view
     * @param array $variables
     * construit la vue retournée dans la layout par $content
     */
    protected function render($view, $variables = [],$path = null): void
    {
        ob_start();
        extract($variables);
        if($this->viewPath == null && $path != null){
            $this->viewPath = $path;
            $this->template = 'default';
        }
        require($this->viewPath . str_replace('.', '/', $view) . '.php');
        $content = ob_get_clean();

        if (!$this->isAjax()) {
            require($this->viewPath . 'templates/' . $this->template . '.php');
        } else {
            echo $content;
        }
    }
    /**
     * virfier c'est on est passer par ajax 
     */
    protected function isAjax()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    protected static function redirect($url, $state = null)
    {
        if ($state) {
            header("HTTP/1.0" . $state);
        }

        header("Location:$url");
        exit();
    }

    protected function forbidden()
    {
        header('HTTP/1.0 403 FORBIDDEN');
        die('Acces Interdit');
    }

    protected function notFound($path = null)
    {
        header('HTTP/1.0 404 NOT FOUND');
        $notFound = ['page'=>'not found'];
        $this->render('errors.404',$notFound,$path);
        die('Page Introuvable ');
    }

    public function filter($data)
    {
        $arg = [];
        $filter = FILTER_SANITIZE_STRING;
        foreach ($data as $key => $value) {
            $filter = FILTER_SANITIZE_STRING;
            $arg[$key] = $filter;
        }
        return filter_input_array(INPUT_POST, $arg, false);
    }
}
