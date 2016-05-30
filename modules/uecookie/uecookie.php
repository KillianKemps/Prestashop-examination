<?php
class uecookie extends Module {
    function __construct() {
        $this->name = 'uecookie';
        $this->tab = 'Blocks';
        $this->author = 'MyPresta.eu';
        $this->version = '1.6.7';
        $this->module_key = '98954d996259885532adabb50f129a9d';
        $this->dir = '/modules/uecookie/';
        $this->setup = $this->getconf();
        $this->psver = $this->psversion();
        parent::__construct();
        $this->displayName = $this->l('European Union Cookies Law');
        $this->description = $this->l('This module displays a nice information about Cookies in your shop');
        $this->mkey = "freelicense";
        if(@file_exists('../modules/'.$this->name.'/key.php'))
            @require_once ('../modules/'.$this->name.'/key.php');
        else
            if(@file_exists(dirname(__file__).$this->name.'/key.php'))
                @require_once (dirname(__file__).$this->name.'/key.php');
            else
                if(@file_exists('modules/'.$this->name.'/key.php'))
                    @require_once ('modules/'.$this->name.'/key.php');
        $this->checkforupdates();
    }

    function checkforupdates($display_msg=0){
        // version 2 of updates checker //
        if (defined('_PS_ADMIN_DIR_')){
            if (Tools::isSubmit('submit_settings_updates')){
                Configuration::updateValue('mypresta_updates',Tools::getValue('mypresta_updates'));
            }
            if (Configuration::get('mypresta_updates')!=0){
                if (Configuration::get('update_'.$this->name) < (date("U")-259200)){
                    $actual_version = uecookieUpdate::verify($this->name, $this->mkey, $this->version);
                }
                if (uecookieUpdate::version($this->version) < uecookieUpdate::version(Configuration::get('updatev_'.$this->name))){
                    $this->warning=$this->l('New version available, check http://MyPresta.eu for more informations');
                }
            }
            if ($display_msg==1){
                if (uecookieUpdate::version($this->version) < uecookieUpdate::version(uecookieUpdate::verify($this->name, $this->mkey, $this->version))){
                    return "<span style='color:red; font-weight:bold; font-size:16px;'>".$this->l('New version available!')."</span>";
                } else {
                    return "<span style='color:green; font-weight:bold; font-size:16px;'>".$this->l('Module is up to date!')."</span>";
                }
            }
        }
    }

    function install() {
        if($this->psversion() == 5 || $this->psversion() == 6 || $this->psversion() == 7) {
            if (parent::install() == false 
            OR !$this->registerhook('header') 
            OR !$this->registerHook('footer') 
            OR !$this->registerHook('top') 
            OR !Configuration::updateValue('update_'.$this->name, '0') 
            OR !Configuration::updateValue('uecookie_opacity', '0.5') 
            OR !Configuration::updateValue('uecookie_position', '1') 
            OR !Configuration::updateValue('uecookie_bg', '000000') 
            OR !Configuration::updateValue('uecookie_shadow', 'FFFFFF') 
            OR !Configuration::updateValue('uecookie_text', array($this->context->language->id => $this->l('This shop uses cookies and other technologies so that we can improve your experience on our sites.')))){
                return false;
            } else {                
                return true;
            }
        } else {
            if(parent::install() == false 
            OR !$this->registerHook('footer') 
            OR !$this->registerHook('top')){
                return false;
            } else {
                return true;
            }
        }
    }


    public function psversion($part = 1) {
        $version = _PS_VERSION_;
        $exp = $explode = explode(".", $version);
        if($part == 1)
            return $exp[1];
        if($part == 2)
            return $exp[2];
        if($part == 3)
            return $exp[3];
        if($part == 4)
            return $exp[3];
    }

    public function getconf() {
        $var = new stdClass();
        $var->uecookie_position = Configuration::get('uecookie_position');
        $var->uecookie_bg = Configuration::get('uecookie_bg');
        $var->uecookie_shadow = Configuration::get('uecookie_shadow');
        $var->uecookie_opacity = Configuration::get('uecookie_opacity');
        $var->uecookie_closex = Configuration::get('uecookie_closex');
        $var->uecookie_close_txt = Configuration::get('uecookie_close_txt');
        $var->uecookie_close_bg = Configuration::get('uecookie_close_bg');
        return $var;
    }

    public function return_dir() {
        return _MODULE_DIR_;
    }

    public function getContent() {
        $output = "";

        if(Tools::isSubmit('module_settings')) {
            Configuration::deleteByName('uecookie_text');
            $message_trads = array();
            Configuration::updateValue('uecookie_text', $_POST['uecookie_text'], true);
            Configuration::updateValue('uecookie_bg', $_POST['uecookie_bg'], true);
            Configuration::updateValue('uecookie_closex', $_POST['uecookie_closex']);
            Configuration::updateValue('uecookie_usex', $_POST['uecookie_usex']);
            Configuration::updateValue('uecookie_close_bg', $_POST['uecookie_close_bg']);
            Configuration::updateValue('uecookie_close_txt', $_POST['uecookie_close_txt']);
            Configuration::updateValue('uecookie_shadow', $_POST['uecookie_shadow'], true);
            Configuration::updateValue('uecookie_position', Tools::getValue('uecookie_position'), true);
            Configuration::updateValue('uecookie_opacity', $_POST['uecookie_opacity'], true);
            Configuration::updateValue('uecookie_lib', Tools::getValue('uecookie_lib',0), true);
            
        }

        return $output.$this->displayForm();
    }

    public function displayForm() {
        $languages = Language::getLanguages(false);
        $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
        $var = $this->getconf();
        global $cookie;
        $iso = Language::getIsoById((int)($cookie->id_lang));
        $isoTinyMCE = (file_exists(_PS_ROOT_DIR_.'/js/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en');
        $ad = dirname($_SERVER["PHP_SELF"]);

        if($this->psversion() == 4 || $this->psversion() == 5) {
            $form = '
				<script type="text/javascript">
				var iso = \''.$isoTinyMCE.'\' ;
				var pathCSS = \''._THEME_CSS_DIR_.'\' ;
				var ad = \''.$ad.'\' ;
				</script>
				
				<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tiny_mce/tiny_mce.js"></script>
				<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tinymce.inc.js"></script>
				<script type="text/javascript">
				$(document).ready(function(){
				tinySetup({
					editor_selector :"rte",
					theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull|cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,undo,redo",
					theme_advanced_buttons2 : "link,unlink,anchor,image,cleanup,code,|,forecolor,backcolor,|,hr,removeformat,visualaid,|,charmap,media,|,ltr,rtl,|,fullscreen",
					theme_advanced_buttons3 : "",
					theme_advanced_buttons4 : ""
					});
				});
				</script>';
        }
        if($this->psversion() == 6 || $this->psversion() == 7) {
            $form = '                <style>
                .language_flags {display:none;}
                </style>
                    <script type="text/javascript">
        				var iso = \''.$isoTinyMCE.'\' ;
        				var pathCSS = \''._THEME_CSS_DIR_.'\' ;
        				var ad = \''.$ad.'\' ;
    				</script>
                    <script type="text/javascript" src="'.__PS_BASE_URI__.'js/tiny_mce/tiny_mce.js"></script>
                    <script type="text/javascript" src="../modules/uecookie/tinymce16.inc.js"></script>
                    ';
        }


        if($this->psversion() == 3) {
            $form = '<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tinymce/jscripts/tiny_mce/jquery.tinymce.js"></script>
		<script type="text/javascript">
		function tinyMCEInit(element)
		{
			$().ready(function() {
				$(element).tinymce({
					// Location of TinyMCE script
					script_url : "'.__PS_BASE_URI__.'js/tinymce/jscripts/tiny_mce/tiny_mce.js",
					// General options
					theme : "advanced",
					plugins : "safari,pagebreak,style,layer,table,advimage,advlink,inlinepopups,media,searchreplace,contextmenu,paste,directionality,fullscreen",
					// Theme options
					theme_advanced_buttons1 : "newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
					theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,,|,forecolor,backcolor",
					theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,media,|,ltr,rtl,|,fullscreen",
					theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,pagebreak",
					theme_advanced_toolbar_location : "top",
					theme_advanced_toolbar_align : "left",
					theme_advanced_statusbar_location : "bottom",
					theme_advanced_resizing : false,
					content_css : "/presta2/themes/prestashop/css/global.css",
					width: "582",
					height: "auto",
					font_size_style_values : "8pt, 10pt, 12pt, 14pt, 18pt, 24pt, 36pt",
					// Drop lists for link/image/media/template dialogs
					template_external_list_url : "lists/template_list.js",
					external_link_list_url : "lists/link_list.js",
					external_image_list_url : "lists/image_list.js",
					media_external_list_url : "lists/media_list.js",
					elements : "nourlconvert",
					convert_urls : false,
					language : "en"
				});
			});
		}
		tinyMCEInit("textarea.rte");
		</script>';
        }
        $position_top = "";
        $position_bottom = "";

        if(Configuration::get('uecookie_position') == 1) {
            $position_top = "checked=\"yes\"";
        }
        if(Configuration::get('uecookie_position') == 2) {
            $position_bottom = "checked=\"yes\"";
        }

        $values = Configuration::getInt('uecookie_text');
        $content = "";
        foreach ($languages as $language)
            $content .= '					
			<div id="ccont_'.$language['id_lang'].'" style="margin-bottom:30px;">
                <h2>'.$language['name'].'</h2>
				<textarea class="rte rtepro" id="uecookie_text_'.$language['id_lang'].'" name="uecookie_text['.$language['id_lang'].']" style="width:500px; height:300px;">'.(isset($values[$language['id_lang']]) ? $values[$language['id_lang']] : '').'</textarea>
			</div>';

        $form .= '
            <script type="text/javascript" src="../modules/uecookie/jscolor/jscolor.js"></script>
			<div style="diplay:block; clear:both; margin-bottom:20px;">
				<iframe src="//apps.facepages.eu/somestuff/whatsgoingon.html" width="100%" height="150" border="0" style="border:none;"></iframe>
			</div>
			<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
				<fieldset style="position:relative;">
					<legend>'.$this->l('UE cookie law').'</legend>
					<div style="margin-bottom:20px; display:block; clear:both; text-align:center; overflow:hidden;">
		                <div style="display:block; clear:both; margin-bottom:20px;">
							<label>'.$this->l('UE cookie law text').':</label>
							<div class="margin-form">'.$content.'
							</div>	
		                </div>
	                </div>
                    <label>'.$this->l('Position').'</label>
					<div class="margin-form">
						<input type="radio"  name="uecookie_position" value="1" '.$position_top.'/>'.$this->l('top').'
                        <input type="radio"  name="uecookie_position" value="2" '.$position_bottom.'/>'.$this->l('bottom').'
					</div>
                    <label>'.$this->l('Background color').'</label>
					<div class="margin-form">
						<input type="text" class="color" name="uecookie_bg" value="'.Configuration::get('uecookie_bg').'"/>
					</div>
                    <label>'.$this->l('Shadow color').'</label>             
					<div class="margin-form">
						<input type="text" class="color" name="uecookie_shadow" value="'.Configuration::get('uecookie_shadow').'"/>
					</div>
                    <label>'.$this->l('Use [X] instead [close] button').'</label>
					<div class="margin-form">
    					<input type="radio"  name="uecookie_usex" value="0" '.(Configuration::get('uecookie_usex') != 1 ? 'checked':'').'/>'.$this->l('No').'
                        <input type="radio"  name="uecookie_usex" value="1" '.(Configuration::get('uecookie_usex') == 1 ? 'checked':'').'/>'.$this->l('Yes').'
					</div>
                    <label>'.$this->l('Color of close [X] button').'</label>             
					<div class="margin-form">
						<input type="text" class="color" name="uecookie_closex" value="'.Configuration::get('uecookie_closex').'"/>
					</div>

                    <label>'.$this->l('Background color of [close] button').'</label>             
					<div class="margin-form">
						<input type="text" class="color" name="uecookie_close_bg" value="'.Configuration::get('uecookie_close_bg').'"/>
					</div>

                    <label>'.$this->l('Text color of [close] button').'</label>             
					<div class="margin-form">
						<input type="text" class="color" name="uecookie_close_txt" value="'.Configuration::get('uecookie_close_txt').'"/>
					</div>
                                        
                    <label>'.$this->l('Opacity').'</label>             
					<div class="margin-form">
						<input type="text" name="uecookie_opacity" value="'.Configuration::get('uecookie_opacity').'"/>
                        '.$this->l('for example: 0.5').'
					</div>
                    <label>'.$this->l('Include library to').'</label>
					<div class="margin-form">
    					<input type="radio"  name="uecookie_lib" value="0" '.(Configuration::get('uecookie_lib') != 1 ? 'checked':'').'/>'.$this->l('footer').'
                        <input type="radio"  name="uecookie_lib" value="1" '.(Configuration::get('uecookie_lib') == 1 ? 'checked':'').'/>'.$this->l('header').'
                        <div class="bootstrap">
                    		<div class="alert alert-info">
                    			'.$this->l('Some templates uses own hook to build footer section. In cases like this please use "header" option to include ue notification to front office').'
                    		</div>
                    	</div>
					</div>
                    
	                <div style="margin-top:20px; clear:both; overflow:hidden; display:block; text-align:center">
	                	<input type="submit" name="module_settings" class="button" value="'.$this->l('save').'">
                    <iframe src="//apps.facepages.eu/somestuff/uecookie.html" width="100%" height="150" border="0" style="border:none;"></iframe>
	                </div>
      			</fieldset>
   			</form>
		';
        
        $form.='
                <div id="module_block_settings">
                    <fieldset id="fieldset_module_block_settings">
                        <legend style="display:inline-block;"><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('MyPresta updates').'</legend>
                        <form action="'.$_SERVER['REQUEST_URI'].'" method="post">
                            <label>'.$this->l('Check updates').'</label>
                            <div class="margin-form">'.(Tools::isSubmit('submit_settings_updates_now') ? $this->checkforupdates(1):'').'
                                <input style="margin: 0px; top: -3px; position: relative;" type="submit" name="submit_settings_updates_now" value="'.$this->l('Check now').'" class="button" />
                            </div>
                            <label>'.$this->l('Updates notifications').'</label>
                            <div class="margin-form">
                                <select name="mypresta_updates">
                                    <option value="-">'.$this->l('-- select --').'</option>
                                    <option value="1" '.((int)(Configuration::get('mypresta_updates')==1) ? 'selected="selected"':'').'>'.$this->l('Enable').'</option>
                                    <option value="0" '.((int)(Configuration::get('mypresta_updates')==0) ? 'selected="selected"':'').'>'.$this->l('Disable').'</option>
                                </select>
                                <p class="clear">'.$this->l('Turn this option on if you want to check MyPresta.eu for module updates automatically. This option will display notification about new versions of this addon.').'</p>
                            </div>
                            <center><input type="submit" name="submit_settings_updates" value="'.$this->l('Save Settings').'" class="button" /></center>
                        </form>
                    </fieldset>
                </div>';
        return ''."<script>/*<![CDATA[*/window.zEmbed||function(e,t){var n,o,d,i,s,a=[],r=document.createElement(\"iframe\");window.zEmbed=function(){a.push(arguments)},window.zE=window.zE||window.zEmbed,r.src=\"javascript:false\",r.title=\"\",r.role=\"presentation\",(r.frameElement||r).style.cssText=\"display: none\",d=document.getElementsByTagName(\"script\"),d=d[d.length-1],d.parentNode.insertBefore(r,d),i=r.contentWindow,s=i.document;try{o=s}catch(c){n=document.domain,r.src='javascript:var d=document.open();d.domain=\"'+n+'\";void(0);',o=s}o.open()._l=function(){var o=this.createElement(\"script\");n&&(this.domain=n),o.id=\"js-iframe-async\",o.src=e,this.t=+new Date,this.zendeskHost=t,this.zEQueue=a,this.body.appendChild(o)},o.write('<body onload=\"document._l();\">'),o.close()}(\"//assets.zendesk.com/embeddable_framework/main.js\",\"prestasupport.zendesk.com\");/*]]>*/</script>".$form;
    }

    public function hookFooter($params) {
        if (Configuration::get('uecookie_lib')!=1){
            if(!isset($_COOKIE['cookie_ue'])) {
                $var = $this->getconf();
                global $smarty;
                $smarty->assign('vareu', $var);
                if($this->psversion() == 5 || $this->psversion() == 6 || $this->psversion() == 7) {
                    $smarty->assign(array('uecookie' => $message = Configuration::get('uecookie_text', $this->context->language->id)));
                } else {
                    global $cookie;
                    $smarty->assign(array('uecookie' => $message = Configuration::get('uecookie_text', $cookie->id_lang)));
                }
                return $this->display(__file__, 'top.tpl');
            }
        }
    }
    
    public function hookHeader($params) {
        if (Configuration::get('uecookie_lib')==1){
            if(!isset($_COOKIE['cookie_ue'])) {
                $var = $this->getconf();
                global $smarty;
                $smarty->assign('vareu', $var);
                if($this->psversion() == 5 || $this->psversion() == 6  || $this->psversion() == 7) {
                    $smarty->assign(array('uecookie' => $message = Configuration::get('uecookie_text', $this->context->language->id)));
                } else {
                    global $cookie;
                    $smarty->assign(array('uecookie' => $message = Configuration::get('uecookie_text', $cookie->id_lang)));
                }
                return $this->display(__file__, 'top.tpl');
            }
        }
    }    
}

class uecookieUpdate extends uecookie {
    public static function version($version) {
        $version = (int)str_replace(".", "", $version);
        if(strlen($version) == 3) {
            $version = (int)$version."0";
        }
        if(strlen($version) == 2) {
            $version = (int)$version."00";
        }
        if(strlen($version) == 1) {
            $version = (int)$version."000";
        }
        if(strlen($version) == 0) {
            $version = (int)$version."0000";
        }
        return (int)$version;
    }

    public static function encrypt($string) {
        return base64_encode($string);
    }

    public static function verify($module, $key, $version) {
        if(ini_get("allow_url_fopen")) {
            if(function_exists("file_get_contents")) {
                $actual_version = @file_get_contents('http://dev.mypresta.eu/update/get.php?module='.$module."&version=".self::encrypt($version)."&lic=$key&u=".self::encrypt(_PS_BASE_URL_.__PS_BASE_URI__));
            }
        }
        Configuration::updateValue("update_".$module, date("U"));
        Configuration::updateValue("updatev_".$module, $actual_version);
        return $actual_version;
    }
}

?>