<?
    require_once('../lib/core.lib.php');
    
    $despacho = new Despacho();
    $centro_acopio = new CentroAcopio();
    $programa = new Programa();
    
    if($_SESSION['s_perfil_id'] == GERENTEG)
        $idCA = (!empty($GPC['id_ca'])) ? $GPC['id_ca'] : null;
    else
        $idCA = $_SESSION['s_ca_id'];
    
    $listadoProgramas = $programa->buscarProgramaCA(null, $idCA);
    foreach($listadoProgramas as $valor){
        $listadoP[$valor['id']] = $valor['nombre'];
    }
    
    $listaCA = $centro_acopio->find('', '', array('id', 'nombre'), 'list', 'id');
    unset($listaCA[1]);
    
    $listadoE = array(1 => 'Lab. Central', 
                            'Cuarentena Central', 
                            'Romana Lleno', 
                            'Lab. Planta', 
                            'Cuarentena Planta', 
                            'Romana Vac&iacute;o', 
                            'Rechazo Central', 
                            'Rechazo Planta', 
                            'Recibido');

    $fdesde = (!empty($GPC['fecha_inicio'])) ? $general->fecha_normal_sql($GPC['fecha_inicio'], 'es') : date('Y-m-d');
    $fhasta = (!empty($GPC['fecha_fin'])) ? $general->fecha_normal_sql($GPC['fecha_fin'], 'es') : date('Y-m-d');
    
    $porPagina = MAX_RESULTS_PAG;
    $inicio = ($GPC['pg']) ? (($GPC['pg'] * $porPagina) - $porPagina) : 0;
    
    //$listadoRecepciones = $despacho->recepcionesReporteGeneral($fdesde, $fhasta, $idCA, $idCo);
    
    $total_registros = $despacho->total_verdadero;
    $paginador = new paginator($total_registros, $porPagina);
    
    switch($GPC['ac']){
        case 'Pdf':
            $idCA = (!empty($idCA)) ? $idCA : 'vacio';
            $idCo = (!empty($idCo)) ? $idCo : 'vacio';
            header('location: pdf_listado_recepciones_todo.php?id='.$fdesde."_".$fhasta."_".$idCA."_".$idCo);
            die();
        break;
        case 'Excel':
            
        break;
    }
    
    require('../lib/common/header.php');
    require('../lib/common/init_calendar.php');
?>
<script type="text/javascript">    
    $(document).ready(function(){        
        $('#Regresar').click(function(){
           history.back();
        });
    });
</script>
    <div id="titulo_modulo">
        CONSULTA DEL GERENTE - DESPACHO<br/><hr/>
    </div>
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
    <div id="filtro">
        <form name="form1" id="form1" method="POST" action="#" enctype="multipart/form-data">
            <table width="100%" border="0">
                <? //if($_SESSION['s_perfil_id'] == GERENTEG){ ?>
                <tr>
                    <td width="100">Centro de Acopio</td>
                    <td><? echo $html->select('id_ca',array('options'=>$listaCA, 'selected' => $GPC['id_ca'], 'default' => 'Todos', 'class' => 'inputGrilla')); ?></td>
                </tr>
                <? //} ?>
                <tr>
                    <td>Despacho</td>
                    <td><? echo $html->input('despacho', $GPC['despacho'], array('type' => 'text', 'class' => 'inputGrilla')); ?></td>
                    <td width="120">Orden</td>
                    <td><? echo $html->input('orden', $GPC['orden'], array('type' => 'text', 'class' => 'inputGrilla')); ?></td>
                </tr>
                <tr>
                    <td>Cliente</td>
                    <td><? echo $html->input('Cliente', $GPC['productor'], array('type' => 'text', 'class' => 'inputGrilla')); ?></td>
                    <td>Cultivo</td>
                    <td><? echo $html->select('id_cultivo',array('options'=>$listadoP, 'selected' => $GPC['id_programa'], 'default' => 'Todos', 'class' => 'inputGrilla'))?></td>
                </tr>
                <tr>
                    <td>Placa Veh&iacute;culo</td>
                    <td><? echo $html->input('placa', $GPC['placa'], array('type' => 'text', 'class' => 'inputGrilla')); ?></td>
                    <td>Estatus</td>
                    <td><? echo $html->select('estatus',array('options'=>$listadoP, 'selected' => $GPC['estatus'], 'default' => 'Todos', 'class' => 'inputGrilla'))?></td>
                </tr>
                <tr>
                    <td>Fecha de Liq</td>
                    <td width="240">
                        <? echo $html->input('fecha_inicio', $general->date_sql_screen($fdesde, '', 'es', '-'), array('type' => 'text', 'class' => 'inputGrilla', 'readOnly' => true)); ?>
                        <img src="../images/calendario.png" id="fdesde" width="16" height="16" style="cursor:pointer" />
                        <script>
                            Calendar.setup({
                                trigger    : "fdesde",
                                inputField : "fecha_inicio",
                                dateFormat: "%d-%m-%Y",
                                selection: Calendar.dateToInt(<?php echo date("Ymd", strtotime($GPC['fecha_inicio']));?>),
                                onSelect   : function() { this.hide() }
                            });
                        </script>
                    </td>
                    <td width="1">Fecha de Despacho</td>
                    <td>
                        <? echo $html->input('fecha_fin', $general->date_sql_screen($fhasta, '', 'es', '-'), array('type' => 'text', 'class' => 'inputGrilla', 'readOnly' => true)); ?>
                        <img src="../images/calendario.png" id="fhasta" width="16" height="16" style="cursor:pointer" />
                        <script>
                            Calendar.setup({
                                trigger    : "fhasta",
                                inputField : "fecha_fin",
                                dateFormat: "%d-%m-%Y",
                                selection: Calendar.dateToInt(<?php echo date("Ymd", strtotime($GPC['fecha_fin']));?>),
                                onSelect   : function() { this.hide() }
                            });
                        </script>
                    </td>
                </tr>
                <tr id="botones">
                    <td colspan="4" style="padding-top: 20px;">
                        <?
                            echo $html->input('ac', 'Buscar', array('type' => 'submit'));
                            echo $html->input('ac', 'Excel', array('type' => 'submit'));
                            echo $html->input('ac', 'Pdf', array('type' => 'submit'));
                            echo $html->input('Regresar', 'Regresar', array('type' => 'button', 'onClick' => 'regresar();'));
                        ?>
                    </td>
                </tr>
            </table>
        </form>
    </div><hr/>
    <div id="paginador">
        <?
            $paginador->print_page_counter('Pag', 'de');
            echo "&nbsp;&nbsp;";
            $paginador->print_paginator('pulldown');
        ?>
    </div>
    <table align="center" width="100%">
        <tr align="center" class="titulos_tabla">
            <th>Cosecha</th>
            <th>Cultivo</th>
            <th>Cedula/Rif Productor</th>
            <th>Productor</th>
            <th>Accion</th>
        </tr>
        <?
            $i=0;
            $totalPesoBruto = 0;
            $totalPesoTara = 0;
            $totalPesoNeto = 0;
            $totalPesoAcon = 0;
            $idCA = (!empty($idCA)) ? "_$idCA" : '';
            foreach($listadoRecepciones as $dataRecepcion){
                $clase = $general->obtenerClaseFila($i);
                $pesoBruto = $dataRecepcion['peso_01l'] + $dataRecepcion['peso_02l'];
                $pesoTara = $dataRecepcion['peso_01v'] + $dataRecepcion['peso_02v'];
                $pesoNeto = $pesoBruto - $pesoTara;
                $pesoAcon = ($pesoNeto - ($dataRecepcion['humedad_des'] + $dataRecepcion['impureza_des']));

                $totalPesoBruto += $pesoBruto;
                $totalPesoTara += $pesoTara;
                $totalPesoNeto += $pesoNeto;
                $totalPesoAcon += $pesoAcon;
        ?>
        <tr class="<?=$clase?>">
            <td align="center"><?=$dataRecepcion['cosecha']?></td>
            <td align="center"><?=$dataRecepcion['cultivo']?></td>
            <td align="center"><?=$dataRecepcion['ced_rif']?></td>
            <td align="center"><?=$dataRecepcion['productor']?></td>
            
            <td align="center">
                <?
                    $urls = array(3 => '../reportes/pdf_listado_recepciones_individual.php?id='.$dataRecepcion['id'].'_'.$dataRecepcion['id_co'].'_'.$fdesde.'_'.$fhasta.$idCA);
                    $general->crearAcciones($acciones, $urls);
                ?>
            </td>
        </tr>
        <? $i++; } ?>
        <tr>
            
            <td colspan="6">&nbsp;</td>
        </tr>
    </table>
    <div id="paginador">
        <?
            $paginador->print_page_counter('Pag', 'de');
            echo "&nbsp;&nbsp;";
            $paginador->print_paginator('pulldown');
        ?>
    </div>
<?
    require('../lib/common/footer.php');
?>