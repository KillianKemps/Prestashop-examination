<?php
class Wsf extends Module
{
  public function __construct()
  {
    $this->name = 'wsf - lookbook';
    $this->tab = 'front_office_features';
    $this->version = '0.1';
    $this->author = 'Killian Kemps';
    $this->need_instance = 0;

    parent::__construct();

    $this->displayName = $this->l('Web School Factory - lookbook');
    $this->description = $this->l('Module to display lookbook');
    $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
  }

   public function install()
  {
    return (parent::install() && $this->registerHook('displayProductButtons') && $this->registerHook('displayHome') && $this->registerHook('actionObjectProductUpdateAfter'));
  }

  public function getContent()
  {
    $this->_postProcess();

    $action = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
    $token = Tools::getAdminTokenLite('AdminModules');
    $form = '
      <form action="'. $action .'&token='.$token.'" method="post">
        <textarea name="SWF_URL" id="" cols="30" rows="10">'. base64_decode(Configuration::get('SWF_URL')) .'</textarea>
        <input type="submit" value="Valider" name="submitWsf" />
      </form>
    ';
    return $form;
  }

  protected function _postProcess()
  {
    if (Tools::isSubmit('submitWsf')) {
      Configuration::updateValue('SWF_URL', base64_encode(Tools::getValue('SWF_URL')));
    }
  }

  public function uninstall()
  {
    return (parent::uninstall());
  }

  public function hookDisplayProductButtons($params)
  {
    $product = $params['product'];
    $data = base64_decode(Configuration::get('SWF_URL'));
    $this->smarty->assign(
      array(
        'data' => $data
      )
    );
    return $this->display(__FILE__, 'displayProductButtons.tpl');
  }

  public function hookDisplayHome($params)
  {
    $data = base64_decode(Configuration::get('SWF_URL'));
    $this->smarty->assign(
      array(
        'data' => $data
      )
    );
    return $this->display(__FILE__, 'displayHome.tpl');
  }

  public function hookActionObjectProductUpdateAfter($params)
  {
    $product = $params['object'];
    StockAvailable::setQuantity($product->id, 0, $product->getQuantity($product->id) + 10);
  }

  public function hookDisplayHeader($params)
  {
    $this->context->controller->addCSS(($this->_path).'css/wsf.css', 'all');
    $this->context->controller->addJS(($this->_path).'js/wsf.js', 'all');
  }
}
