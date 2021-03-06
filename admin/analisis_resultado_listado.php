<?php
require_once('../lib/core.lib.php');

$centroAcopio=new CentroAcopio();
$cosecha=new Cosecha();
$cultivo=new Cultivo();
$analisis=new Analisis();

$listaLab = array('C' => 'Lab. Central', 'P' => 'Lab. Planta');
$listaMov = array('rec' => 'RECEPCI&Oacute;N', 'des' => 'DESPACHO');
$listaAccion = array('B' => 'Buscar');
$fecha=$general->fecha_normal_sql($GPC['fecha'], 'es');
$idCA = ($_SESSION['s_perfil_id']==GERENTEG) ? null: $_SESSION['s_ca_id'];
$porPagina = MAX_RESULTS_PAG;
$inicio = ($GPC['pg']) ? (($GPC['pg'] * $porPagina) - $porPagina) : 0;

if (!empty($GPC['mov']))
    $_SESSION['s_mov']=$GPC['mov'];
else 
    $GPC['mov']=$_SESSION['s_mov'];

if (!empty($GPC['lab']))
    $_SESSION['s_lab']=$GPC['lab'];
else
    $GPC['lab']=$_SESSION['s_lab'];
    
switch ($GPC['mov']) {
    case 'rec':
        $recepcion = new Recepcion();
        $estatus=($_SESSION['s_lab']=='C')? "'1','10','11'": "'4','12','13'";
        $orden = " ORDER BY r.fecha_recepcion , r.id";
        $fecha=$general->fecha_normal_sql($GPC['fecha'], 'es');
        if (!empty($GPC['numEntrada']) && !empty($fecha))
            $estatus=$estatus . ",'2','3','5','6','7','8','9'";
        $listadoM=$recepcion->listadoRecepcion(null, $idCA, $idCo=null, null, $GPC['numEntrada'], $estatus, null, null, $porPagina, $inicio, null, $orden, null, null, null, null, null, null, $fecha, $fecha);
        //$listadoM=$recepcion->listadoRecepcion(null, $idCA, null, null, null, $estatus, null, null, null, null, null, $orden);
        $total_registros = $recepcion->total_verdadero;
        break;
    case 'des':
        $despacho= new Despacho();
        $estatus="'2'";
        $orden = " ORDER BY d.fecha_des , d.id";
        //$listadoM=$despacho->listadoDespacho(null, $idCA, null, null, null, $estatus, null, null, $porPagina, $inicio);
	$listadoM=$despacho->listadoDespacho(null, $idCA, null, null, $GPC['numEntrada'], $estatus, null, null, $porPagina, $inicio, null, null, null, null, null, null, $fecha, $fecha);
        $total_registros = $despacho->total_verdadero;
        break;
}

$paginador = new paginator($total_registros, $porPagina);

require('../lib/common/header.php');
require('../lib/common/init_calendar.php');
?>
<script type="text/javascript">
    
    var win = null;
    function openWindow(mypage,myname,w,h,scroll){
        LeftPosition = (screen.width) ? (screen.width-w)/2 : 0;
        TopPosition = (screen.height) ? (screen.height-h)/2 : 0;
        settings ='height='+h+',width='+w+',top='+TopPosition+',left='+LeftPosition+', scrollbars=yes ';
        win = window.open(mypage,myname,settings)
    }
    
    $(document).ready(function(){
        
        $(".positive").numeric({ negative: false }, function() { alert("No negative values"); this.value = ""; this.focus(); });
        
        $('#Procesar').click(function(){
            if ($('#lab').val()=='') {
                return;
            } 
            
            if ($('#mov').val()=='') {
                return;
            } 

            if ($('#Accion').val()=='') {
                return;
            }
        });
        
        //$('.imprimir').css('display','none');
        $('#Accion').change(function(){
            if ($(this).val()=='')
                $('#verAccion').css('display','none');
            else
                $('#verAccion').css('display','block');
        });
             
        $('#Regresar').click(function(){
           history.back();
        });
        
        $('#Buscar').click(function(){
            if ($('#Accion').val()=='B') {
                //alert('../ajax/detalle_analisis_buscar.php?codigo='+$('#numero').val()+'&fecha='+$('#fecha').val());
                 $('#idRec').load('../ajax/detalle_analisis_buscar.php?numero='+$('#numero').val()+'&fecha='+$('#fecha').val());                 
            }            
        });
    });
</script>
<div id="titulo_modulo">    
    RESULTADOS DE ANALISIS - <?php echo $listaMov[$GPC['mov']]; ?><hr/>    
</div> 
<div id="mensajes">
    <?php    
        switch($GPC['msg']){
            case 'exitoso':
                echo "<span class='msj_verde'>Registro Guardado !</span>";
            break;
            case 'error':
                echo "<span class='msj_rojo'>Ocurri&oacute; un Problema !</span>";
            break;
        }
    ?>
</div>
<div id="filtro">
    <form name="form1" id="form1" method="GET" action="" enctype="multipart/form-data">
        <table width="100%" border="0">
<!--            <tr>
                <td width="1">Fecha</td>
                <td width="200">
                    <?php 
                    //$GPC['fecha']
                    echo $html->input('fecha', $GPC['fecha'], array('type' => 'text', 'class' => 'crproductor', 'readOnly' => true)); ?>
                    <img src="../images/calendario.png" id="fdesde" width="16" height="16" style="cursor:pointer" />
                    <script>
                        Calendar.setup({
                            trigger    : "fdesde",
                            inputField : "fecha",
                            dateFormat: "%d-%m-%Y",
                            selection: Calendar.dateToInt(<?php echo date("Ymd", strtotime($GPC['fecha']));?>),
                            onSelect   : function() { this.hide() }
                        });
                    </script>
                </td>
                <td>Numero</td>
                <td><?php echo $html->input('numEntrada', $GPC['numEntrada'], array('type' => 'text', 'class' => 'crproductor', 'readOnly' => $soloLectura, 'class' => 'crproductor positive'));?> </td>
            </tr>
            <tr>
            </tr>-->
            <tr id="botones" aligment="right">
                <td colspan="4">
                    <?php
                        echo $html->input('Buscar', 'Buscar', array('type' => 'submit'));    
                        $general->crearAcciones($acciones, '', 1);
                        echo $html->input('Regresar', 'Regresar', array('type' => 'button', 'onClick' => 'regresar();'));
                    ?>
                </td>
            </tr>
        </table>
    </form>
</div><hr/>
<div id="paginador">
    <?php
        $paginador->print_page_counter('Pag', 'de');
        echo "&nbsp;&nbsp;";
        $paginador->print_paginator('pulldown');
    ?>
</div>
<table align="center" width="100%" border="0">
    <tr align="center" class="titulos_tabla"> 
    <?php if ($_SESSION['s_perfil_id']==GERENTEG) { ?>
        <th>Centro de Acopio</th>
    <?php
        }
        if ($_SESSION['s_mov']=='rec') {
    ?>
        <th width="85px">Nro Entrada</th>
    <?php }else{ ?>
        <th width="85px">Nro Salida</th>
    <?php } ?>
        <th>Cultivo</th>
    <?php if ($_SESSION['s_mov']=='rec') { ?>
        <th>Guia</th>
    <?php }else{ ?>
        <th>Orden</th>
    <?php } ?>
        <th>Fecha</th>
        <th>Estatus</th>
        <th>Acci&oacute;n</th>
    </tr>    
    <?php    
    $i=0;
    foreach($listadoM as $dataMov) {
        $clase = $general->obtenerClaseFila($i); 
    ?>
    <tr class="<?php echo $clase?>">
    <?php
        if ($_SESSION['s_perfil_id']==GERENTEG) {
            
            echo '<td>'.$dataMov['ca_codigo'].' - '.$dataMov['centro_acopio'].'</td>';
        }
    ?>
        <td>
    <?php
        if ($GPC['mov']=='rec') {
            $numero = ($dataMov['numero'] < 10) ? '0'.$dataMov['numero'] : $dataMov['numero'];
            $numEntrada = "R".$numero."-".$general->date_sql_screen($dataMov['fecha_recepcion'], '', 'es', null);
            echo $numEntrada;
        }else{
            $numero = ($dataMov['numero'] < 10) ? '0'.$dataMov['numero'] : $dataMov['numero'];
            $numSalida = "D".$numero."-".$general->date_sql_screen($dataMov['fecha_des'], '', 'es', null);
            echo $numSalida;
        }
    ?>
        </td>
        <td><?php echo $dataMov['cultivo_codigo'].' - '.$dataMov['cultivo_nombre']; ?></td>
        <td><?php echo $dataMov['numero_guia']; ?></td>
        <td>
        <?php 
        if ($GPC['mov']=='rec')
            echo $general->date_sql_screen($dataMov['fecha_recepcion'],'','es','-');
        if ($GPC['mov']=='des')
            echo $general->date_sql_screen($dataMov['fecha_des'],'','es','-');
        ?>
        </td>
        <td align="center">
        <?php
        switch ($GPC['mov']) {
            case 'rec':
                switch ($dataMov['estatus_rec']) {
                    case 1:
                        echo '<img src="../images/reloj.png" width="16" height="16" title=Espera />';
                        break;
                    case 3:
                        echo '<img src="../images/peso1.png" width="16" height="16" title=Romana Lleno />';
                        break;
                    case 4:
                        echo '<img src="../images/reloj.png" width="16" height="16" title=Espera />';
                        break;
                    case 5:
                        echo '<img src="../images/peso1.png" width="16" height="16" title=Cuarentena Tolva />';
                        break;
                    case 6:
                        echo '<img src="../images/peso1.png" width="16" height="16" title=Romana Vacio />';
                        break;
                    case 7:
                        echo '<img src="../images/deshabilitar.png" width="16" height="16" title=Rechazo Central />';
                        break;
                    case 8:
                        echo '<img src="../images/deshabilitar.png" width="16" height="16" title=Rechazo Planta />';
                        break;
                    case 11:
                        echo '<img src="../images/cuarentena.png" width="16" height="16" title=Cuarentena />';
                        echo ' ';
                        echo '<img src="../images/habilitar.png" width="16" height="16" title=Aprobado />';
                        break;
                    case 12:
                        echo '<img src="../images/cuarentena.png" width="16" height="16" title=Cuarentena />';
                        echo ' ';
                        echo '<img src="../images/deshabilitar.png" width="16" height="16" title=Rechazado />';

                }
                break;
            case 'des':
                switch ($dataMov['estatus']) {
                    case '2':
                        echo '<img src="../images/reloj.png" width="16" height="16" title=Espera />';
                        break;
                }
                break;
        }
        ?>
        </td>
        <td align="center">
        <?php 
        switch ($GPC['mov']) {            
            case 'rec':
                switch ($dataMov['estatus_rec']) {
                    case 1:
                    case 4:
                        echo $html->link('<img src="../images/editar.png" width="16" height="16" title=Nuevo>', 'analisis_resultado.php?ac=nuevo&id='.$dataMov['id'].'&cant_muestras='.$dataMov['cant_muestras'].'&id_cosecha='.$dataMov['id_cosecha']);
                        break;
                    case 11:
                    case 12:
                        echo $html->link('<img src="../images/editar.png" width="16" height="16" title=Editar>', 'cuarentena.php?ac=editar&id='.$dataMov['id']); 
                    case 6:
                    break;
                }
            case 'des':
                switch ($dataMov['estatus']) {
                    case 2:                    
                        echo $html->link('<img src="../images/editar.png" width="16" height="16" title=Nuevo>', 'analisis_resultado.php?ac=nuevo&id='.$dataMov['id'].'&cant_muestras='.$dataMov['cant_muestras'].'&id_cosecha='.$dataMov['id_cosecha']); 
                        break;             
                }                
        }
    echo "&nbsp;";
    if ($GPC['mov']=='rec')
        if ($dataMov['estatus_rec']!=1) {
            /*?>            
            <img src="../images/imprimir.png" width="16" height="16" title="Detalle" border="0" style="cursor:pointer" onclick="openWindow('<?php echo DOMAIN_ROOT."reportes/imprimir.php?reporte=boleta_recepcion&id_rec=".$dataMov['id']."&ca=".$dataMov['id_centro_acopio']."&re=true"?>','','1200','500','visible');return false;">
            <?*/
        }
    else
        if ($dataMov['estatus_rec']!=1) {
            echo $html->link('<img src="../images/imprimir.png" width="16" height="16" title=Re-impirmir>', 'cuarentena.php?ac=editar&id='.$dataMov['id']);
        }
    ?>
        </td>
    </tr>
    <?php    
    $i++;
    }
    ?>
    <tr>
        <td colspan="4">&nbsp;</td>
    </tr>
</table>
<div id="paginador">
    <?php
        $paginador->print_page_counter('Pag', 'de');
        echo "&nbsp;&nbsp;";
        $paginador->print_paginator('pulldown');
    ?>
</div>
<?php    require('../lib/common/footer.php');
?>
