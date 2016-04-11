<html><head><title>Widget products</title></head><body>
<?php
define('DEBUG', false);
define('PS_SHOP_PATH', 'http://192.168.33.10/prestashop_examen/');
define('PS_WS_AUTH_KEY', 'U16Z1ZMD8ZF7FZUJ9UWNR12WA3UUZQBX');
require_once('./PSWebServiceLibrary.php');

try {
    $webService = new PrestaShopWebservice(PS_SHOP_PATH, PS_WS_AUTH_KEY, DEBUG);
}
catch (PrestaShopWebserviceException $ex) {
    // Shows a message related to the error
    echo 'Other error: <br />' . $ex->getMessage();
}

$opt['resource'] = 'products';
//$opt['filter'] = 'id';
//$opt['display'] = 'full';

if (isset($_GET['id']))
  $opt['id'] = (int)$_GET['id']; // cast string => int for security measures

$xml = $webService->get($opt);

$resources = $xml->children()->children();

//var_dump($resources[0]);

echo '<table border="5">';
// if $resources is set we can lists element in it otherwise do nothing cause there's an error
if (isset($resources))
{
	if (!isset($_GET['id']))
	{
		echo '<tr><th>Id</th><th>More</th></tr>';
		foreach ($resources as $resource)
		{
			// Iterates on the found IDs
			echo '<tr><td>'.$resource->attributes().'</td><td>'.
			'<a href="?id='.$resource->attributes().'">Retrieve</a>'.
			'</td></tr>';
		}
	}
	else
	{
		foreach ($resources as $key => $resource)
		{
			// Iterates on customer's properties
			echo '<tr>';
			echo '<th>'.$key.'</th><td>'.$resource.'</td>';
			echo '</tr>';
		}
	}
}
echo '</table>';
?>
</body></html>
