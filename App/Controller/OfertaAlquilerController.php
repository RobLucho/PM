<?php
    require_once(__DIR__.'/../Model/controladorDeSessiones.php');
    require_once(__DIR__.'/../Model/ofertaAlquiler.php');

    class OfertaAlquilerController extends Controller{

        private $ofertaModel;
        private $userSession;

        public function __construct($connect,$session){
            $this->ofertaModel = new OfertaAlquiler($connect);
            $this->userSession = new ControladorDeSessiones($session,$connect);
        }

        public function home(){
            if($this->userSession->Roll() === LOG){
                $user = $this->userSession->ID();
                $userVerificado = $this->userSession->esVerificado();
                $ofertasUsuario = $this->ofertaModel->buscarRegistrosRelacionados('usuarios', 'usuarioID', 'creadorID' , $user);
                $cont = 0;
                foreach($ofertasUsuario as $ofertas){
                    if($ofertas != null){
                        $cont++;
                    }
                }
                $this->render('publicarOferta', [
                    'esVerificado' => $userVerificado,
                    'cantOfertas' => $cont,
                ] ,'site');
            }else{
                header("Location:".URL_PATH);
            }
        }

        public function crear(){
            if($this->userSession->Roll() === LOG){
                $this->render('publicarOfertaCreate',[],'site');
            }else{
                header("Location:".URL_PATH);
            } 
        }

        public function create(){
            $result = new Result();
            
            if($_SERVER["REQUEST_METHOD"] == "POST"){
                $titulo = (isset($_POST['titulo'])) ? $_POST['titulo']: '';
                $descripcion = (isset($_POST['descripcion'])) ? $_POST['descripcion']: '';
                $ubicacion = (isset($_POST['ubicacion'])) ? $_POST['ubicacion']: '';
                $etiqueta = (isset($_POST['etiqueta'])) ? $_POST['etiqueta']: '';
                $costo = (isset($_POST['costoAlquilerPorDia'])) ? $_POST['costoAlquilerPorDia']: '';
                $cupo = (isset($_POST['cupo']))? $_POST['cupo']: '';
                $tiempoMin = (isset($_POST['tiempoMinPermanencia'])) ? $_POST['tiempoMinPermanencia']: '';
                $tiempoMax = (isset($_POST['tiempoMaxPermanencia'])) ? $_POST['tiempoMaxPermanencia']: '';
                $fechaIni = (isset($_POST['fechaInicio'])) ? $_POST['fechaInicio']: '';
                $fechaFin = (isset($_POST['fechaFin'])) ? $_POST['fechaFin']: '';
                $listServicios = isset($_POST['servicios']) ? implode(", ", $_POST['servicios']) : '';
                
                if (isset($_FILES['galeriaFotos']) && is_array($_FILES['galeriaFotos']['tmp_name'])) {
                    $galeriaFotos = [];
                
                    foreach ($_FILES['galeriaFotos']['tmp_name'] as $key => $tmp_name) {
                        // Validar si es una imagen y obtener la extensión del archivo
                        $tipoMIME = $_FILES['galeriaFotos']['type'][$key];
                        $extension = pathinfo($_FILES['galeriaFotos']['name'][$key], PATHINFO_EXTENSION);
                        
                        // Verificar que es una imagen válida 
                        if (strpos($tipoMIME, 'image') === 0) {
                            // Generar un nombre de archivo único con una extensión segura
                            $fecha_img = new DateTime();
                            $nombre_foto = $fecha_img->getTimestamp() . '_' . uniqid() . '.' . $extension;
                            
                            // Ruta de destino relativa al directorio de imágenes
                            //'Assets/images/galeriaFotos/''C:\xampp\htdocs\PM\Public\Assets\images\galeriaFotos\\'
                            $destinationPath = 'Assets/images/galeriaFotos/' . $nombre_foto;
                            
                            // Mover la imagen al directorio de destino
                            if (move_uploaded_file($tmp_name, $destinationPath)) {
                                $galeriaFotos[] = $nombre_foto;
                            }
                        }
                    }
                }

                $fotosString = implode(", ", $galeriaFotos);
            }
            $esVerificado = $this->userSession->esVerificado();
            $estado = '';
            if($esVerificado === true){
                $estado = 'publicado';
            }else{
                $estado = 'espera';
            }

            $id = $this->userSession->ID();

            $this->ofertaModel->insert([
                'titulo' => $titulo,
                'descripcion' => $descripcion,
                'ubicacion' => $ubicacion,
                'etiquetas' =>  $etiqueta,
                'galeriaFotos' => $fotosString,
                'listServicios' => $listServicios,
                'costoAlquilerPorDia' => $costo,
                'tiempoMinPermanencia' => $tiempoMin,
                'tiempoMaxPermanencia' => $tiempoMax,
                'cupo' => $cupo,
                'fechaInicio' => $fechaIni,
                'fechaFin' => $fechaFin,
                'estado' => $estado,
                'creadorID' => $id
            ]);

            $result->success = true;
            $result->message = "Oferta de alquiler creada con éxito";
            echo json_encode($result);
        }

        public function edit(){
            if($this->userSession->Roll() === LOG){
                $id = $_GET['id'];

                $oferta = $this->ofertaModel->getById($id);

                $this->render('publicarOfertaEdit',[
                    'oferta' => $oferta,
                ],'site');
            }else{
                header("Location:".URL_PATH);
            } 
        }

        public function delete(){
            $result = new Result();

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $idOferta = (isset($_POST['id']))?$_POST['id']:'';
                if($idOferta != null && is_numeric($idOferta)){
                    $oferta = $this->ofertaModel->getById($idOferta);
                    if($oferta){
                        $imagenes = explode(", ", $oferta['galeriaFotos']);
                        foreach($imagenes as $imagen){
                            $imagenPath = 'Assets/images/galeriaFotos/'.$imagen;
                            if (file_exists($imagenPath)) {
                                unlink($imagenPath);
                            }
                        }
                        $this->ofertaModel->deleteById($_POST['id']);
                        $result->success = true;
                        $result->message = "Oferta eliminada con éxito";
                    }else{
                        $result->success = false;
                        $result->message = "Oferta no encontrada";
                    }

                }else{
                    $result->success = false;
                    $result->message = "Identificador invalido";
                }
            }else {
                $result->success = false;
                $result->message = "Solicitud no válida";
            }    
            

            echo json_encode($result);
        }

        public function table(){
            $result = new Result();
            $userLogin = $this->userSession->ID();
            $ofertasUsuario = $this->ofertaModel->buscarRegistrosRelacionados('usuarios', 'usuarioID', 'creadorID' , $userLogin);
            $result->success = true;
            $result->result = [
                'ofertas' => $ofertasUsuario,
            ];            
            echo json_encode($result);
        }

        public function update() {
            $result = new Result();
        
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $idOferta = (isset($_POST['textID'])) ? $_POST['textID'] : null;
                $titulo = (isset($_POST['titulo'])) ? $_POST['titulo'] : '';
                $descripcion = (isset($_POST['descripcion'])) ? $_POST['descripcion'] : '';
                $ubicacion = (isset($_POST['ubicacion'])) ? $_POST['ubicacion'] : '';
                $etiqueta = (isset($_POST['etiqueta'])) ? $_POST['etiqueta'] : '';
                $costo = (isset($_POST['costoAlquilerPorDia'])) ? $_POST['costoAlquilerPorDia'] : '';
                $cupo = (isset($_POST['cupo'])) ? $_POST['cupo'] : '';
                $tiempoMin = (isset($_POST['tiempoMinPermanencia'])) ? $_POST['tiempoMinPermanencia'] : '';
                $tiempoMax = (isset($_POST['tiempoMaxPermanencia'])) ? $_POST['tiempoMaxPermanencia'] : '';
                $fechaIni = (isset($_POST['fechaInicio'])) ? $_POST['fechaInicio'] : '';
                $fechaFin = (isset($_POST['fechaFin'])) ? $_POST['fechaFin'] : '';
                $listServicios = isset($_POST['servicios']) ? implode(", ", $_POST['servicios']) : '';
        
                if ($idOferta != null && is_numeric($idOferta)) {
                    $oferta = $this->ofertaModel->getById($idOferta);
                    if ($oferta) {
                        if (count($_FILES)>0) {
                            // Procesar y subir nuevas imágenes
                            if(isset($_FILES['galeriaFotos']) && is_array($_FILES['galeriaFotos']['tmp_name'])){
                                $galeriaFotos = [];
                                if (isset($oferta['galeriaFotos'])) {
                                    $imagenes = explode(", ", $oferta['galeriaFotos']);
                                    foreach ($imagenes as $imagen) {
                                        $imagenPath = 'Assets/images/galeriaFotos/' . $imagen;
                                        if (file_exists($imagenPath) && is_file($imagenPath)) {
                                            unlink($imagenPath);
                                        }
                                    }
                                }
                                
            
                                foreach ($_FILES['galeriaFotos']['tmp_name'] as $key => $tmp_name) {
                                    // Validar si es una imagen y obtener la extensión del archivo
                                    $tipoMIME = $_FILES['galeriaFotos']['type'][$key];
                                    $extension = pathinfo($_FILES['galeriaFotos']['name'][$key], PATHINFO_EXTENSION);
            
                                    // Verificar que es una imagen válida
                                    if (strpos($tipoMIME, 'image') === 0) {
                                        // Generar un nombre de archivo único con una extensión segura
                                        $fecha_img = new DateTime();
                                        $nombre_foto = $fecha_img->getTimestamp() . '_' . uniqid() . '.' . $extension;
            
                                        // Ruta de destino relativa al directorio de imágenes
                                        $destinationPath = 'Assets/images/galeriaFotos/' . $nombre_foto;
            
                                        // Mover la imagen al directorio de destino
                                        if (move_uploaded_file($tmp_name, $destinationPath)) {
                                            $galeriaFotos[] = $nombre_foto;
                                        }
                                    }
                                }
                                $fotosString = implode(", ", $galeriaFotos);
                            }
                            
                        } else {
                            $fotosString = $oferta['galeriaFotos']; // No se subieron nuevas imágenes, mantener las antiguas
                        }
                        $id = $this->userSession->ID();
                        // Luego, actualiza la oferta con los datos proporcionados
                        $this->ofertaModel->updateById($idOferta, [
                            'titulo' => $titulo,
                            'descripcion' => $descripcion,
                            'ubicacion' => $ubicacion,
                            'etiquetas' => $etiqueta,
                            'galeriaFotos' => $fotosString, // Utilizar las nuevas imágenes o las antiguas
                            'listServicios' => $listServicios,
                            'costoAlquilerPorDia' => $costo,
                            'tiempoMinPermanencia' => $tiempoMin,
                            'tiempoMaxPermanencia' => $tiempoMax,
                            'cupo' => $cupo,
                            'fechaInicio' => $fechaIni,
                            'fechaFin' => $fechaFin,
                            'creadorID' => $id
                        ]);
        
                        $result->success = true;
                        $result->message = "Oferta modificada con éxito";
                    } else {
                        $result->success = false;
                        $result->message = "Identificador inválido";
                    }
                }
            }
            echo json_encode($result);
        }
        
        
    }


?>