<?php
class FrontController extends FrontControllerCore
{
    public function setMedia()
    {
        parent::setMedia();   
        $this->addCSS(_THEME_CSS_DIR_.'faq.css');  
    }
}
