<?php

class LookbookControllerCore extends FrontController
{
    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        $this->setTemplate(_PS_THEME_DIR_.'lookbook.tpl');
    }

    public function init()
    {
        parent::init();

        $id_product = 8;

        $lang = $this->context->cookie->id_lang;

        $children = Pack::getItems($id_product, $lang);

        $pack = Pack::getItemTable($id_product, $lang);

        $p = new Product($id_product, true, $lang);

        var_dump($p);
        //d($children);
        //var_dump($children);

        $this->context->smarty->assign(array('look' => $children));
        $this->context->smarty->assign(array('pack' => $pack));
    }
}
