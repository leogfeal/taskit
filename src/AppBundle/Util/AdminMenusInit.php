<?php

namespace AppBundle\Util;

class AdminMenusInit
{
    private $options;
    private $title;
    private $id;
    
    public function __construct($title, $id) {
        $this->options = array();
        $this->title = $title;
        $this->id = $id;
    }
    
    public function addMenu($title, $icon, $route, array $routeParameters = null , $target = null)
    {        
        $this->options[] = array(
            'title' => $title,
            'icon' => $icon,
            'route' => $route,
            'routeParameters' => $routeParameters,
            'target' => $target
            );
        
        return $this;
    }
    
    public function addDivider()
    {
        $this->options[] = 'divider';
        return $this;
    }

    public function addDisable($title, $icon)
    {
        $this->options[] = array(
            'title' => $title,
            'icon' => $icon,
            'disabled' => true,
        );
    }

    public function addModal($title, $icon, $class)
    {
        $this->options[] = array(
            'title' => $title,
            'icon' => $icon,
            'modal' => true,
            'class' => $class
        );
    }
    
    public function getOptions()
    {
        return $this->options;
    }
    
    public function getTitle()
    {
        return $this->title;
    }
    
    public function getId()
    {
        return $this->id;
    }
}
?>
