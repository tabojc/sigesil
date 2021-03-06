<?php
    require_once('../lib/core.lib.php');
    $productor = new Productor();

    $lineaComienzo = (!empty($GPC['primera_linea'])) ? $GPC['primera_linea'] : 2;
    
    if ($GPC['ac'] == "guardar") {
        $archivo = new FileUpload(array('excel'));
        $permitidas = $archivo->allowedTypes;
        $usuario_archivo = $_FILES['archivo'];
        $ext_xls = array('.xls', '.xlsx'); //Los archivos nativos de excel no se pueden leer
        $ext_archivo = substr($usuario_archivo['name'], strrpos($usuario_archivo['name'], '.'));
        if (is_uploaded_file($usuario_archivo['tmp_name']) && in_array($usuario_archivo['type'], $permitidas) && in_array($ext_archivo, $ext_xls)) {
            $resultadoArchivo = $productor->subirProductores($_FILES);
            if ($resultadoArchivo) {
                header("location: productor_carga_ver.php?archivo=$resultadoArchivo&linea=".$lineaComienzo);
                exit;
            } else {
                header("location: ?msg=error");
                exit;
            }
        } else {
            header("location: ?msg=error");
            exit;
        }
    }
    require('../lib/common/header.php');
    $validator = new Validator('form1');
    $validator->printIncludes();
    $validator->setRules('archivo', array('required' => array('value' => true, 'message' => 'Requerido')));
    $validator->setRules('primera_linea', array('required' => array('value' => true, 'message' => 'Requerido')));
    $validator->printScript();
?>
<script type="text/javascript">
    function cancelar(){
        window.location = 'productor_carga.php';
    }
    $(document).ready(function(){
        $('#Guardar').click(function(){
            show_div_loader();
        });
        
        $(".positive").numeric({ negative: false }, function() { alert("No negative values"); this.value = ""; this.focus(); });
    });
</script>
<form name="form1" id="form1" method="POST" action="?ac=guardar" enctype="multipart/form-data">
    <div id="titulo_modulo">
       CARGA DE PRODUCTORES<br/><hr/>
    </div>
    <div id="mensajes">
        <?php
            switch($GPC['msg']){
                case 'exitoso':
                    echo "<span class='msj_verde'>Registro Guardado !</span>";
                break;
                case 'error':
                    echo "<span class='msj_rojo'>Extensi&oacute;n del Archivo Inv&aacute;lida !</span>";
                break;
            }
        ?>
    </div>
    <table align="center">
        <tr>
            <td><span class="msj_rojo">* </span>Archivo</td>
            <td><?php echo $html->input('archivo', '', array('type' => 'file', 'class' => 'estilo_campos')) ?></td>
        </tr>
        <tr>
            <td><span class="msj_rojo">* </span>Primera L&iacute;nea de Datos</td>
            <td><?php echo $html->input('primera_linea', '2', array('type' => 'text', 'class' => 'estilo_campos positive')) ?></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
    </table>
    <table width="100%" border="0">
        <tr align="center">
            <th colspan="5">Estructura del Archivo Excel</th>
        </tr>
        <tr align="center" class="titulos_tabla">
            <th>A</th>
            <th>B</th>
            <th>C</th>
            <th>D</th>
        </tr>
        <tr align="center" class="segundaclase">
            <th><span class="msj_rojo">* </span>C&eacute;dula/Rif (Obligatorio)</th>
            <th><span class="msj_rojo">* </span>Nombre (Obligatorio)</th>
            <th>Tel&eacute;fono (Opcional)</th>
            <th>Direcci&oacute;n (Opcional)</th>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <th class="msj_verde" colspan="2">Correcta: V9999999</th>
            <th class="msj_verde" colspan="2">Correcto: 0416123456</th>
        </tr>
        <tr>
            <th class="msj_rojo" colspan="2">Incorrecta: 9999999</th>
            <th class="msj_rojo" colspan="2">Inorrecto: 0416-123456</th>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr align="center">
            <td colspan="5">
                <?php echo $html->input('Guardar', 'Cargar', array('type' => 'submit')); ?>
                <?php echo $html->input('Cancelar', 'Cancelar', array('type' => 'reset', 'onClick' => 'cancelar()')); ?>
            </td>
        </tr>
    </table>
</form>
<?php require('../lib/common/footer.php'); ?>