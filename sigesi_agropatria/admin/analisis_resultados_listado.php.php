<?php
require_once('../lib/core.lib.php');

$Rec=new Recepcion();

//$listaEstatus = array(1 => 'Laboratorio', 2 => '40tna. lab', 3 => 'Roma. Lleno', 4 => 'Tolvas', 5 => '40tna. Tolva', 6 => 'Roma. Vacio', 7 => 'Rch.Lab.Cen', 8 => 'Rch.L.Plant.', 9 => 'Acondicionado');
$listaEstatus = array(1 => 'Laboratorio', 2 => '40tna. lab', 4 => 'Tolvas', 5 => '40tna. Tolva');
$listaM = array('R' => 'Recepci&oacute;n', 'D' => 'Despacho');

if($_SESSION['s_perfil_id'] == GERENTEG)
    $idCA = (!empty($GPC['id_ca'])) ? $GPC['id_ca'] : null;
else
    $idCA = $_SESSION['s_ca_id'];

$listadoRec=$Rec->listadoAnalisis($idCA,null,null,$GPC['estatus']);

require('../lib/common/header.php');
?>
    <div id="titulo_modulo">
        RESULTADOS DE ANALISIS<br/><hr/>
    </div>
    <div id="filtro">
        <form name="form1" id="form1" method="GET" action="" enctype="multipart/form-data">
            <table width="100%">
                <tr>
                    <td width="110">Condici&oacute;n:</td>
                    <td colspan="2">
                        <?
                            echo $html->select('estatus',array('options'=>$listaM, 'selected' => $GPC['estatus'], 'default' => 'Seleccione'));
                            echo $html->input('Buscar', 'Buscar', array('type' => 'submit'));
                        ?>
                    </td>
                </tr>
            </table>
        </form>
    </div><hr/>
    <div id="mensajes">
        <?
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
    <table align="center" width="100%">
        <tr align="center" class="titulos_tabla">            
            <th>Centro de Acopio</th>            
            <th>Entrada Nro</th>
            <th>C&oacute;digo de Cultivo</th> 
            <th>Fecha</th>            
            <th>Acci&oacute;n</th>
            <th>Condici&oacute;n</th>
        </tr>
        <?
            $i=0;
            foreach($listadoRec as $dataREC){
            $clase = $general->obtenerClaseFila($i);
        ?>
        <tr class="<?=$clase?>">
            <td align="center"><?=$dataREC['codigo_ca'].' - '.$dataREC['nombre_ca']?></td>
            <td align="center"><?=$dataREC['numero']?></td>
            <td><?=$dataREC['codigo_cul'].' - '.$dataREC['nombre_cul']?></td>
            <td align="center"><?=$general->date_sql_screen($dataREC['fecha_recepcion'],false,'es','-')?></td>
            <td align="center">
                <?
                    switch ($dataREC['estatus_rec']) {
                        case '1':
                            echo $html->link('<img src="../images/editar.png" width="16" height="16" title=Nuevo>', 'analisis_recepcion.php?ac=nuevo&id_rec='.$dataREC['id_rec'].'&cant_muestras='.$dataREC['cant_muestras'].'&id_cultivo='.$dataREC['codigo_cul']);
                            break;
                        case '2':                            
                            echo $html->link('<img src="../images/editar.png" width="16" height="16" title=Nuevo>', 'cuarentena.php?ac=editar&id='.$dataREC['id_rec']);
                            break;
                    }
                ?>
            </td>
            <td align="center">
                <? 
                    switch ($dataREC['estatus_rec']) 
                    {
                        case '1':
                            echo '<img src="../images/deshabilitar.png" width="16" height="16" title="Pendiente">';
                            break;
                        case '2':
                            echo '<img src="../images/cuarentena.png" width="16" height="16" title="Curentena">';
                            break;
                        case '3':
                            echo '<img src="../images/deshabilitar.png" width="16" height="16" title="Cuarentena">';
                            break;
                    }
                ?>
                
            </td>
        </tr>
        <? $i++; } ?>
        <tr>            
            <td colspan="4">&nbsp;</td>
        </tr>
    </table>
<?
    require('../lib/common/footer.php');
?>