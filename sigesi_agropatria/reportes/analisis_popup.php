<?
    require_once('../lib/core.lib.php');
    require('../lib/common/header_popup.php');
    
    $id_rec = $GPC['id_rec'];
    $ca = $GPC['ca'];
    $recepcion = new Recepcion();
    $analisisCul = new AnalisisCultivo();
    $AnalisisRes = new Analisis();
    
    $dataRecepcion = $recepcion->listadoAnalisis($ca, null, $id_rec);
    $listadoAnalisis = $AnalisisRes->buscarAC(null, $dataRecepcion[0]['id_cultivo'], $ca);
    $data = $AnalisisRes->listadoResultados($id_rec);
    
    $numero = "R".$dataRecepcion[0]['numero']."-".$general->date_sql_screen($dataRecepcion[0]['fecha_recepcion'], '', 'es', '');
?>
<script language="javascript">
    $(document).ready(function(){
        $('#Cerrar').click(function(){
            ventana = window.self;
            ventana.opener = window.self;
            ventana.close();
        });
    });
</script>
<table id="tabla_reporte" border="0" width="800">
    <tr>
        <td id="titulo_reporte">RESULTADOS DE ANALISIS</td>
    </tr>
    <tr>
        <td>ENTRADA NRO: &nbsp;<?=$numero?></td>
    </tr>
    <tr>
        <td>
            FECHA DE ENTRADA: &nbsp;<?=$general->date_sql_screen($dataRecepcion[0]['fecha_recepcion'],'','es','-')?>&nbsp;&nbsp;&nbsp;
            HORA DE ENTRADA:&nbsp;<?=$general->hora_sql_normal($dataRecepcion[0]['fecha_recepcion'])?></td>
    </tr>
    <tr>
        <td>CARRIL DE MUESTREO:&nbsp;<?=$dataRecepcion[0]['carril']?></td>
    </tr>
    <tr>
        <td>CULTIVO:&nbsp;<?="( ".$dataRecepcion[0]['codigo_cul'].") ".$dataRecepcion[0]['nombre_cul']?></td>
    </tr>
</table>
<table id="tabla_reporte2" width="800" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <th style="border-top: #000000 1px dashed; border-bottom: #000000 1px dashed;">C&Oacute;DIGO</th>
        <th align="left" style="border-top: #000000 1px dashed; border-bottom: #000000 1px dashed;">DESCRIPCION</th>
        <? for($i=1;$i<=$dataRecepcion[0]['cant_muestras'];$i++){ ?>
        <th style="border-top: #000000 1px dashed; border-bottom: #000000 1px dashed;">MUESTRA <?=$i?></th>
        <? } ?>
    </tr>
    <? $j = 0; foreach ($listadoAnalisis as $dataAnalisis) { ?>
    <tr>
        <td align="center" id="reporte_fila_separar"><?=$dataAnalisis['codigo'] ?></td>
        <td id="reporte_fila_separar"><?=$dataAnalisis['nombre'] ?></td>
        <? for($i=1;$i<=$dataRecepcion[0]['cant_muestras'];$i++){ ?>            
        <td align="center" id="reporte_fila_separar"><div style="border-bottom: #000000 1px solid; width: 100px; text-align: center;"><? echo $data[$j]['muestra'.$i] = (!empty($data[$j]['muestra'.$i])) ? trim($data[$j]['muestra'.$i]) : $general->caracter(35, '&nbsp;'); ?></div></td>
        <? } ?>
    </tr>
    <? $j++; } ?>
    <tr>
        <td align="center" colspan="4"><? echo $html->input('Cerrar', 'Cerrar', array('type' => 'button')); ?></td>
    </tr>
</table>
<?
    require('../lib/common/footer_popup.php');
?>