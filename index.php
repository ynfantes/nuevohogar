<?php
include_once 'includes/constants.php';
$accion = isset($_GET['accion']) ? $_GET['accion']:'';

if ($accion!="") {
    if ($accion=='inmobiliaria') {
        echo $twig->render('inmobiliaria/index.html.twig');
    } else {
        echo $twig->render($accion.'.html.twig');
        //echo $twig->render('suspendido.html.twig');
    }
} else {
   $db = new db();
   $municipios = $db->dame_query("select * from municipios where id > 1 order by descripcion ");
   $tipo = $db->dame_query("select * from inmobiliaria_tipo where id > 1 order by descripcion");
   $operacion = $db->dame_query("select * from operaciones where id > 1 order by descripcion");
  
   $data = Array(
       'municipios'=>$municipios['data'],
       'tipo'=>$tipo['data'],
       'operacion'=>$operacion['data']
   );
   echo $twig->render('index.html.twig',$data);
}