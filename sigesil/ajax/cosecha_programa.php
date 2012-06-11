<?php
    require_once('../lib/core.lib.php');
    $cosecha = new Cosecha();
    
    switch ($GPC['ac']){
        case 'programa':
            if(!empty($GPC['id'])){
                $listadoC = $cosecha->buscarCosechaP('', $GPC['id']);
                foreach($listadoC as $valor){
                    $listaC[$valor['id']] = $valor['codigo'];
                }
                
                echo $html->select('Recepcion.id_cosecha',array('options'=>$listaC, 'default' => 'Seleccione'));
            }else{
                echo $html->select('Recepcion.id_cosecha',array('default' => 'Seleccione'));
            }
        break;
        case 'cantidad':
            $recepcion = new Recepcion();
            $cantRecepcion = $recepcion->recepcionesDia($_SESSION['s_ca_id']);
            $numeroRecepcion = ++$cantRecepcion[0]['total'];
            $numeroRecepcion = ($numeroRecepcion < 10) ? '0'.$numeroRecepcion : $numeroRecepcion;
            echo "Entrada Nro R$numeroRecepcion-".date('dmY');
            //echo $html->input('Recepcion.numero', $numeroRecepcion, array('type' => 'hidden', 'class' => 'estilo_campos'));
        break;
        case 'cantidad2':
            $despacho = new Despacho();
            $cantSalida = $despacho->despachosDia($_SESSION['s_ca_id']);
            $numeroSalida = ++$cantSalida[0]['total'];
            $numeroSalida = ($numeroSalida < 10) ? '0'.$numeroSalida : $numeroSalida;
            echo "Salida Nro D$numeroSalida-".date('dmY');
            //echo $html->input('Despacho.numero', $numeroSalida, array('type' => 'hidden', 'class' => 'estilo_campos'));
        break;
        case 'cos_pro':
            $infoProductores = $cosecha->asignarCosechaProductor($GPC['co']);
            foreach($infoProductores as $valor){
                if(!in_array($valor['id_productor'], $productores))
                    $productores[$valor['id_productor']] = $valor['ced_productor']. " - " .$valor['productor'];
            }
            echo $html->select('id_productor', array('options' => $productores, 'default' => 'Seleccione', 'class' => 'estilo_campos2'));
        break;
        case 'cos_aon':
            $productor = new Productor();
            //$infoPro = $productor->find(array('id' => $GPC['cpro']));
            $infoProductores = $cosecha->asignarCosechaProductor($GPC['co'], $GPC['cpro']);
            foreach($infoProductores as $valor){
                if(!empty($valor['id_asociacion'])){
                    if(!in_array($valor['id_asociacion'], $productores))
                        $productores[$valor['id_asociacion']] = $valor['ced_asociacion']. " - " .$valor['asociacion'];
                }else{
                    if(!in_array($valor['id_productor'], $productores))
                        $productores[$valor['id_productor']] = $valor['ced_productor']. " - " .$valor['productor'];
                }
            }
            echo $html->select('id_asociacion', array('options' => $productores, 'default' => 'Seleccione', 'class' => 'estilo_campos2'));
        break;
        case 'cos_ado':
            $productor = new Productor();
            $infoProductores = $cosecha->asignarCosechaProductor($GPC['co'], $GPC['cpro'], $GPC['caon']);
            echo "<tr align='center' class='titulos_tabla'><th>&nbsp;</th><th>C&eacute;dula/Rif Asociaci&oacute;n</th><th>Nombre Asociaci&oacute;n</th><th>C&eacute;dula/Rif</th><th>Nombre</th></tr>";
            $i=0;
            foreach($infoProductores as $valor){
                $clase = $general->obtenerClaseFila($i);
                if(!empty($valor['id_asociado'])){
                    if($valor['id_cosecha'] == $GPC['co'] || empty($valor['id_cosecha'])){
                        echo "<tr class='$clase'><td align='center' width='30'>".$html->input('id_asociado[]', $valor['id_asociado'], array('type' => 'checkbox', 'checked' => 'checked'))."</td>";
                        echo "<td align='center' width='100'>".$valor['ced_asociacion']."</td>";
                        echo "<td>".$valor['asociacion']."</td>";
                        echo "<td align='center' width='100'>".$valor['ced_asociado']."</td>";
                        echo "<td>".$valor['asociado']."</td></tr>";
                        $i++;
                    }
                }elseif($valor['id_productor'] != $GPC['caon']){
                    echo "<tr class='$clase'><td align='center' width='30'>".$html->input('id_asociado[]', $valor['id_productor'], array('type' => 'checkbox'))."</td>";
                    echo "<td align='center' width='100'>-</td>";
                    echo "<td align='center'>-</td>";
                    echo "<td align='center' width='100'>".$valor['ced_productor']."</td>";
                    echo "<td>".$valor['productor']."</td></tr>";
                    $i++;
                }
            }
        break;
    }
?>
<script type="text/javascript">
    hide_div_loader();
</script>