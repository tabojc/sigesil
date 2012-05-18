<?
    require_once('../lib/core.lib.php');
    
    $recepcion = new Recepcion();
    $centro_acopio = new CentroAcopio();
    $cosecha = new Cosecha();
    
    if($_SESSION['s_perfil_id'] == GERENTEG)
        $idCA = (!empty($GPC['id_ca'])) ? $GPC['id_ca'] : null;
    else
        $idCA = $_SESSION['s_ca_id'];
    
    $listadoCosecha = $cosecha->buscarCosechaP(null, null, null, null, $idCA);
    foreach($listadoCosecha as $valor){
        $listadoC[$valor['id']] = $valor['codigo'];
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
                            'Liquidado');

    $guia = (!empty($GPC['guia'])) ? $GPC['guia'] : '';
    $idCo = (!empty($GPC['id_cosecha'])) ? $GPC['id_cosecha'] : '';
    $entrada = (!empty($GPC['entrada'])) ? $GPC['entrada'] : '';
    $contrato = (!empty($GPC['contrato'])) ? $GPC['contrato'] : '';
    $productor = (!empty($GPC['productor'])) ? $GPC['productor'] : '';
    $placa = (!empty($GPC['placa'])) ? $GPC['placa'] : '';
    $estatus = (!empty($GPC['estatus'])) ? $GPC['estatus'] : '';
    $fliq = (!empty($GPC['fecha_liq'])) ? $general->fecha_normal_sql($GPC['fecha_liq'], 'es') : '';
    $frec = (!empty($GPC['fecha_rec'])) ? $general->fecha_normal_sql($GPC['fecha_rec'], 'es') : '';
    
    $porPagina = MAX_RESULTS_PAG;
    $inicio = ($GPC['pg']) ? (($GPC['pg'] * $porPagina) - $porPagina) : 0;
    
    $orden = ($_SESSION['s_perfil_id'] == GERENTEG) ? ' ORDER BY r.id_centro_acopio, r.creado, numero' : ' ORDER BY r.creado, numero';
    $listadoRecepciones = $recepcion->listadoRecepcion(null, $idCA, $idCo, null, $entrada, $estatus, null, null, $porPagina, $inicio, null, $orden, $contrato, $productor, null, $placa, $fliq, $frec, null, $guia);
    $total_registros = $recepcion->total_verdadero;
    $paginador = new paginator($total_registros, $porPagina);
    
    /*switch($GPC['ac']){
        case 'Pdf':
            $idCA = (!empty($idCA)) ? $idCA : 'vacio';
            $idCo = (!empty($idCo)) ? $idCo : 'vacio';
            header('location: pdf_listado_recepciones_todo.php?id='.$fliq."_".$frec."_".$idCA."_".$idCo);
            die();
        break;
    }*/
    
    if(!empty($GPC['exportar'])){
        $formato = ($GPC['exportar'] == 'Excel') ? 'xlsx' : 'ods';

        $filename_client = "Consulta_Gerente_Recepcion_".date("d_m_Y");
        $filename_server = md5(date("dmYhis").rand());
        if($formato=='xlsx'){
            $filename_client .= '.xlsx';
            $filename_server .= '.xlsx';
        }else{
            $filename_client .= '.ods';
            $filename_server .= '.ods';
        }

        require_once APPROOT.'lib/class/PHPExcel/PHPExcel.php';
        require_once APPROOT.'lib/class/PHPExcel/PHPExcel/IOFactory.php';
        $workbook = new PHPExcel();
        $activeWorksheet = $workbook->getActiveSheet();
        $activeWorksheet->setTitle("General Report");

        $activeWorksheet->getDefaultStyle()->applyFromArray(
            array('font' => array('name' => 'arial',
            'size' => 10))
        );

        $activeWorksheet->getStyle('A1')->applyFromArray(
            array('font' => array('bold' => true,
            'size' => 12,
            'color' => array('rgb' => '8B0000')))
        );
        $activeWorksheet->getStyle('A2')->applyFromArray(
            array('font' => array('bold' => true,
            'size' => 11))
        );
        $activeWorksheet->getStyle('A3')->applyFromArray(
            array('font' => array('bold' => false,
            'size' => 10))
        );
        $activeWorksheet->getStyle('A4')->applyFromArray(
            array('font' => array('bold' => false,
            'size' => 10))
        );

        $arrStyleTitle = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array('rgb' => '04B486')),
            'font' => array('bold' => true,
            'color' => array('argb' => PHPExcel_Style_Color::COLOR_WHITE)),
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));

        $activeWorksheet->getColumnDimension('A')->setWidth(20); //Centro de Acopio
        $activeWorksheet->getColumnDimension('B')->setWidth(20); //Programa
        $activeWorksheet->getColumnDimension('C')->setWidth(15); //Cosecha
        $activeWorksheet->getColumnDimension('D')->setWidth(15); //Cultivo
        $activeWorksheet->getColumnDimension('E')->setWidth(15); //Nro Entrada
        $activeWorksheet->getColumnDimension('F')->setWidth(17); //Fecha Recepcion
        $activeWorksheet->getColumnDimension('G')->setWidth(10); //Estatus
        $activeWorksheet->getColumnDimension('H')->setWidth(15); //Guia
        //$activeWorksheet->getColumnDimension('I')->setWidth(15); //SubGuias
        $activeWorksheet->getColumnDimension('I')->setWidth(20); //Ced/Rif Productor
        $activeWorksheet->getColumnDimension('J')->setWidth(35); //Productor
        $activeWorksheet->getColumnDimension('K')->setWidth(20); //Ced/Rif Asociacion
        $activeWorksheet->getColumnDimension('L')->setWidth(35); //Asociacion
        $activeWorksheet->getColumnDimension('M')->setWidth(20); //Ced/Rif Asociado
        $activeWorksheet->getColumnDimension('N')->setWidth(35); //Asociado
        $activeWorksheet->getColumnDimension('O')->setWidth(15); //Vehiculo
        $activeWorksheet->getColumnDimension('P')->setWidth(15); //Placa Motriz
        $activeWorksheet->getColumnDimension('Q')->setWidth(15); //Placa Remolque
        $activeWorksheet->getColumnDimension('R')->setWidth(20); //Cedula Chofer
        $activeWorksheet->getColumnDimension('S')->setWidth(35); //Chofer
        $activeWorksheet->getColumnDimension('T')->setWidth(20); //Peso Lleno Motriz
        $activeWorksheet->getColumnDimension('U')->setWidth(20); //Peso Lleno Remolque
        $activeWorksheet->getColumnDimension('V')->setWidth(20); //Peso Bruto
        $activeWorksheet->getColumnDimension('W')->setWidth(20); //Peso Vacio Motriz
        $activeWorksheet->getColumnDimension('X')->setWidth(20); //Peso Vacio Remolque
        $activeWorksheet->getColumnDimension('Y')->setWidth(20); //Peso Tara
        $activeWorksheet->getColumnDimension('Z')->setWidth(15); //% Humedad
        $activeWorksheet->getColumnDimension('AA')->setWidth(15); //Humedad Desc
        $activeWorksheet->getColumnDimension('AB')->setWidth(15); //% Impureza
        $activeWorksheet->getColumnDimension('AC')->setWidth(15); //Impureza Desc
        $activeWorksheet->getColumnDimension('AD')->setWidth(20); //Peso Acond
        $activeWorksheet->getColumnDimension('AE')->setWidth(20); //Peso Acon Liq.
        $activeWorksheet->getColumnDimension('AF')->setWidth(17); //Fecha Liquidacion

        //Titulo del reporte
        $activeWorksheet->setCellValue('A1', "AGROPATRIA - SIGESIL");
        $activeWorksheet->setCellValue('A2', "CONSULTA DEL GERENTE - RECEPCION");
        $activeWorksheet->setCellValue('A3', "Fecha: ".date("d-m-Y"));
        $activeWorksheet->setCellValue('A4', "Hora: ".date("h:i"));

        /*indice pata inprimir los titulos de la columnas de reporte*/
        $index_title=6;
        $index_detail=0;
        /*linea de titulo del detalle*/
        $j=0;
            $titulos = array('Centro de Acopio','Programa','Cosecha','Cultivo','Nro Entrada','Fecha Recepcion','Estatus',
                'Guia','Ced/Rif Productor','Productor','Ced/Rif Asociacion','Asociacion',
                'Ced/Rif Asociado', 'Asociado','Vehiculo','Placa Motriz','Placa Remolque','Cedula Chofer',
                'Chofer', 'Peso Lleno Motriz','Peso Lleno Remolque','Peso Bruto','Peso Vacio Motriz','Peso Vacio Remolque',
                'Peso Tara','% Humedad','Humedad Desc','% Impureza','Impureza Desc', 'Peso Acond', 'Peso Acon Liq.', 'Fecha Liquidacion');
        foreach($titulos as $columnTitle){
            $activeWorksheet->setCellValueByColumnAndRow($j, $index_title, $columnTitle);
            $oColumn = $activeWorksheet->getCellByColumnAndRow($j, $index_title)->getColumn();
            $oRow = $activeWorksheet->getCellByColumnAndRow($j, $index_title)->getRow();
            $activeWorksheet->getStyle($oColumn.$oRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);;
            $activeWorksheet->getStyle($oColumn.$oRow)->applyFromArray($arrStyleTitle);
            $j++;
        }

        $fila = 7;
        
        $orden = ($_SESSION['s_perfil_id'] == GERENTEG) ? ' ORDER BY r.id_centro_acopio, r.creado, numero' : ' ORDER BY r.creado, numero';
        $listadoRecepciones = $recepcion->listadoRecepcion(null, $idCA, $idCo, null, $entrada, $estatus, null, null, null, null, null, $orden, $contrato, $productor, null, $placa, $fliq, $frec, null, $guia);
        $totalRegistros = count($listadoRecepciones);
        if(!empty($listadoRecepciones)){
            $registro = 1;
            foreach($listadoRecepciones as $recepcion){
                $valores = array();
                $valores[] = "(".$recepcion['ca_codigo'].") ".$recepcion['centro_acopio'];
                $valores[] = $recepcion['programa'];
                $valores[] = $recepcion['cosecha_codigo'];
                $valores[] = "(".$recepcion['cultivo_codigo'].") ".$recepcion['cultivo_nombre'];
                $numero = ($recepcion['numero'] < 10) ? '0'.$recepcion['numero'] : $recepcion['numero'];
                $numEntrada = "R".$numero.$general->date_sql_screen($recepcion['fecha_recepcion'], '', 'es', null);
                $valores[] = $numEntrada;
                $valores[] = $general->date_sql_screen($recepcion['fecha_recepcion'], '', 'es', '-');
                $valores[] = $general->estatus($recepcion['estatus_rec'], 'rec');
                $valores[] = $recepcion['numero_guia'];
                //$valores[]= $recepcion['subGuias'];
                $valores[] = $recepcion['ced_productor'];
                $valores[] = $recepcion['productor_nombre'];
                $valores[] = $recepcion['ced_asociacion'];
                $valores[] = $recepcion['asociacion_nombre'];
                $valores[] = $recepcion['ced_asociado'];
                $valores[] = $recepcion['asociado_nombre'];
                $valores[] = $recepcion['marca'];
                $valores[] = $recepcion['placa'];
                $valores[] = $recepcion['placa_remolques'];
                $valores[] = $recepcion['ced_chofer'];
                $valores[] = $recepcion['chofer_nombre'];
                $valores[] = (!empty($recepcion['peso_01l'])) ? $recepcion['peso_01l'] : 0;
                $valores[] = (!empty($recepcion['peso_02l'])) ? $recepcion['peso_02l'] : 0;
                $totalL = $recepcion['peso_01l'] + $recepcion['peso_02l'];
                $valores[] = $totalL;
                $valores[] = (!empty($recepcion['peso_01v'])) ? $recepcion['peso_01v'] : 0;
                $valores[] = (!empty($recepcion['peso_02v'])) ? $recepcion['peso_02v'] : o;
                $totalV = $recepcion['peso_01v'] + $recepcion['peso_02v'];
                $valores[] = $totalV;
                $valores[] = (!empty($recepcion['humedad'])) ? $recepcion['humedad'] : 0;
                $valores[] = (!empty($recepcion['humedad_des'])) ? $recepcion['humedad_des'] : 0;
                $valores[] = (!empty($recepcion['impureza'])) ? $recepcion['impureza'] : 0;
                $valores[] = (!empty($recepcion['impureza_des'])) ? $recepcion['impureza_des'] : 0;
                $valores[] = (!empty($recepcion['peso_acon'])) ? $recepcion['peso_acon'] : 0;
                $valores[] = (!empty($recepcion['peso_acon_liq'])) ? $recepcion['peso_acon_liq'] : 0;
                $valores[] = $general->date_sql_screen($recepcion['fecha_v'], '', 'es', '-');

                $columna = 0;
                foreach($valores as $valor){
                    $activeWorksheet->setCellValueByColumnAndRow($columna, $fila, $valor);
                    $columna++;
                }
                $fila++;
                
                //TOTALES POR CENTRO DE ACOPIO
                $totalesCA[0] = 'Total del Centro de Acopio: ';
                $totalesCA[1] += $recepcion['peso_01l'];
                $totalesCA[2] += $recepcion['peso_02l'];
                $totalesCA[3] += $recepcion['peso_01l'] + $recepcion['peso_02l'];
                $totalesCA[4] += $recepcion['peso_01v'];
                $totalesCA[5] += $recepcion['peso_02v'];
                $totalesCA[6] += $recepcion['peso_01v'] + $recepcion['peso_02v'];
                $totalesCA[7] += $recepcion['humedad'];
                $totalesCA[8] += $recepcion['humedad_des'];
                $totalesCA[9] += $recepcion['impureza'];
                $totalesCA[10] += $recepcion['impureza_des'];
                $totalesCA[11] += $recepcion['peso_acon'];
                $totalesCA[12] += $recepcion['peso_acon_liq'];
                
                if(((!empty($caDiferente) && $caDiferente != $recepcion['id_centro_acopio'])) || $registro >= $totalRegistros){
                    $columnaTotalCA = 18;
                    foreach($totalesCA as $valor){
                        $activeWorksheet->setCellValueByColumnAndRow($columnaTotalCA, $fila, $valor);
                        $oColumn = $activeWorksheet->getCellByColumnAndRow($columnaTotalCA, $fila)->getColumn();
                        $oRow = $activeWorksheet->getCellByColumnAndRow($columnaTotalCA, $fila)->getRow();
                        $activeWorksheet->getStyle($oColumn.$oRow)->applyFromArray(array('font' => array('bold' => true, 'size' => 10)));
                        $columnaTotalCA++;
                    }

                    //TOTALES
                    $totales[0] = 'Total: ';
                    $totales[1] += $totalesCA[1];
                    $totales[2] += $totalesCA[2];
                    $totales[3] += $totalesCA[3];
                    $totales[4] += $totalesCA[4];
                    $totales[5] += $totalesCA[5];
                    $totales[6] += $totalesCA[6];
                    $totales[7] += $totalesCA[7];
                    $totales[8] += $totalesCA[8];
                    $totales[9] += $totalesCA[9];
                    $totales[10] += $totalesCA[10];
                    $totales[11] += $totalesCA[11];
                    $totales[12] += $totalesCA[12];
                    
                    $fila++;
                }
                $caDiferente = $recepcion['id_centro_acopio'];
                $registro++;
            }
            $fila++;
            $columnaTotal = 18;
            foreach($totales as $valor){
                $activeWorksheet->setCellValueByColumnAndRow($columnaTotal, $fila, $valor);
                $oColumn = $activeWorksheet->getCellByColumnAndRow($columnaTotal, $fila)->getColumn();
                $oRow = $activeWorksheet->getCellByColumnAndRow($columnaTotal, $fila)->getRow();
                $activeWorksheet->getStyle($oColumn.$oRow)->applyFromArray(array('font' => array('bold' => true, 'size' => 10)));
                $columnaTotal++;
            }
        }
        
        chdir(APPROOT.'temp_files');

        if($formato=='xlsx'){
            $writer_name = 'Excel2007';
        } else {
            $writer_name = 'Excel5';
        }

        $writer = PHPExcel_IOFactory::createWriter($workbook, $writer_name);
        $writer->save($filename_server);
        //system('chmod 777 '.APPROOT.'temp_files/*');
        $descarga = new Descarga(APPROOT.'temp_files/'.$filename_server, $filename_client, $_SERVER['PHP_SELF']);
        //Quitar el true para que no elimine los archivos temporales
        $resultado = $descarga->download_file(true);

        if($resultado!=1){
            header("location: $referer?download_error=1");
        }
        exit;
    }
    
    function array_sum_key($arr, $index = null, $field = null){
        if(!is_array($arr) || sizeof($arr) < 1){
            return 0;
        }
        $ret = 0;
        foreach( $arr as $id => $data ){
            if(isset($index) && $field == null){
                $ret += (isset( $data[$index])) ? $data[$index] : 0;
            }elseif(isset($field)){
                if($data[$field] == $index) $ret++;
            }else{
                $ret += $data;
            }
        }
        return $ret;
    }

    $tLabC = array_sum_key($listadoRecepciones, '1', 'estatus_rec');
    $tCuarentenaC = array_sum_key($listadoRecepciones,'2', 'estatus_rec');
    $tRomanaL = array_sum_key($listadoRecepciones,'3', 'estatus_rec');
    $tLabP = array_sum_key($listadoRecepciones,'4', 'estatus_rec');
    $tCuarentenaP = array_sum_key($listadoRecepciones,'5', 'estatus_rec');
    $tRomanaV = array_sum_key($listadoRecepciones,'6', 'estatus_rec');
    $tRechazo = array_sum_key($listadoRecepciones,'7', 'estatus_rec');
    $tRechazo += array_sum_key($listadoRecepciones,'8', 'estatus_rec');
    $tLiquidado = array_sum_key($listadoRecepciones,'9', 'estatus_rec');
    
    require('../lib/common/header.php');
    require('../lib/common/init_calendar.php');
?>
<script type="text/javascript">
    show_div_loader();
    
    var win = null;
    function openWindow(mypage,myname,w,h,scroll){
        LeftPosition = (screen.width) ? (screen.width-w)/2 : 0;
        TopPosition = (screen.height) ? (screen.height-h)/2 : 0;
        settings ='height='+h+',width='+w+',top='+TopPosition+',left='+LeftPosition+', scrollbars=yes ';
        win = window.open(mypage,myname,settings)
    }
    
    $(document).ready(function(){
        $('#anterior').attr('disabled', true);
        $('#Regresar').click(function(){
           history.back();
        });
        
        $('#anterior').click(function(){
            $('#siguiente').attr('disabled', false);
            var siguiente = $('#campos').val();
            if(siguiente > 8){
                $('td:nth-child('+siguiente+'),th:nth-child('+siguiente+')').hide();
                siguiente++;
                $('td:nth-child('+siguiente+'),th:nth-child('+siguiente+')').hide();
                siguiente = siguiente-3;
                $('#campos').val(siguiente);
                $('td:nth-child('+siguiente+'),th:nth-child('+siguiente+')').show();
                siguiente++;
                $('td:nth-child('+siguiente+'),th:nth-child('+siguiente+')').show();
            }
            if(siguiente == 9) $('#anterior').attr('disabled', true);
        });
        
        $('#siguiente').click(function(){
            $('#anterior').attr('disabled', false);
            var siguiente = $('#campos').val();
            if(siguiente < 18){
                $('td:nth-child('+siguiente+'),th:nth-child('+siguiente+')').hide();
                siguiente++;
                $('td:nth-child('+siguiente+'),th:nth-child('+siguiente+')').hide();
                siguiente++;
                $('td:nth-child('+siguiente+'),th:nth-child('+siguiente+')').show();
                $('#campos').val(siguiente);
                siguiente++;
                $('td:nth-child('+siguiente+'),th:nth-child('+siguiente+')').show();
            }
            if(siguiente == 19) $('#siguiente').attr('disabled', true);
        });
        
        // Funcion que abre los Dialogos
        $('#dialog').dialog({
            autoOpen: false,
            draggable: false,
            closeOnEscape: true,
            width: 250,
            modal: true,
            resizable: false
        });

        // Boton para abir el Dialogo
        $('#leyenda').click(function(){
            $('#dialog').dialog('open');
            return false;
        });
    });
</script>
    <div id="titulo_modulo">
        CONSULTA DEL GERENTE - RECEPCI&Oacute;N<br/><hr/>
    </div>
    <div id="dialog" title="Leyenda de Estatus" style="display: none;">
	<table align="center" border="0" style="font-size: 11px;">
            <tr align="center" class="titulos_tabla">
                <th width="140">Estatus</th>
                <th>Cantidad</th>
            </tr>
            <tr>
                <th style="vertical-align: middle !important">
                    <? echo $html->link('<img src="../images/reloj.png" width="16" height="16">'); ?>
                    Lab. Central
                </th>
                <td align="center"><?=$tLabC?></td>
            </tr>
            <tr>
                <th style="vertical-align: middle !important">
                    <? echo $html->link('<img src="../images/cuarentena.png" width="16" height="16">'); ?>
                    Cuarentena Central
                </th>
                <td align="center"><?=$tCuarentenaC?></td>
            </tr>
            <tr>
                <th style="vertical-align: middle !important">
                    <? echo $html->link('<img src="../images/peso1.png" width="26" height="20">'); ?>
                    Romana Lleno
                </th>
                <td align="center"><?=$tRomanaL?></td>
            </tr>
            <tr>
                <th style="vertical-align: middle !important">
                    <? echo $html->link('<img src="../images/reloj.png" width="16" height="16">'); ?>
                    Lab. Planta
                </th>
                <td align="center"><?=$tLabP?></td>
            </tr>
            <tr>
                <th style="vertical-align: middle !important">
                    <? echo $html->link('<img src="../images/cuarentena.png" width="16" height="16">'); ?>
                    Cuarentena Planta
                </th>
                <td align="center"><?=$tCuarentenaP?></td>
            </tr>
            <tr>
                <th style="vertical-align: middle !important">
                    <? echo $html->link('<img src="../images/peso1.png" width="26" height="20">'); ?>
                    Romana Vac&iacute;o
                </th>
                <td align="center"><?=$tRomanaV?></td>
            </tr>
            <tr>
                <th style="vertical-align: middle !important">
                    <? echo $html->link('<img src="../images/deshabilitar.png" width="16" height="16">'); ?>
                    Rechazados
                </th>
                <td align="center"><?=$tRechazo?></td>
            </tr>
            <tr>
                <th style="vertical-align: middle !important">
                    <? echo $html->link('<img src="../images/habilitar.png" width="16" height="16">'); ?>
                    Liquidados
                </th>
                <td align="center"><?=$tLiquidado?></td>
            </tr>
        </table>
    </div>
    <div id="filtro">
        <form name="form1" id="form1" method="GET" action="#">
            <? echo $html->input('campos', '8', array('type' => 'hidden')); ?>
            <table width="100%" border="0">
                <tr>
                    <? if($_SESSION['s_perfil_id'] == GERENTEG){ ?>
                    <td>Centro de Acopio</td>
                    <td><? echo $html->select('id_ca',array('options'=>$listaCA, 'selected' => $idCA, 'default' => 'Todos', 'class' => 'inputGrilla')); ?></td>
                    <? } ?>
                    <!--td>Agencia</td>
                    <td><? echo $html->select('id_agencia',array('options'=>$listaCA, 'selected' => $idAg, 'default' => 'Todos', 'class' => 'inputGrilla')); ?></td-->
                </tr>
                <tr>
                    <td>Guia</td>
                    <td colspan="3"><? echo $html->input('guia', $guia, array('type' => 'text', 'class' => 'inputGrilla')); ?></td>
                </tr>
                <tr>
                    <td width="125">Entrada</td>
                    <td><? echo $html->input('entrada', $entrada, array('type' => 'text', 'class' => 'inputGrilla')); ?></td>
                    <td width="90">Contrato</td>
                    <td><? echo $html->input('contrato', $contrato, array('type' => 'text', 'class' => 'inputGrilla')); ?></td>
                </tr>
                <tr>
                    <td>Productor</td>
                    <td><? echo $html->input('productor', $productor, array('type' => 'text', 'class' => 'inputGrilla')); ?></td>
                    <td>Cosecha</td>
                    <td><? echo $html->select('id_cosecha',array('options'=>$listadoC, 'selected' => $idCo, 'default' => 'Todas', 'class' => 'inputGrilla'))?></td>
                </tr>
                <tr>
                    <td>Placa Veh&iacute;culo</td>
                    <td><? echo $html->input('placa', $placa, array('type' => 'text', 'class' => 'inputGrilla')); ?></td>
                    <td>Estatus</td>
                    <td><? echo $html->select('estatus',array('options'=>$listadoE, 'selected' => $estatus, 'default' => 'Todos', 'class' => 'inputGrilla'))?></td>
                </tr>
                <tr>
                    <td width="1">Fecha de Recepci&oacute;n</td>
                    <td>
                        <? echo $html->input('fecha_rec', $general->date_sql_screen($frec, '', 'es', '-'), array('type' => 'text', 'class' => 'inputGrilla', 'readOnly' => true)); ?>
                        <img src="../images/calendario.png" id="frec" width="16" height="16" style="cursor:pointer" />
                        <script>
                            Calendar.setup({
                                trigger    : "frec",
                                inputField : "fecha_rec",
                                dateFormat: "%d-%m-%Y",
                                selection: Calendar.dateToInt(<?php echo date("Ymd", strtotime($GPC['fecha_rec']));?>),
                                onSelect   : function() { this.hide() }
                            });
                        </script>
                    </td>
                    <td>Fecha de Liq</td>
                    <td width="240">
                        <? echo $html->input('fecha_liq', $general->date_sql_screen($fliq, '', 'es', '-'), array('type' => 'text', 'class' => 'inputGrilla', 'readOnly' => true)); ?>
                        <img src="../images/calendario.png" id="fliq" width="16" height="16" style="cursor:pointer" />
                        <script>
                            Calendar.setup({
                                trigger    : "fliq",
                                inputField : "fecha_liq",
                                dateFormat: "%d-%m-%Y",
                                selection: Calendar.dateToInt(<?php echo date("Ymd", strtotime($GPC['fecha_liq']));?>),
                                onSelect   : function() { this.hide() }
                            });
                        </script>
                    </td>
                </tr>
                <tr id="botones">
                    <td colspan="4" style="padding-top: 20px;">
                        <?
                            echo $html->input('leyenda', 'Estatus', array('type' => 'button'));
                            echo $html->input('ac', 'Buscar', array('type' => 'submit'));
                            echo $html->input('exportar', 'Excel', array('type' => 'submit'));
                            echo $html->input('exportar', 'Calc', array('type' => 'submit'));
                            //echo $html->input('ac', 'Pdf', array('type' => 'submit'));
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
    <table align="center" width="100%" border="0">
        <tr>
            <th colspan="7"></th>
            <th align="center" colspan="2">
                <? echo $html->input('anterior', '<', array('type' => 'button')); ?>
                <? echo $html->input('siguiente', '>', array('type' => 'button')); ?>
            </th>
        </tr>
        <tr align="center" class="titulos_tabla">
            <th width="100">Entrada Nro</th>
            <th width="1">Cedula/Rif</th>
            <th>Productor</th>
            <th width="1">Guia</th>
            <th width="1">Cosecha</th>
            <th width="1">Cultivo</th>
            <th width="60">Estatus</th>
            <th>Peso Lleno M</th>
            <th>Peso Lleno R</th>
            <th style="display: none;">Peso Vac&iacute;o M</th>
            <th style="display: none;">Peso Vac&iacute;o R</th>
            <th style="display: none;">Peso Bruto</th>
            <th style="display: none;">Tara del Veh&iacute;culo</th>
            <th style="display: none;">Humedad %</th>
            <th style="display: none;">Humedad Desc</th>
            <th style="display: none;">Impureza %</th>
            <th style="display: none;">Impureza Desc</th>
            <th style="display: none;">Peso Acon.</th>
            <th style="display: none;">Peso Acon. Liq.</th>
            <th width="1">Accion</th>
        </tr>
        <?
            $i=0;
            $idCA = (!empty($idCA)) ? "_$idCA" : '';
            
            $totalPesoLM = 0;
            $totalPesoLR = 0;
            $totalPesoVM = 0;
            $totalPesoVR = 0;
            $totalPesoBruto = 0;
            $totalPesoTara = 0;
            $totalH = 0;
            $totalI = 0;
            $totalHD = 0;
            $totalID = 0;
            $totalPesoAcon = 0;
            $totalPesoAconL = 0;
            
            foreach($listadoRecepciones as $dataRecepcion){
                $clase = $general->obtenerClaseFila($i);
                $numero = ($dataRecepcion['numero'] < 10) ? '0'.$dataRecepcion['numero'] : $dataRecepcion['numero'];
                $numEntrada = "R".$numero.$general->date_sql_screen($dataRecepcion['fecha_recepcion'], '', 'es', null);
                $pesoBruto = $dataRecepcion['peso_01l'] + $dataRecepcion['peso_02l'];
                $pesoTara = $dataRecepcion['peso_01v'] + $dataRecepcion['peso_02v'];
                
                $totalPesoLM += $dataRecepcion['peso_01l'];
                $totalPesoLR += $dataRecepcion['peso_02l'];
                $totalPesoVM += $dataRecepcion['peso_01v'];
                $totalPesoVR += $dataRecepcion['peso_02v'];
                $totalPesoBruto += $pesoBruto;
                $totalPesoTara += $pesoTara;
                $totalH += $dataRecepcion['humedad'];
                $totalHD += $dataRecepcion['humedad_des'];
                $totalI += $dataRecepcion['impureza'];
                $totalID += $dataRecepcion['impureza_des'];
                $totalPesoAcon += $dataRecepcion['peso_acon'];
                $totalPesoAconL += $dataRecepcion['peso_acon_liq'];
        ?>
        <tr class="<?=$clase?>">
            <td align="center"><?=$numEntrada?></td>
            <td align="center"><?=$dataRecepcion['ced_productor']?></td>
            <td align="center"><?=$dataRecepcion['productor_nombre']?></td>
            <td align="center"><?=$dataRecepcion['numero_guia']?></td>
            <td align="center"><?=$dataRecepcion['cosecha_codigo']?></td>
            <td align="center"><?=$dataRecepcion['cultivo_codigo']?></td>
            <td align="center">
                <?
                    switch($dataRecepcion['estatus_rec']){
                        case 1:
                            echo $html->link('<img src="../images/reloj.png" width="16" height="16">').' Central';
                        break;
                        case 2:
                        case 10:
                        case 11:
                            echo $html->link('<img src="../images/cuarentena.png" width="16" height="16">').' Central';
                        break;
                        case 3:
                            echo $html->link('<img src="../images/peso1.png" width="16" height="16">').' Lleno';
                        break;
                        case 4:
                            echo $html->link('<img src="../images/reloj.png" width="16" height="16">').' Planta';
                        break;
                        case 5:
                        case 12:
                        case 13:
                            echo $html->link('<img src="../images/cuarentena.png" width="16" height="16">').' Planta';
                        break;
                        case 6:
                            echo $html->link('<img src="../images/peso1.png" width="16" height="16">').' Vac&iacute;o';
                        break;
                        case 7:
                            echo $html->link('<img src="../images/deshabilitar.png" width="16" height="16">').' Central';
                        break;
                        case 8:
                            echo $html->link('<img src="../images/deshabilitar.png" width="16" height="16">');
                        break;
                        case 9:
                            echo $html->link('<img src="../images/habilitar.png" width="16" height="16">');
                        break;
                    }
                    
                    $humedad = (!empty($dataRecepcion['humedad'])) ? $dataRecepcion['humedad'] : 0;
                    $impureza = (!empty($dataRecepcion['impureza'])) ? $dataRecepcion['impureza'] : 0;
                ?>
            </td>
            <td align="right"><?=$general->formato_numero(round($dataRecepcion['peso_01l']), 3)?></td>
            <td align="right"><?=$general->formato_numero(round($dataRecepcion['peso_02l']), 3)?></td>
            <td align="right" style="display: none"><?=$general->formato_numero(round($dataRecepcion['peso_01v']), 3)?></td>
            <td align="right" style="display: none"><?=$general->formato_numero(round($dataRecepcion['peso_02v']), 3)?></td>
            <td align="right" style="display: none"><?=$general->formato_numero(round($pesoBruto), 3)?></td>
            <td align="right" style="display: none"><?=$general->formato_numero(round($pesoTara), 3)?></td>
            <td align="right" style="display: none"><?=$humedad?></td>
            <td align="right" style="display: none"><?=$general->formato_numero(round($dataRecepcion['humedad_des']), 3)?></td>
            <td align="right" style="display: none"><?=$impureza?></td>
            <td align="right" style="display: none"><?=$general->formato_numero(round($dataRecepcion['impureza_des']), 3)?></td>
            <td align="right" style="display: none"><?=$general->formato_numero(round($dataRecepcion['peso_acon']), 3)?></td>
            <td align="right" style="display: none"><?=$general->formato_numero(round($dataRecepcion['peso_acon_liq']), 3)?></td>
            <td align="center">
                <img src="../images/buscar.png" width="16" height="16" title="Detalle" border="0" style="cursor:pointer" onclick="openWindow('gerente_recepcion_detalle.php?id=<?php echo $dataRecepcion['id'] ?>','','1200','500','visible');return false;">
            </td>
        </tr>
        <? $i++; } ?>
        <tr align="right">
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th>Total:</th>
            <th><?=$general->formato_numero(round($totalPesoLM), 3)?></th>
            <th><?=$general->formato_numero(round($totalPesoLR), 3)?></th>
            <th style="display: none"><?=$general->formato_numero(round($totalPesoVM), 3)?></th>
            <th style="display: none"><?=$general->formato_numero(round($totalPesoVR), 3)?></th>
            <th style="display: none"><?=$general->formato_numero(round($totalPesoBruto), 3)?></th>
            <th style="display: none"><?=$general->formato_numero(round($totalPesoTara), 3)?></th>
            <th style="display: none"><?=$general->formato_numero(round($totalH), 3)?></th>
            <th style="display: none"><?=$general->formato_numero(round($totalHD), 3)?></th>
            <th style="display: none"><?=$general->formato_numero(round($totalI), 3)?></th>
            <th style="display: none"><?=$general->formato_numero(round($totalID), 3)?></th>
            <th style="display: none"><?=$general->formato_numero(round($totalPesoAcon), 3)?></th>
            <th style="display: none"><?=$general->formato_numero(round($totalPesoAconL), 3)?></th>
        </tr>
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