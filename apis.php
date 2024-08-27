<?php
    // Tomo las variables del formulario
    $calle = $_POST["calle"];
    $altura = $_POST["altura"];

    // Armo la petición para normalizar_direcciones
    $normalizar_direcciones = str_replace(" ","%20","https://ws.usig.buenosaires.gob.ar/rest/normalizar_direcciones?calle=" . $calle . "&altura=" . $altura . "&desambiguar=1");
    
    // Mando la petición a normalizar_direccion y tomo el json
    $json_normalizar_direcciones = file_get_contents($normalizar_direcciones, False);
    $json_normalizar_direcciones_output = json_decode($json_normalizar_direcciones);
    
    // Hago los controles de json vacío y actualizo las variables
    if(empty($json_normalizar_direcciones_output->DireccionesCalleAltura->direcciones[0]->CodigoCalle)){$codigo_calle = null;}else{$codigo_calle = $json_normalizar_direcciones_output->DireccionesCalleAltura->direcciones[0]->CodigoCalle;};
    if(empty($json_normalizar_direcciones_output->DireccionesCalleAltura->direcciones[0]->Calle)){$calle_normalizada = 'No se pudo encontrar';}else{$calle_normalizada = $json_normalizar_direcciones_output->DireccionesCalleAltura->direcciones[0]->Calle;};
    if(empty($json_normalizar_direcciones_output->DireccionesCalleAltura->direcciones[0]->Altura)){$altura_normalizada = 'No se pudo encontrar';}else{$altura_normalizada = $json_normalizar_direcciones_output->DireccionesCalleAltura->direcciones[0]->Altura;};    
    
    // Armo la petición para datos_utiles
    $datos_utiles = str_replace(" ","%20","https://ws.usig.buenosaires.gob.ar/datos_utiles?calle=" . $calle . "&altura=" . $altura);
    
    // Mando la petición a datos_utiles y tomo el json
    $json_datos_utiles = file_get_contents($datos_utiles, False);
    $json_datos_utiles_output = json_decode($json_datos_utiles);

    // Hago los controles para json vacío y actualizo las variables
    if(empty($json_datos_utiles_output->comuna)){$comuna = 'No se pudo encontrar';}else{$comuna = $json_datos_utiles_output->comuna;};
    if(empty($json_datos_utiles_output->barrio)){$barrio = 'No se pudo encontrar';}else{$barrio = $json_datos_utiles_output->barrio;};
    if(empty($json_datos_utiles_output->codigo_postal)){$codigo_postal = 'No se pudo encontrar';}else{$codigo_postal = $json_datos_utiles_output->codigo_postal;};
    if(empty($json_datos_utiles_output->distrito_escolar)){$distrito_escolar = 'No se pudo encontrar';}else{$distrito_escolar = $json_datos_utiles_output->distrito_escolar;};

    // Si tengo codigo de calle (o sea que tengo resultado para normalizar_direcciones)
    if ($codigo_calle <> null) {
        // Armo la petición para calles_adyacentes    
        $calles_adyacentes = str_replace(" ", "%20", "https://ws.usig.buenosaires.gob.ar/rest/obtener_calles_adyacentes?cod_calle=" . $codigo_calle . "&altura=" . $altura_normalizada);

        // Mando la petición a calles_adyacentes
        $json_calles_adyacentes = file_get_contents($calles_adyacentes, False);
        $json_calles_adyacentes_output = json_decode($json_calles_adyacentes);

        // Hago los controles para json vacío y actualizo las variables
        if(empty($json_calles_adyacentes_output->calles[0]->Nombre)){$calle_izquierda = 'No se pudo encontrar';}else{$calle_izquierda = $json_calles_adyacentes_output->calles[0]->Nombre;};
        if(empty($json_calles_adyacentes_output->calles[1]->Nombre)){$calle_derecha = 'No se pudo encontrar';}else{$calle_derecha = $json_calles_adyacentes_output->calles[1]->Nombre;};


    }else{$calle_izquierda = 'No se pudo encontrar';$calle_derecha = 'No se pudo encontrar';};

    
    // Armo la vista para mostrar los resultados
?>
<!doctype html>
<html lang="en" class="h-100">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>: geo :</title>
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <style>
            .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
            }
            @media (min-width: 768px) {
                .bd-placeholder-img-lg {
                    font-size: 3.5rem;
                }
            }
        </style>
        <link href="css/cover.css" rel="stylesheet">
    </head>
    <body class="d-flex h-100 text-center text-white bg-dark">
        <div class="cover-container d-flex w-100 h-100 p-3 mx-auto flex-column">
            <header class="mb-auto">
                <div>
                    <h3 class="float-md-start mb-0">GEO</h3>
                    <nav class="nav nav-masthead justify-content-center float-md-end">
                        <a class="nav-link active" aria-current="page" href="index.html">Inicio</a>
                    </nav>
                </div>
            </header>
            <main class="px-3">
                <h1>Resultado de la geolocalización de direcciones</h1>
                <p>Dirección pedida : <b><?php echo $calle . ' ' . $altura; ?></b>
                <hr>
                <div class="container">
                    <div class="row">
                        <!-- Pongo los títulos de la tabla -->
                        <div class="col-8"><b>Campo</b></div>
                        <div class="col-4"><b>Valor</b></div>
                        <!-- Pongo el contenido -->
                        <div class="col-8">Calle Normalizada</div>
                        <div class="col-4"><?php echo $calle_normalizada ?></div>
                        <div class="col-8">Altura Normalizada</div>
                        <div class="col-4"><?php echo $altura_normalizada ?></div>
                        <div class="col-8">Comuna</div>
                        <div class="col-4"><?php echo $comuna ?></div>
                        <div class="col-8">Barrio</div>
                        <div class="col-4"><?php echo $barrio ?></div>
                        <div class="col-8">Código Postal</div>
                        <div class="col-4"><?php echo 'C' . $codigo_postal ?></div>
                        <div class="col-8">Distrito Escolar</div>
                        <div class="col-4"><?php echo $distrito_escolar ?></div>
                        <div class="col-8">Calle de la Izquierda</div>
                        <div class="col-4"><?php echo $calle_izquierda ?></div>
                        <div class="col-8">Calle de la derecha</div>
                        <div class="col-4"><?php echo $calle_derecha ?></div>
                    </div>
                </div>
            </main>
            <footer class="mt-auto text-white-50">
                <p>GEO</p>
            </footer>
        </div>
    </body>
</html>