<?php

class FAQControllerCore extends FrontController
{
    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        $this->setTemplate(_PS_THEME_DIR_.'faq.tpl');
    }

    public function init()
    {
        parent::init();
    }
}
