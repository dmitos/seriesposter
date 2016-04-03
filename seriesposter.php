<?php
echo "||||||  ||||||  ||||||  ||||  ||||||  ||||||\n";
echo "||      ||   |  ||  ||   ||   ||   |  ||\n";
echo "||||||  ||||    ||||||   ||   ||||    ||||||\n";
echo "    ||  ||   |  || ||    ||   ||   |      ||\n";
echo "||||||  ||||||  ||  ||  ||||  ||||||  ||||||\n";
echo "||||||  ||||||  |||||| |||||| ||||||  ||||||\n";
echo "||  ||  ||  ||  ||       ||   ||   |  ||  ||\n";
echo "||||||  ||  ||  ||||||   ||   ||||    ||||||\n";
echo "||      ||  ||      ||   ||   ||   |  || ||\n";
echo "||      ||||||  ||||||   ||   ||||||  ||  ||v1.1\n";

//pido que ingrese el directorio base
//por ejemplo /home/nombre/Videos/series tv #en linux
echo "Ingresar direcotrio raiz donde se encuentran las SERIES: ";
//$directorio1 = trim(fgets(STDIN));
$directorio1="C:/xampp/htdocs/www/seriestv/tvseries";
if (is_dir($directorio1)){ //compruebo si es un directorio
// obtengo el array con los directorios y sus nombres
$arboldir  = scandir($directorio1);

// elimino el primero y segundo array, por ser /. /..
$i = 0;
	while ($i < 2){
		unset($arboldir[$i]);
	$i++;
	}
//ordeno el array directorio
$arraydir = array_values($arboldir);
$cantidad = count($arraydir);
echo "se encontraron ".$cantidad." directorios\n";
}else{ echo "No existe el directorio al cual quiere acceder\n";
exit;}
//------------------------------------------------------

foreach($arraydir as $serie){
//echo "serie: ";
//$serie = trim(fgets(STDIN));
//revisar si ya esta el cover proximamente---------------------

$path = $directorio1."/".$serie."/cover.jpg";
if (file_exists($path)){
	echo "Ex-iste el cover de ".$serie.", No se Descarga\n";
}else{
//sustituyo los espacios en blanco o separadores con +
	$remplazar = array(" ","_","-");
	$buscado = str_replace($remplazar, "+",$serie);



	echo "Descargando caratula de ".$serie."\n";
//path de donde descargo la busqueda con el dato
	$url = 'https://itunes.apple.com/search?term='.$buscado.'&entity=tvSeason';
	$config['useragent'] = 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0';
//para decodificar el archivo
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_USERAGENT, $config['useragent']);

		curl_setopt($ch, CURLOPT_URL, $url); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0); 
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$json = curl_exec($ch); 
		if ($errno = curl_errno($ch)) {
		    echo json_encode(array('error' => "error en tomar base de itunes"));
		    print_r(curl_error($ch));
		    exit;
		}
		curl_close($ch);    
		$obj = json_decode($json,TRUE);

		foreach ($obj as $objeto) {
			$imagen = $objeto[0]['artworkUrl100'];

		}
	$imagengrande = str_replace("100x100bb","300x300",$imagen);

//-----------------------------------------------------------

// cover.jpg es el nombre cuando se descarge
	$img = $directorio1."/".$serie."/cover.jpg";
//la descarga se da aca
	file_put_contents($img, file_get_contents($imagengrande));
}
}
?>