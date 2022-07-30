<?php
include_once '../includes/constants.php';
$publicacion = new publicaciones();
$db = new db();

$accion = isset($_GET['accion']) ? $_GET['accion'] : "listar";

switch ($accion) {
    
    // <editor-fold defaultstate="collapsed" desc="listar">
    case "listar":
    default :
        $sort = 'fecha_publicacion';
        $order = 'ASC';
        $limit = 4;
        $total = 0;
        $num_pages = 1;

        if (isset($_GET['page'])) {
            $page = $_GET['page'];
        } else {
            $page = 1;
        }
        $data = $_POST;
        $data['sort'] = $sort;
        $data['order'] = $order;
        $data['start'] = ($page - 1) * $limit;
        $data['limit'] = $limit;

        $publicaciones = $publicacion->obtenerPublicaciones($data);
        $mas_visto = $publicacion->publicacionMasVistas(3);
        
        $municipios = $db->dame_query("select * from municipios order by descripcion ");
        $tipo = $db->dame_query("select * from inmobiliaria_tipo where id > 1 order by descripcion");
        $operacion = $db->dame_query("select * from operaciones where id > 1 order by descripcion");

        if ($publicaciones['suceed']) {
            $total = $publicacion->totalPublicaciones();
            $num_pages = ceil($total / $limit);
        }
       
        echo $twig->render('inmobiliaria/index.html.twig', Array(
            'listado' => $publicaciones['data'],
            'num_pages' => $num_pages,
            'municipios' => $municipios['data'],
            'tipo' => $tipo['data'],
            'operaciones' => $operacion['data'],
            'mas_visto' => $mas_visto['data'],
            'data' => $data,
            'pagina'=>$page)
        );
        break; // </editor-fold>
        
    // <editor-fold defaultstate="collapsed" desc="ver propiedad">
    case "propiedad":
        $propiedad = $publicacion->ver($_GET['id']);
        if ($propiedad['suceed'] && count($propiedad['data']) > 0) {
            for ($index = 0; $index < count($propiedad['data']); $index++) {
                $imagenes = $publicacion->obtenerImagenesPorPublicacion($propiedad['data'][$index]['id']);
                $propiedad['data'][$index]['imagenes'] = $imagenes['data'];
            }
            $publicacion->actualizar($_GET['id'], Array("visto" => $propiedad['data'][0]['visto'] + 1));
        }
        $url = "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

        echo $twig->render('inmobiliaria/propiedad.html.twig', Array(
            'propiedad' => $propiedad['data'], 'url'=>$url
        ));
        break; 
    // </editor-fold>

}