<?php
namespace Core\Html;

use PhpParser\Node\Stmt\Label;

class BootstrapForm extends Form{
    public $surround = 'div';
    public $surroundClass = 'form-group';
    /**
     * @param $html string Code html à entourer
     * @return string
     */
    protected function surround($html){
        return "<{$this->surround} class={$this->surroundClass}>{$html}</{$this->surround}>";
    }

    /**
     * @param $name string
     * @param $label
     * @param array $options
     * @param null $em
     * @param null $value
     * @param string|null $label_class c'est le classe de la balise label 
     * @return string
     */
    public function input($name, $label, $options = [],$em = null , $value = null,$label_class = null){
        $type = isset($options['type']) ? $options['type'] : 'text';
        $placeholder = isset($options['placeholder']) ? $options['placeholder'] : '';
        $class = isset($options['class']) ? $options['class'].' form-control' : 'form-control';
        $label = '<label>' . $label . '</label>';
        if($label_class){
            $label = '<label class="'.$label_class.'">' . $label . '</label>';
        }
        if($type === 'textarea'){
            $input = '<textarea name="' . $name . '" class="'.$class.'">' . $this->getValue($name) . '</textarea>';
        } else{
            $input = '<input class = "'.$class.'" type="' . $type . '" placeholder="' .$placeholder .'" name="' . $name . '" value="' . $this->getValue($name) .'" >';
        }
        if($em){
            if(substr($em,1,1)=== 'i'){
                $input .= "<span class='eye'>$em</span>";
            }elseif(substr($em,1,4)=== 'span'){
            $input = "$em".$input;
            }else{
                $input .= "<span class='remember'><em class='help'>$em</em></span>";
            }
           
        }
        return $this->surround($label . $input,$options);
    }
    /**
     * @return select personalisé
     * @param string $name
     * @param string|null $label
     * @param array $options
     * @param string|null $cls class name 
     * @param string|null $data data-name   
     */
    public function select($name, $label = null, $options, $cls = null, $data = null){
        if($label){
            $label = '<label>' . $label . '</label>';
        }
        $class = "form-control";
        if($cls){
            $class  .= "  $cls";
        }
        $input = '<select class="'.$class.'" name="' . $name . '">';
        if($data){
            $input = '<select class="'.$class.'" name="' . $name . '" data-id = "' . $data . '">';
        }
        
        foreach($options as $k => $v){
            $attributes = '';
            if($k == $this->getValue($name)){
                $attributes = ' selected';
            }
            $input .= "<option value='$k' $attributes>$v</option>";
        }
        $input .= '</select>';
        return $this->surround($label . $input,$options);
    }

    /**
     * @param string|null $type
     * @param string|null $class
     * @param string|null $button_name
     * @param string|null $id
     * @return string|null
     */
    public function submit($type = null ,$class = null,$button_name = null,$id = null ,$href = null){
        $type = isset($type) ? $type : "submit";
        $class = isset($class) ? $class : "primary";
        $button_name = isset($button_name) ? $button_name : "Envoyer";
        $button = "<button type=\"{$type}\"  class=\"btn {$class}\"> $button_name</button>";
        if($id){
            $button = "<button type=\"{$type}\"  class=\"btn {$class}\" id=\"$id\"> $button_name</button>";
        }
        if($href){
            $button = "<button data-link={$href} type=\"{$type}\"  class=\"btn {$class}\" id=\"$id\"> $button_name</button>";
        }
        return $button;
    }
}