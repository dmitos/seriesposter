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
echo "||      ||||||  ||||||   ||   ||||||  ||  ||v1\n";

//pido que ingrese el directorio base
//por ejemplo /home/nombre/Videos/series tv #en linux
echo "Ingresar direcotrio raiz donde se encuentran las SERIES: ";
$directorio1 = trim(fgets(STDIN));

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
//------------------------------------------------------

foreach($arraydir as $serie){
//echo "serie: ";
//$serie = trim(fgets(STDIN));

//sustituyo los espacios en blanco o separadores con +
	$remplazar = array(" ","_","-");
	$buscado = str_replace($remplazar, "+",$serie);
	echo "Descargando caratula de ".$serie."\n";
//path de donde descargo la busqueda con el dato
	$url = 'https://itunes.apple.com/search?term='.$buscado.'&entity=tvSeason';
//para decodificar el archivo
	$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL, $url); 
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30); 
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$json = curl_exec($ch); 
		if ($errno = curl_errno($ch)) {
		    echo json_encode(array('error' => "error en tomar base de itunes"));
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
?>