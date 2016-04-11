<?php
define('DEBUG', true);
define('PS_SHOP_PATH', 'http://monprestashop.com');
define('PS_WS_AUTH_KEY', 'U16Z1ZMD8ZF7FZUJ9UWNR12WA3UUZQBX');
require_once('./PSWebServiceLibrary.php');

try {
    $webService = new PrestaShopWebservice(PS_SHOP_PATH, PS_WS_AUTH_KEY, DEBUG);
}
catch (PrestaShopWebserviceException $ex) {
    // Shows a message related to the error
    echo 'Other error: <br />' . $ex->getMessage();
}
