<?php
    require_once("../lib/core.lib.php");
    require_once("../lib/common/header_reportes.php");
    if (empty($GPC['id_rec'])) {
        header('location: ../admin/romana_listado.php?mov='.$GPC['mov']);
        die();
    }
    
    $id_rec = $GPC['id_rec'];
    $idCA = $GPC['ca'];
    $recepcion = new Recepcion();
    $despacho = new Despacho();
    
    if($GPC['mov'] == 'rec'){
        $subguias = new Guia();
        $dataMovimiento = $recepcion->listadoRecepcion($id_rec, $idCA);
        $dataSubGuias = $subguias->buscarSubGuias($dataMovimiento[0]['id_guia']);
        $label = 'RECIBIDO';
    }else{
        $subOrdenes = new Orden();
        $dataMovimiento = $despacho->listadoDespacho($id_rec, $idCA);
        $dataSubOrdenes = $subOrdenes->buscarSubOrden($dataMovimiento[0]['id_orden']);
        $label = 'DESPACHADO';
    }

    $pesoBruto = $dataMovimiento[0]['peso_01l']+$dataMovimiento[0]['peso_02l'];
    $pesoTara = $dataMovimiento[0]['peso_01v']+$dataMovimiento[0]['peso_02v'];
    $pesoNeto = ($pesoBruto-$pesoTara);
    
    if(!empty($dataMovimiento[0]['id']) && (in_array($dataMovimiento[0]['estatus_rec'], array(14,9))) || $dataMovimiento[0]['estatus'] == 5){
        if(!empty($GPC['reimprimir'])){
?>
<script type="text/javascript">
    window.print();
    window.close();
</script>
<?php } ?>
<table id="tabla_reporte" border="0" width="800">
    <?php
        if($GPC['mov'] == 'rec'){
            $numero = "R".$dataMovimiento[0]['numero']."-".$general->date_sql_screen($dataMovimiento[0]['fecha_recepcion'], '', 'es', '');
    ?>
    <tr>
        <td id="titulo_reporte" colspan="6">CONSTANCIA DE RECEPCI&Oacute;N</td>
    </tr>
    <tr>
        <td colspan="6">GUIA INSAI: <?php echo $dataMovimiento[0]['numero_guia']?></td>
    </tr>
    <tr>
        <td width="150">ENTRADA Nro:</td>
        <td><?php echo $numero?></td>
        <td align="right">FECHA:</td>
        <td><?php echo $general->date_sql_screen($dataMovimiento[0]['modificado'],'','es','-')?></td>
        <?php if(!empty($dataSubGuias)){ ?>
        <td align="right">SUBGUIAS:</td>
        <td>
            <?php
                foreach($dataSubGuias as $valor){
                    $guiastotal .= $valor['subguia']. " - ";
                }
                    echo substr($guiastotal, 0, -2);
            ?>
        </td>
        <?php } ?>
    </tr>
    <tr>
        <td>COSECHA:</td>
        <td colspan="5"><?php echo $dataMovimiento[0]['cosecha']. " - (" .$dataMovimiento[0]['cultivo_codigo'].") ".$dataMovimiento[0]['cultivo_nombre']?></td>
    </tr>
    <tr>
        <td>PROPIEDAD DE:</td>
        <td colspan="5"><?php echo $dataMovimiento[0]['ced_productor']?></td>
    </tr>
    <tr>
        <td>NOMBRE:</td>
        <td colspan="5"><?php echo $dataMovimiento[0]['productor_nombre']?></td>
    </tr>
    <?php if(!empty($dataMovimiento[0]['ced_asociacion'])){ ?>
    <tr>
        <td>ASOCIACI&Oacute;N:</td>
        <td colspan="5"><?php echo $dataMovimiento[0]['ced_asociacion']. " " .$dataMovimiento[0]['asociacion_nombre']?></td>
    </tr>
    <?php    
        }
        if(!empty($dataMovimiento[0]['ced_asociado'])){
    ?>
    <tr>
        <td>ASOCIADO:</td>
        <td colspan="5"><?php echo $dataMovimiento[0]['ced_asociado']. " " .$dataMovimiento[0]['asociado_nombre']?></td>
    </tr>
    <?php } ?>
    <tr>
        <td>PRODUCTO:</td>
        <td colspan="5"><?php echo $dataMovimiento[0]['cultivo_codigo']. " " .$dataMovimiento[0]['cultivo_nombre']?></td>
    </tr>
    <tr>
        <td>CHOFER:</td>
        <td colspan="5"><?php echo $dataMovimiento[0]['ced_chofer']. " NOMBRE: " .$dataMovimiento[0]['chofer_nombre']?></td>
    </tr>
    <tr>
        <td>
            VEHICULO PLACAS:
        </td>
        <td colspan="5">
            <?php
                $placa = $dataMovimiento[0]['placa'];
                $placa .= (!empty($dataMovimiento[0]['placa_remolques'])) ? " / ".$dataMovimiento[0]['placa_remolques'] : "";
                echo $placa;
            ?>
        </td>
    </tr>
    <?php
        }else{
            $numero = "D".$dataMovimiento[0]['numero']."-".$general->date_sql_screen($dataMovimiento[0]['fecha_des'], '', 'es', '');
    ?>
    <tr>
        <td id="titulo_reporte" colspan="6">GU&Iacute;A DE DESPACHO</td>
    </tr>
    <tr>
        <td width="150">DESPACHO Nro:</td>
        <td><?php echo $numero?></td>
        <td align="right">PRODUCTO:</td>
        <td><?php echo $dataMovimiento[0]['cultivo_codigo']. " " .$dataMovimiento[0]['cultivo_nombre']?></td>
    </tr>
    <tr>
        <td>CLIENTE:</td>
        <td colspan="3"><?php echo $dataMovimiento[0]['ced_cliente']. " " .$dataMovimiento[0]['cliente_nombre']?></td>
    </tr>
    <tr>
        <td>ORDEN DE COMPRA:</td>
        <td><?php echo $dataMovimiento[0]['numero_guia']?></td>
        <td align="right">ORDEN DE CARGA:</td>
        <td>
            <?php
                foreach($dataSubOrdenes as $valor){
                    $ordenestotal .= $valor['numero_orden']. " - ";
                }
                    echo substr($ordenestotal, 0, -2);
            ?>
        </td>
    </tr>
    <tr>
        <td>CHOFER:</td>
        <td colspan="3"><?php echo $dataMovimiento[0]['ced_chofer']. " NOMBRE: " .$dataMovimiento[0]['chofer_nombre']?></td>
    </tr>
    <tr>
        <td>
            VEHICULO PLACAS:
        </td>
        <td colspan="5">
            <?php
                $placa = $dataMovimiento[0]['placa'];
                $placa .= (!empty($dataMovimiento[0]['placa_remolques'])) ? " / ".$dataMovimiento[0]['placa_remolques'] : "";
                echo $placa;
            ?>
        </td>
    </tr>
    <tr>
        <td>PUNTO DE ENTREGA:</td>
        <td colspan="3"><?php echo $dataMovimiento[0]['pto_entrega']?></td>
    </tr>
    <?php } ?>
</table>
<table border="0" width="800" style="padding-top: 20px;">
    <?php //if($GPC['mov'] == 'rec'){
       if(!empty($dataMovimiento[0]['peso_02l'])){ ?>
    <tr>
        <td colspan="3">&nbsp;</td>
        <td>MOTRIZ</td>
        <td>REMOLQUE</td>
        <td>&nbsp;</td>
    </tr>
    <?php } ?>
    <tr>
        <td width="20">&nbsp;</td>
        <td>PESO BRUTO TOTAL Kgrs</td>
        <td>----------------------------------------------------------------------------------------></td>
        <?php if(empty($dataMovimiento[0]['peso_02l'])){ ?>
        <td width="1" align="right"><?php echo $general->formato_numero($pesoBruto, 3);?></td>
        <?php }else{ ?>
        <td width="1" align="right"><?php echo $general->formato_numero($dataMovimiento[0]['peso_01l'], 3);?></td>
        <td width="1" align="right"><?php echo $general->formato_numero($dataMovimiento[0]['peso_02l'], 3);?></td>
        <?php } ?>
        <td width="20">&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>PESO DEL VEHICULO Kgrs</td>
        <td>----------------------------------------------------------------------------------------></td>
        <?php if(empty($dataMovimiento[0]['peso_02l'])){ ?>
        <td width="1" align="right"><?php echo $general->formato_numero($pesoTara, 3);?></td>
        <?php }else{ ?>
        <td width="1" align="right"><?php echo $general->formato_numero($dataMovimiento[0]['peso_01v'], 3);?></td>
        <td width="1" align="right"><?php echo $general->formato_numero($dataMovimiento[0]['peso_02v'], 3);?></td>
        <?php } ?>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>NETO <?php echo $label?> Kgrs</td>
        <td>----------------------------------------------------------------------------------------></td>
        <?php if(empty($dataMovimiento[0]['peso_02l'])){ ?>
        <td width="1" align="right"><?php echo $general->formato_numero($pesoNeto, 3);?></td>
        <?php }else{ ?>
        <td width="1" align="right"><?php echo $general->formato_numero(($dataMovimiento[0]['peso_01l']-$dataMovimiento[0]['peso_01v']), 3);?></td>
        <td width="1" align="right"><?php echo $general->formato_numero(($dataMovimiento[0]['peso_02l']-$dataMovimiento[0]['peso_02v']), 3);?></td>
        <?php } ?>
        <td>&nbsp;</td>
    </tr>
    <?php
        if(empty($dataMovimiento[0]['peso_02l'])){
            $humedad = $general->formato_numero($dataMovimiento[0]['humedad'], 3);
            $impureza = $general->formato_numero($dataMovimiento[0]['impureza'], 3);
        }else{
            $humedad = $general->formato_numero($dataMovimiento[0]['humedad'], 3)."% &nbsp;&nbsp;&nbsp; ".$general->formato_numero($dataMovimiento[0]['humedad2'], 3)."%";
            $impureza = $general->formato_numero($dataMovimiento[0]['impureza'], 3)."% &nbsp;&nbsp;&nbsp; ".$general->formato_numero($dataMovimiento[0]['impureza2'], 3)."%";
        }
        if(!in_array($dataMovimiento[0]['cultivo_codigo'], array(10,12))){
    ?>
    <tr>
        <td>&nbsp;</td>
        <td>DESC. POR HUMEDAD: <?php echo $humedad?>% Kgrs</td>
        <td>----------------------------------------------------------------------------------------></td>
        <?php if(empty($dataMovimiento[0]['peso_02l'])){ ?>
        <td width="1" align="right"><?php echo $general->formato_numero($dataMovimiento[0]['humedad_des'], 3);?></td>
        <?php }else{ ?>
        <td width="1" align="right"><?php echo $general->formato_numero($dataMovimiento[0]['humedad_des'], 3);?></td>
        <td width="1" align="right"><?php echo $general->formato_numero($dataMovimiento[0]['humedad_des2'], 3);?></td>
        <?php } ?>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>DESC. POR IMPUREZAS: <?php echo $impureza;?>% Kgrs</td>
        <td>----------------------------------------------------------------------------------------></td>
        <?php if(empty($dataMovimiento[0]['peso_02l'])){ ?>
        <td width="1" align="right"><?php echo $general->formato_numero($dataMovimiento[0]['impureza_des'], 3);?></td>
        <?php }else{ ?>
        <td width="1" align="right"><?php echo $general->formato_numero($dataMovimiento[0]['impureza_des'], 3);?></td>
        <td width="1" align="right"><?php echo $general->formato_numero($dataMovimiento[0]['impureza_des2'], 3);?></td>
        <?php } ?>
        <td>&nbsp;</td>
    </tr>
    <?php
        }else{
            if($dataMovimiento[0]['humedad'] + $dataMovimiento[0]['impureza'] >= 14)
                $descuento = $pesoNeto - $dataMovimiento[0]['peso_acon'];
            else
                $descuento = 0;
    ?>
    <tr>
        <td>&nbsp;</td>
        <td>DESC. HUM|IMP Kgrs</td>
        <td>----------------------------------------------------------------------------------------></td>
        <?php if(empty($dataMovimiento[0]['peso_02l'])){ ?>
        <td width="1" align="right"><?php echo $general->formato_numero($descuento, 3);?></td>
        <?php
            }else{
                if($dataMovimiento[0]['humedad2'] + $dataMovimiento[0]['impureza2'] >= 14)
                    $descuento2 = ($dataMovimiento[0]['peso_02l']-$dataMovimiento[0]['peso_02v']) - $dataMovimiento[0]['peso_acon2'];
                else
                    $descuento2 = 0;
        ?>
        <td width="1" align="right"><?php echo $general->formato_numero($descuento, 3);?></td>
        <td width="1" align="right"><?php echo $general->formato_numero($descuento2, 3);?></td>
        <?php } ?>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td colspan="3">HUMEDAD: <?php echo $humedad?><br/>IMPUREZA: <?php echo $impureza?></td>
    </tr>
    <?php } ?>
    <tr>
        <td colspan="4">&nbsp;</td>
    </tr>
    <?php if($GPC['mov'] == 'rec' && !empty($dataMovimiento[0]['peso_acon'])){ ?>
    <tr>
        <td>&nbsp;</td>
        <td>PESO ACONDICIONADO Kgrs</td>
        <td>----------------------------------------------------------------------------------------></td>
        <?php if(empty($dataMovimiento[0]['peso_02l'])){ ?>
        <td width="1" align="right"><?php echo $general->formato_numero($dataMovimiento[0]['peso_acon'], 3);?></td>
        <?php }else{ ?>
        <td width="1" align="right"><?php echo $general->formato_numero($dataMovimiento[0]['peso_acon'], 3)?></td>
        <td width="1" align="right"><?php echo $general->formato_numero($dataMovimiento[0]['peso_acon2'], 3)?></td>
        <?php } ?>
        <td>&nbsp;</td>
    </tr>
    <?php } ?>
    <tr>
        <td>&nbsp;</td>
        <td>PESO ACONDICIONADO A LIQUIDAR Kgrs</td>
        <td>----------------------------------------------------------------------------------------></td>
        <?php if(empty($dataMovimiento[0]['peso_02l'])){ ?>
        <td width="1" align="right"><?php echo $general->formato_numero($dataMovimiento[0]['peso_acon_liq'], 3);?></td>
        <?php }else{ ?>
        <td width="1" align="right"><?php echo $general->formato_numero($dataMovimiento[0]['peso_acon_liq'], 3)?></td>
        <td width="1" align="right"><?php echo $general->formato_numero($dataMovimiento[0]['peso_acon_liq2'], 3)?></td>
        <?php } ?>
        <td>&nbsp;</td>
    </tr>
    <?php
        /*}else{
            if(!empty($dataMovimiento[0]['peso_02l'])){
    ?>
    <tr>
        <td colspan="3">&nbsp;</td>
        <td>MOTRIZ</td>
        <td>REMOLQUE</td>
        <td>&nbsp;</td>
    </tr>
    <?php
            }
    ?>
    <tr>
        <td width="20">&nbsp;</td>
        <td>PESO BRUTO TOTAL Kgrs</td>
        <td>----------------------------------------------------------------------------------------></td>
        <?php if(empty($dataMovimiento[0]['peso_02l'])){ ?>
        <td width="1" align="right"><?php echo $general->formato_numero($pesoBruto, 3);?></td>
        <?php }else{ ?>
        <td width="1" align="right"><?php echo $general->formato_numero($dataMovimiento[0]['peso_01l'], 3);?></td>
        <td width="1" align="right"><?php echo $general->formato_numero($dataMovimiento[0]['peso_02l'], 3);?></td>
        <?php } ?>
        <td width="20">&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>PESO DEL VEHICULO Kgrs</td>
        <td>----------------------------------------------------------------------------------------></td>
        <?php if(empty($dataMovimiento[0]['peso_02l'])){ ?>
        <td width="1" align="right"><?php echo $general->formato_numero($pesoTara, 3);?></td>
        <?php }else{ ?>
        <td width="1" align="right"><?php echo $general->formato_numero($dataMovimiento[0]['peso_01v'], 3);?></td>
        <td width="1" align="right"><?php echo $general->formato_numero($dataMovimiento[0]['peso_02v'], 3);?></td>
        <?php } ?>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>NETO DESPACHADO Kgrs</td>
        <td>----------------------------------------------------------------------------------------></td>
        <?php if(empty($dataMovimiento[0]['peso_02l'])){ ?>
        <td width="1" align="right"><?php echo $general->formato_numero($pesoNeto, 3);?></td>
        <?php }else{ ?>
        <td width="1" align="right"><?php echo $general->formato_numero(($dataMovimiento[0]['peso_01l']-$dataMovimiento[0]['peso_01v']), 3);?></td>
        <td width="1" align="right"><?php echo $general->formato_numero(($dataMovimiento[0]['peso_02l']-$dataMovimiento[0]['peso_02v']), 3);?></td>
        <?php } ?>
        <td>&nbsp;</td>
    </tr>
    <?php
        if(empty($dataMovimiento[0]['peso_02l'])){
            $humedad = $general->formato_numero($dataMovimiento[0]['humedad'], 3);
            $impureza = $general->formato_numero($dataMovimiento[0]['impureza'], 3);
        }else{
            $humedad = $general->formato_numero($dataMovimiento[0]['humedad'], 3)."% &nbsp;&nbsp;&nbsp; ".$general->formato_numero($dataMovimiento[0]['humedad2'], 3)."%";
            $impureza = $general->formato_numero($dataMovimiento[0]['impureza'], 3)."% &nbsp;&nbsp;&nbsp; ".$general->formato_numero($dataMovimiento[0]['impureza2'], 3)."%";
        }
        if(!in_array($dataMovimiento[0]['cultivo_codigo'], array(10,12))){
    ?>
    <tr>
        <td>&nbsp;</td>
        <td>DESC. POR HUMEDAD: <?php echo $humedad?>% Kgrs</td>
        <td>----------------------------------------------------------------------------------------></td>
        <?php if(empty($dataMovimiento[0]['peso_02l'])){ ?>
        <td width="1" align="right"><?php echo $general->formato_numero($dataMovimiento[0]['humedad_des'], 3);?></td>
        <?php }else{ ?>
        <td width="1" align="right"><?php echo $general->formato_numero($dataMovimiento[0]['humedad_des'], 3);?></td>
        <td width="1" align="right"><?php echo $general->formato_numero($dataMovimiento[0]['humedad_des2'], 3);?></td>
        <?php } ?>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>DESC. POR IMPUREZAS: <?php echo $impureza;?>% Kgrs</td>
        <td>----------------------------------------------------------------------------------------></td>
        <?php if(empty($dataMovimiento[0]['peso_02l'])){ ?>
        <td width="1" align="right"><?php echo $general->formato_numero($dataMovimiento[0]['impureza_des'], 3);?></td>
        <?php }else{ ?>
        <td width="1" align="right"><?php echo $general->formato_numero($dataMovimiento[0]['impureza_des'], 3);?></td>
        <td width="1" align="right"><?php echo $general->formato_numero($dataMovimiento[0]['impureza_des2'], 3);?></td>
        <?php } ?>
        <td>&nbsp;</td>
    </tr>
    <?php
        }else{
            if($dataMovimiento[0]['humedad'] + $dataMovimiento[0]['impureza'] >= 14)
                $descuento = $pesoNeto - $dataMovimiento[0]['peso_acon'];
            else
                $descuento = 0;
    ?>
    <tr>
        <td>&nbsp;</td>
        <td>DESC. HUM|IMP Kgrs</td>
        <td>----------------------------------------------------------------------------------------></td>
        <?php
            if(empty($dataMovimiento[0]['peso_02l'])){
                if($dataMovimiento[0]['humedad2'] + $dataMovimiento[0]['impureza2'] >= 14)
                    $descuento2 = $pesoNeto - $dataMovimiento[0]['peso_acon2'];
                else
                    $descuento2 = 0;
        ?>
        <td width="1" align="right"><?php echo $general->formato_numero($descuento, 3);?></td>
        <?php }else{ ?>
        <td width="1" align="right"><?php echo $general->formato_numero($descuento, 3);?></td>
        <td width="1" align="right"><?php echo $general->formato_numero($descuento2, 3);?></td>
        <?php } ?>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td colspan="3">HUMEDAD: <?php echo $humedad?><br/>IMPUREZA: <?php echo $impureza?></td>
    </tr>
    <?php } ?>
    <tr>
        <td colspan="4">&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>PESO ACONDICIONADO Kgrs</td>
        <td>----------------------------------------------------------------------------------------></td>
        <?php if(empty($dataMovimiento[0]['peso_02l'])){ ?>
        <td width="1" align="right"><?php echo $general->formato_numero($dataMovimiento[0]['peso_acon_liq'], 3);?></td>
        <?php }else{ ?>
        <td width="1" align="right"><?php echo $general->formato_numero($dataMovimiento[0]['peso_acon_liq'], 3)?></td>
        <td width="1" align="right"><?php echo $general->formato_numero($dataMovimiento[0]['peso_acon_liq2'], 3)?></td>
        <?php } ?>
        <td>&nbsp;</td>
    </tr>
    <?php }*/ ?>
</table>
<table border="0" width="800" style="padding-top: 35px;" class="centrar">
    <tr align="center">
        <td><?php echo str_repeat('_',30)?></td>
        <td><?php echo str_repeat('&nbsp;',30)?></td>
        <td><?php echo str_repeat('_',30)?></td>
    </tr>
    <tr align="center">
        <td>Fiscal</td>
        <td>&nbsp;</td>
        <td>Productor/Conductor</td>
    </tr>
</table>
<?php
    }else{
        header('location: ../admin/recepcion.php');
        die();
    }
    require_once("../lib/common/footer_reportes.php");
?>
