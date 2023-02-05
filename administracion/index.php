<?php
include_once '../includes/constants.php';

$accion = isset($_GET['accion'])?$_GET['accion']:"";
$publicaciones = new publicaciones();
switch ($accion) {
    
    
    case "login":
        $administrador = Array();
        $login = new administrador();
        if (isset($_POST['usuario']) && isset($_POST['password'])) {
            $administrador = $login->login($_POST['usuario'], $_POST['password']);
        }
        echo $twig->render('administracion/index.html.twig', Array("administrador" => $administrador));
        break; 

    case "inactivar":
        if (isset($_GET['id'])) {
            $result = $publicaciones->actualizar($_GET['id'], Array("inactivo" => 1));
            if ($result['suceed']) {
                $result['mensaje'] = "(".$result['stats']['affected_rows'].") Publicación inactivada) con éxito.";
            } else {
                $result['mensaje'] = "Ups! ocurrió un error al tratar de inactivar la publicación.";
            }
            
        }
        $listado = $publicaciones->obtenerPublicaciones();
        echo $twig->render('administracion/lista-publicaciones.html.twig', Array(
            "listado" => $listado['data'],"resultado"=>$result
        ));
        break;
     
    case "publicaciones":
        $listado = $publicaciones->obtenerPublicaciones();
        echo $twig->render('administracion/lista-publicaciones.html.twig', Array(
            "listado" => $listado['data']
        ));
        break; 

    case "publicar":
        $db = new db();
        $municipios = $db->dame_query("select * from municipios where id > 1 order by descripcion ");
        $tipo = $db->dame_query("select * from inmobiliaria_tipo where id > 1 order by descripcion");
        $operacion = $db->dame_query("select * from operaciones where id > 1 order by descripcion");
        echo $twig->render('administracion/registrar-propiedad.html.twig', Array(
            "municipios" => $municipios['data'],
            "tipo" => $tipo['data'],
            "operacion" => $operacion['data']));
        break; 

    case "guardar":
        $dir    = "images/prv/estate/";
        $db     = new db();
        $result = [];
        $data   = $_POST;
        if(!empty($_FILES)){
            echo 'vienen files';
            $route = $dir . basename($FILES['file']['name']);
            die($route);
            move_uploaded_file($_FILES['file']['tmp'],$route);
        }
        
        if (isset($data['imagen'])) {
            $imagenes = $data['imagen'];
            unset($data['imagen']);
        }
        // editar
        if (isset($data['editar']) && $data['editar'] == 'editar') {
            unset($data['editar']);
            $id = $data['id'];
            $result = $publicaciones->actualizar($id, $data);
            if ($result['suceed']) {
                $result['mensaje'] = 'Publicación actualizada con éxito!';
            } else {
                $result['mensaje'] = 'Ups! Ocurrió un error durante el proceso. No se puedo actualizar la información';
            }
            $db->delete("inmobiliaria_img", Array("id_inmobiliaria_publicacion" => $id));
        } else {
            // guardar
            $result = $publicaciones->insertar($data);
            if ($result['suceed']) {
                $id = $result['insert_id'];
                $result['mensaje'] = 'Publicación guardada con éxito';
            } else {
                $result['mensaje'] = 'Ups! Ocurrió un error durante el proceso. No se puedo guardar la información';
            }
        }
        if ($result['suceed']) {
            if (isset($imagenes)) {
                $i = 0;
                foreach ($imagenes as $imagen) {
                    if ($i == 0) {
                        $result = $publicaciones->actualizar($id, Array("imagen" => $dir . $imagen));
                    } else {
                        $db->insert("inmobiliaria_img", Array("id_inmobiliaria_publicacion" => $id, "imagen" => $dir . $imagen));
                    }
                    $i++;
                }
            }
        }

        $propiedad  = $publicaciones->ver($id);
        $municipios = $db->dame_query("select * from municipios where id > 1 order by descripcion ");
        $tipo       = $db->dame_query("select * from inmobiliaria_tipo where id > 1 order by descripcion");
        $operacion  = $db->dame_query("select * from operaciones where id > 1 order by descripcion");

        $opciones = [
                'municipios' => $municipios['data'],
                'tipo'       => $tipo['data'],
                'operacion'  => $operacion['data'],
                'propiedad'  => $propiedad['data'],
                'accion'     => 'editar',
                'resultado'  => $result,
            ];
        echo $twig->render('administracion/registrar-propiedad.html.twig', $opciones);


        break; 
    
    case "editar":
        $db = new db();
        
        $municipios = $db->dame_query("select * from municipios where id > 1 order by descripcion ");
        $tipo       = $db->dame_query("select * from inmobiliaria_tipo where id > 1 order by descripcion");
        $operacion  = $db->dame_query("select * from operaciones where id > 1 order by descripcion");
        $propiedad  = $publicaciones->ver($_GET['id']);

        if ($propiedad['suceed'] && count($propiedad['data']) > 0) {
            for ($index = 0; $index < count($propiedad['data']); $index++) {
                $imagenes = $publicaciones->obtenerImagenesPorPublicacion($propiedad['data'][$index]['id']);
                $propiedad['data'][$index]['imagenes'] = $imagenes['data'];
            }
            $publicaciones->actualizar($_GET['id'], Array("visto" => $propiedad['data'][0]['visto'] + 1));
        }
        $opciones = [
            'municipios'  => $municipios['data'],
            'tipo'        => $tipo['data'],
            'operacion'   => $operacion['data'],
            'propiedad'   => $propiedad['data'],
            'accion'      => "editar"
        ];
        echo $twig->render('administracion/registrar-propiedad.html.twig', $opciones);
        break; 

    default :
        echo $twig->render('administracion/index.html.twig');
        break;
}
        


