<?php
    require_once('../lib/core.lib.php');
    
    switch ($GPC['ac']){
        case 'tipo':
            if(!empty($GPC['cul'])){
                $analisis = new Analisis();
                $infoAnalisis = $analisis->listaAnalisis('', '', '', '', 't', 't');
                foreach($infoAnalisis as $valor){
                    $listaAnalisis[$valor['codigo']] = $valor['nombre'];
                }
                ?>
                <tr>
                    <td width="110">An&aacute;lisis </td>
                    <td><?php echo $html->select('Formula.id_analisis', array('options' => $listaAnalisis, 'default' => 'Seleccione', 'class' => 'inputGrilla botonera')) ?></td>
                </tr>
                <?php
            }
        break;
        case 'campos':
            $tipo = $GPC['tipo'];
            $condicion = $GPC['cond'];
            if(!empty($tipo) && !empty($condicion)){
                if($tipo == 1 && $condicion == 2){
                    echo "Condici&oacute;n ".$html->input("desde_1", '', array('type' => 'text', 'class' => 'positive rango', 'style' => 'width: 40px'));
                    echo "<&nbsp;&nbsp;".$html->input("hasta_1", '', array('type' => 'text', 'class' => 'positive rango', 'style' => 'width: 40px'))."<br/>";
                }elseif($tipo != 1 && ($condicion == 1 || $condicion == 2)){
                    echo "Condici&oacute;n ".$html->input('otra_condicion_1', '', array('type' => 'text', 'class' => 'otra_con', 'style' => 'width: 100px')).":&nbsp;";
                    echo $html->input('desde_1', '', array('type' => 'text', 'class' => 'positive rango', 'style' => 'width: 40px'));
                    echo "<&nbsp;&nbsp;".$html->input('hasta_1', '', array('type' => 'text', 'class' => 'positive rango', 'style' => 'width: 40px'))."<br/>";
                    echo "Evaluada&nbsp;&nbsp;".$html->input('condicion_eval', '', array('type' => 'text', 'readOnly' => 'readOnly', 'style' => 'width: 100px'));
                }
            }
        break;
        case 'multiple':
            $formulas = new Formulas();
            $parametros->listaParametros(1, 6);
            $formulas->listaFormulas(1, '');
            $nro = $GPC['nro'];
            ?>
            <legend>F&oacute;rmula <?php echo $nro?></legend>
            <table align="center" width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td align="center" style="padding: 10px 0;">
                        <?php
                        foreach($parametros->lista as $parametro){
                            echo $html->input("btn_".$parametro['parametro_llave']."_$nro", $parametro['parametro_llave'], array('type' => 'button', 'class' => 'formula_campos constante'));
                        }
                        foreach($formulas->listaF as $formula){
                            echo $html->input("btn_".$formula['codigo']."_$nro", $formula['codigo'], array('type' => 'button', 'class' => 'btnFormula', 'onClick' => "asignarFormula($nro, '".$formula['formula']."')"));
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td align="left">
                        <?php
                            if(!empty($GPC['tipo']) && $GPC['tipo'] == 2){
                                echo "Condici&oacute;n ".$html->input('otra_condicion_'.$nro, '', array('type' => 'text', 'readOnly' => 'readOnly', 'class' => 'otra_con', 'style' => 'width: 100px')).":&nbsp;";
                                echo $html->input("desde_$nro", '', array('type' => 'text', 'class' => 'positive rango', 'style' => 'width: 40px'));
                                echo "<&nbsp;&nbsp;".$html->input("hasta_$nro", '', array('type' => 'text', 'class' => 'positive rango', 'style' => 'width: 40px'));
                                echo $html->input('formula_exp_'.$nro, $GPC['formula'], array('type' => 'text', 'class' => 'campo_formula form_exp'));
                            }else{
                                echo "Condici&oacute;n ".$html->input("desde_$nro", '', array('type' => 'text', 'class' => 'positive rango', 'style' => 'width: 40px'));
                                echo "<&nbsp;&nbsp;".$html->input("hasta_$nro", '', array('type' => 'text', 'class' => 'positive rango', 'style' => 'width: 40px'))."<br/>";
                                echo $html->input('formula_exp_'.$nro, $GPC['formula'], array('type' => 'text', 'class' => 'campo_formula form_exp'));
                            }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 10px 0;"><hr/></td>
                </tr>
                <tr>
                    <th>Comprobar F&oacute;rmula<br/><br/></th>
                </tr>
                <tr>
                    <td align="center" id="td_<?php echo $nro?>" style="padding: 10px 0;">
                        <?php
                        foreach($parametros->lista as $parametro){
                            echo $parametro['parametro_llave']."&nbsp;&nbsp;";
                            echo $html->input($parametro['parametro_llave']."_$nro", '', array('type' => 'text', 'length' => '7', 'class' => 'cuadricula valores positive'));
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <?php echo $html->input('formula_eval_'.$nro, '', array('type' => 'text', 'readOnly' => true, 'class' => 'campo_formula')); ?>
                    </td>
                </tr>
                <tbody id="resultado_<?php echo $nro?>" class="verif_resul"></tbody>
                <tr align="center">
                    <td style="padding-top: 20px;">
                        <?php echo $html->input('Comprobar_'.$nro, 'Comprobar', array('type' => 'button', 'class' => 'comprobar')); ?>
                    <?php echo $html->input('Recuperar_'.$nro, 'Corregir Valores', array('type' => 'button', 'class' => 'recuperar')); ?>
                    </td>
                </tr>
            </table>
            <script type="text/javascript">
                $('.otra_con').val($('#otra_condicion_1').val());
            </script>
            <?php
        break;
        case 'formu':
            if(!empty($GPC['formula_eval'])){
                $evaluar = new LaMermelex();
                $evaluar->suppress_errors = true;
                if ($evaluar->evaluate('y(x) = ' . $GPC['formula_eval'])) {
                    $total = $evaluar->e("y(0)");
                    if($total < 0)
                        echo "<tr><th class='error' align='center'><h3>El resultado es: " . number_format($total, 2) . "</h3></th></tr>";
                    else
                        echo "<tr><th class='msj_verde' align='center'><h3>El resultado es: " . number_format($total, 2) . "</h3></th></tr>";
                } else {
                    echo "<tr><th class='error' align='center'><h3>No se puede evaluar la funci&oacute;n: " . $evaluar->last_error . "</h3></th></tr>";
                }
            }
        break;
        case 'ejes':
            $recepcion = new Recepcion();
            $aprox = $recepcion->toneladasPorEje($GPC['opc']);
            ?>
            <tr>
                <td>Peso Recomendado</td>
                <td id=""><?php echo $html->input('pesoAprox', $aprox[0]['capacidad'], array('type' => 'text', 'class' => 'estilo_campos2', 'readOnly' => true)); ?></td>
            </tr>
            <?php
        break;
        case 'pesar':
            $romana = new Tolcarom();
            
            $movimiento = split('-', $GPC['status']);
            $infoRomana = $romana->find(array('id' => $movimiento[2]), null, 'parametros');
            $peso=file_get_contents($infoRomana[0]['parametros']);
            //$peso = rand(5000, 50000)."K";
            $peso = ($peso < 0 || $peso == '') ? '0K' : $peso;
            switch($movimiento[0]){
                case 'rec':
                    if($movimiento[1] == 3)
                        echo $html->input('Recepcion.peso_0'.$GPC['boton'].'l', substr($peso, 0, -1), array('type' => 'text', 'readOnly' => true, 'class' => 'estilo_campos2 positive'));
                    elseif(in_array($movimiento[1], array(6, 8))){
                        echo $html->input('Recepcion.peso_0'.$GPC['boton'].'v', substr($peso, 0, -1), array('type' => 'text', 'readOnly' => true, 'class' => 'estilo_campos2 positive verifPeso'));
                        ?>
                            <script type="text/javascript">
                                $(document).ready(function(){
                                    var form = $('#form1').serialize();
                                    $("#resultado").load('../ajax/detalle_formula.php?ac=pesoromana&formula='+form);
                                });
                            </script>
                        <?php
                    }
                break;
                case 'des':
                    if($movimiento[1] == 1)
                        echo $html->input('Recepcion.peso_0'.$GPC['boton'].'v', substr($peso, 0, -1), array('type' => 'text', 'readOnly' => true, 'class' => 'estilo_campos2 positive'));
                    elseif($movimiento[1] == 3){
                        echo $html->input('Recepcion.pesoLleno'.$GPC['boton'], substr($peso, 0, -1), array('type' => 'text', 'readOnly' => true, 'class' => 'estilo_campos2 positive verifPeso'));
                        ?>
                            <script type="text/javascript">
                                $(document).ready(function(){
                                    var form = $('#form1').serialize();
                                    $("#resultado").load('../ajax/detalle_formula.php?ac=pesoromana&formula='+form);
                                });
                            </script>
                        <?php
                    }
                break;
            }
            
            echo $html->input('pesar'.$GPC['boton'], 'PESAR', array('type' => 'button', 'class' => 'capturar'));
        break;
        case 'pesoromana':
            if(!empty($GPC['Recepcion'])){
                $id = split('=', $GPC['formula']);
                $id = $id[1];

                $formula = new Formulas();
                $evaluar = new LaMermelex();
                $resultados = new Analisis();
                $cultivo = new Cultivo();
                $evaluar->suppress_errors = true;
                
                if($GPC['mov'] == 'rec')
                    $for_mov = 1;
                elseif($GPC['mov'] == 'des')
                    $for_mov = 2;
                else
                    $for_mov = 3;
                
                $GPC['cod_cul'] = trim(substr($GPC['cultivo'], 0, 2));
                $infoCultivo = $cultivo->find(array('codigo' => $GPC['cod_cul']), null, 'id');
                $orden = " ORDER BY id_centro_acopio DESC, id_cultivo, id_analisis, id";
                $formulas = $formula->formulaCultivo($_SESSION['s_ca_id'], $infoCultivo[0]['id'], $orden, $for_mov);
                
                if($GPC['mov'] == 'rec'){
                    $recepcion = new Recepcion();
                    $infoMovimiento = $recepcion->find(array('id' => $id));
                    $infoResultados = $resultados->listadoResultados($id, '', '', "'1', '2'");
                }else{
                    $despacho = new Despacho();
                    $infoMovimiento = $despacho->find(array('id' => $id));
                    $infoResultados = $resultados->listadoResultados('', $id, '', "'1', '2'");
                    $descuento = $despacho->despachoDescuento($id);
                }
                
                if(!empty($infoResultados)){
                    foreach($infoResultados as $valor){
                        if($valor['codigo'] == 1){
                            if($infoMovimiento[0]['muestra'] == 0){
                                $sumHum = (!empty($valor['muestra1'])) ? $valor['muestra1'] : 0;
                                $sumHum2 = (!empty($valor['muestra2'])) ? $valor['muestra2'] : 0;
                                //$sumHumT = $sumHum + $sumHum2;
                            }elseif($infoMovimiento[0]['muestra'] == 1){
                                $sumHum = 0;
                                $sumHum2 = (!empty($valor['muestra2'])) ? $valor['muestra2'] : 0;
                                //$sumHumT = (!empty($valor['muestra2'])) ? $valor['muestra2'] : 0;
                            }elseif($infoMovimiento[0]['muestra'] == 2){
                                $sumHum = (!empty($valor['muestra1'])) ? $valor['muestra1'] : 0;
                                $sumHum2 = 0;
                                //$sumHumT = (!empty($valor['muestra1'])) ? $valor['muestra1'] : 0;
                            }
                        }else{
                            if($infoMovimiento[0]['muestra'] == 0){
                                $sumImp = (!empty($valor['muestra1'])) ? $valor['muestra1'] : 0;
                                $sumImp2 = (!empty($valor['muestra2'])) ? $valor['muestra2'] : 0;
                                //$sumImpT = $sumImp + $sumImp2;
                            }elseif($infoMovimiento[0]['muestra'] == 1){
                                $sumImp = 0;
                                $sumImp2 = (!empty($valor['muestra2'])) ? $valor['muestra2'] : 0;
                                //$sumImpT = (!empty($valor['muestra2'])) ? $valor['muestra2'] : 0;
                            }elseif($infoMovimiento[0]['muestra'] == 2){
                                $sumImp = 0;
                                $sumImp2 = (!empty($valor['muestra2'])) ? $valor['muestra2'] : 0;
                                //$sumImpT = (!empty($valor['muestra1'])) ? $valor['muestra1'] : 0;
                            }
                        }
                    }
                }/*else{
                    $sumHumT = 0;
                    $sumImpT = 0;
                }*/

                /*
                //PROMEDIO PARA LA HUMEDAD E IMPUREZA
                if($infoMovimiento[0]['muestra'] == 0){
                    $promHum = $sumHumT/$GPC['cant_m'];
                    $promImp = $sumImpT/$GPC['cant_m'];
                }else{
                    $promHum = $sumHumT;
                    $promImp = $sumImpT;
                    }*/

                //VARIABLES A USAR PARA LOS CALCULOS
                $reservadas = array('PL1', 'PL2', 'PV1', 'PV2', 'HUML', 'IMPL', 'PHUM', 'PIMP');
                $GPC['Recepcion']['pesoLleno2'] = (!empty($GPC['Recepcion']['pesoLleno2'])) ? $GPC['Recepcion']['pesoLleno2'] : 0;
                $GPC['Recepcion']['peso_02v'] = (!empty($GPC['Recepcion']['peso_02v'])) ? $GPC['Recepcion']['peso_02v'] : 0;

                /*if($GPC['mov'] == 'rec')
                    $pesos = array($GPC['Recepcion']['pesoLleno1'], $GPC['Recepcion']['pesoLleno2'], $GPC['Recepcion']['peso_01v'], $GPC['Recepcion']['peso_02v'], $promHum, $promImp);
                else{*/
                    $pesos = array($GPC['Recepcion']['pesoLleno1'], 0, $GPC['Recepcion']['peso_01v'], 0, $sumHum, $sumImp);
                    $pesos2 = array(0, $GPC['Recepcion']['pesoLleno2'], 0, $GPC['Recepcion']['peso_02v'], $sumHum2, $sumImp2);
                //}
                //ALMACENAR FORMULAS EN ARREGLO
                $otra = false;
                foreach($formulas as $valor){
                    if($valor['codigo'] == 'PL12')
                        $formulaAplicar['PL'] = $valor['formula'];
                    elseif($valor['codigo'] == 'PV12')
                        $formulaAplicar['PV'] = $valor['formula'];
                    elseif($valor['codigo'] == 'PN')
                        $formulaAplicar['PN'] = $valor['formula'];
                    elseif($valor['codigo'] == 'PA')
                        $formulaAplicar['PA'] = $valor['formula'];
                    else{
                        if(empty($valor['condicion'])){
                            $humImp[] = $valor['formula'];
                            $humImp2[] = $valor['formula'];
                        }else{
                            if($valor['id_analisis'] == 1){
                                $rango = split('<',$valor['condicion']);
                                if(($sumHum >= $rango[0]) && ($sumHum < $rango[1]))
                                    $humImp[] = $valor['formula'];
                                if(($sumHum2 >= $rango[0]) && ($sumHum2 < $rango[1]))
                                    $humImp2[] = $valor['formula'];
                            }elseif($valor['id_analisis'] == 2){
                                $rango = split('<',$valor['condicion']);
                                if(($sumImp >= $rango[0]) && ($sumImp < $rango[1]))
                                    $humImp[] = $valor['formula'];
                                if(($sumImp2 >= $rango[0]) && ($sumImp2 < $rango[1]))
                                    $humImp2[] = $valor['formula'];
                            }else{
                                $rango = split('=',$valor['condicion']);
                                $rangoEvaluar = split('<', $rango[1]);
                                $calculoFormula = str_replace($reservadas, $pesos, $rango[0]);
                                $calculoFormula2 = str_replace($reservadas, $pesos2, $rango[0]);
                                
                                //FORMULAS MOTRIZ
                                if ($evaluar->evaluate('y(x) = ' . $calculoFormula))
                                    $condicionFormula = $evaluar->e("y(0)");
                                //FORMULAS REMOLQUE
                                if ($evaluar->evaluate('y(x) = ' . $calculoFormula2))
                                    $condicionFormula2 = $evaluar->e("y(0)");
                                
                                if(($condicionFormula >= $rangoEvaluar[0]) && ($condicionFormula <= $rangoEvaluar[1]))
                                    $otraFormula[] = $valor['formula'];
                                else{
                                    $otraFormula[] = $valor['formula'];
                                    $otra = true;
                                }
                                
                                if(($condicionFormula2 >= $rangoEvaluar[0]) && ($condicionFormula2 <= $rangoEvaluar[1]))
                                    $otraFormula2[] = $valor['formula'];
                                else{
                                    $otraFormula2[] = $valor['formula'];
                                    $otra2 = true;
                                }
                            }
                        }
                    }
                }
                if($otra)
                    $otraFormula[1] = $formulaAplicar['PN'];
                
                if($otra2)
                    $otraFormula2[1] = $formulaAplicar['PN'];
                //CALCULO DE LA HUMEDAD Y LA IMPUREZA
                if($descuento[0]['descuento'] == 't' || $GPC['mov'] == 'rec'){
                    if(!empty($humImp)){
                        //CALCULO DE LA HUMEDAD
                        $totalH = str_replace($reservadas, $pesos, $humImp[0]);
                        if ($evaluar->evaluate('y(x) = ' . $totalH))
                            $pesoH = $evaluar->e("y(0)");
                        $pesos[] = $pesoH;

                        //CALCULO DE LA IMPUREZA
                        $totalI = str_replace($reservadas, $pesos, $humImp[1]);
                        if ($evaluar->evaluate('y(x) = ' . $totalI))
                            $pesoI = $evaluar->e("y(0)");
                        $pesos[] = $pesoI;
                    }
                }else{
                    $pesos[] = 0;
                    $pesos[] = 0;
                }
                
                if(!empty($otraFormula)){
                    //PESO ACONDICIONADO Y PESO ACONDICIONADO A LIQUIDAR, PARA EL CASO DE CULTIVOS CON OTRA FORMULACION
                    $totalA = str_replace($reservadas, $pesos, $otraFormula[0]);
                    if ($evaluar->evaluate('y(x) = ' . $totalA))
                    $pesoA = $evaluar->e("y(0)");

                    if(!empty($otraFormula[1])){
                        $totalAl = str_replace($reservadas, $pesos, $otraFormula[1]);
                        if ($evaluar->evaluate('y(x) = ' . $totalAl))
                        $pesoAl = $evaluar->e("y(0)");
                    }
                }else{
                    //PESO ACONDICIONADO A LIQUIDAR, PARA EL CASO DE CULTIVOS CON FORMULACION POR HUMEDAD E IMPUREZAS
                    $totalAl = str_replace($reservadas, $pesos, $formulaAplicar['PA']);
                    if ($evaluar->evaluate('y(x) = ' . $totalAl))
                        $pesoAl = $evaluar->e("y(0)");
                }

                //CALCULO DEL PESO NETO
                $totalN = str_replace($reservadas, $pesos, $formulaAplicar['PN']);
                if ($evaluar->evaluate('y(x) = ' . $totalN))
                    $pesoN = $evaluar->e("y(0)");

                //PESO ACONDICIONADO Y PESO ACONDICIONADO A LIQUIDAR, PARA EL CASO DE CULTIVOS CON OTRA FORMULACION
                $totalA = str_replace($reservadas, $pesos, $otraFormula[0]);
                if ($evaluar->evaluate('y(x) = ' . $totalA))
                    $pesoA = $evaluar->e("y(0)");

                $totalAl = str_replace($reservadas, $pesos, $otraFormula[1]);
                if ($evaluar->evaluate('y(x) = ' . $totalAl))
                    $pesoAl = $evaluar->e("y(0)");

                $movimiento = ($GPC['mov'] == 'rec') ? 'Recibido' : 'Despachado Motriz';
                $label = (!empty($GPC['Recepcion']['pesoLleno2'])) ? 'Motriz' : '';
                
                $pesoA = ($GPC['estatus'] != 8) ? $pesoA : $pesoN;
                $pesoAl = ($GPC['estatus'] != 8) ? $pesoAl : $pesoN;
            ?>
            <tr>
                <td>Peso Neto <?php echo $movimiento?> Kgrs</td>
                <td><?php echo $html->input('pesoRecibido1', $general->formato_numero($pesoN, 3), array('type' => 'text', 'class' => 'estilo_campos', 'readOnly' => true)); ?></td>
            </tr>
            <?php
                if(in_array($infoCultivo[0]['id'], array(10,12))){
                    $suma = $sumHum + $sumImp;
                    $desc1 = ($suma >= 14) ? ($pesoN - $pesoA) : 0;
            ?>
            <tr>
                <td>Desc. Hum | Imp <?php echo $label?> Kgrs</td>
                <td><?php echo $html->input('descuento1', $general->formato_numero($desc1, 3), array('type' => 'text', 'class' => 'estilo_campos', 'readOnly' => true)); ?></td>
            </tr>
            <?php
                    if($GPC['mov'] == 'rec'){
            ?>
            <tr>
                <td>Peso Neto Acondicionado <?php echo $label?> Kgrs</td>
                <td><?php echo $html->input('netoAcondicionado1', $general->formato_numero($pesoA, 3), array('type' => 'text', 'class' => 'estilo_campos', 'readOnly' => true)); ?></td>
            </tr>
            <?php
                    }else{
            ?>
            <tr>
                <td>Peso Neto Acondicionado <?php echo $label?> Kgrs</td>
                <td><?php echo $html->input('netoAcondicionado1', $general->formato_numero($pesoAl, 3), array('type' => 'text', 'class' => 'estilo_campos', 'readOnly' => true)); ?></td>
            </tr>
            <?php
                    }
                }else{
            ?>
            <tr>
                <td>Desc. por Humedad <?php echo $label?> Kgrs</td>
                <td><?php echo $html->input('descHumedad1', $general->formato_numero($pesoH, 3), array('type' => 'text', 'class' => 'estilo_campos', 'readOnly' => true)); ?></td>
            </tr>
            <tr>
                <td>Desc. por Impureza <?php echo $label?> Kgrs</td>
                <td><?php echo $html->input('descImpurezas1', $general->formato_numero($pesoI, 3), array('type' => 'text', 'class' => 'estilo_campos', 'readOnly' => true)); ?></td>
            </tr>
            <?php
                }
                //if($GPC['mov'] == 'rec'){
            ?>
            <tr>
                <td>Peso Neto a Liquidar <?php echo $label?> Kgrs</td>
                <td><?php echo $html->input('netoAcondicionadoL1', $general->formato_numero($pesoAl, 3), array('type' => 'text', 'class' => 'estilo_campos', 'readOnly' => true)); ?></td>
            </tr>
            <?php
                //}
                if(($sumHum2 != '' || $sumImp2 != '' || $GPC['Recepcion']['pesoLleno2'] != '' || $GPC['Recepcion']['peso_02v'] != '')){
                    //CALCULO DE LA HUMEDAD Y LA IMPUREZA
                    if($descuento[0]['descuento'] == 't' || $GPC['mov'] == 'rec'){
                        if(!empty($humImp)){
                            //CALCULO DE LA IMPUREZA
                            $totalH2 = str_replace($reservadas, $pesos2, $humImp2[0]);
                            if ($evaluar->evaluate('y(x) = ' . $totalH2))
                                $pesoH2 = $evaluar->e("y(0)");
                            $pesos2[] = $pesoH2;

                            //CALCULO DE LA IMPUREZA
                            $totalI2 = str_replace($reservadas, $pesos2, $humImp2[1]);
                            if ($evaluar->evaluate('y(x) = ' . $totalI2))
                                $pesoI2 = $evaluar->e("y(0)");
                            $pesos2[] = $pesoI2;
                        }
                    }else{
                        $pesos2[] = 0;
                        $pesos2[] = 0;
                    }

                    if(!empty($otraFormula2)){
                        //PESO ACONDICIONADO Y PESO ACONDICIONADO A LIQUIDAR, PARA EL CASO DE CULTIVOS CON OTRA FORMULACION
                        $totalA2 = str_replace($reservadas, $pesos2, $otraFormula2[0]);
                        if ($evaluar->evaluate('y(x) = ' . $totalA2))
                        $pesoA2 = $evaluar->e("y(0)");

                        if(!empty($otraFormula2[1])){
                            $totalAl2 = str_replace($reservadas, $pesos2, $otraFormula2[1]);
                            if ($evaluar->evaluate('y(x) = ' . $totalAl2))
                            $pesoAl2 = $evaluar->e("y(0)");
                        }
                    }else{
                        //PESO ACONDICIONADO A LIQUIDAR, PARA EL CASO DE CULTIVOS CON FORMULACION POR HUMEDAD E IMPUREZAS
                        $totalAl2 = str_replace($reservadas, $pesos2, $formulaAplicar['PA']);
                        if ($evaluar->evaluate('y(x) = ' . $totalAl2))
                            $pesoAl2 = $evaluar->e("y(0)");
                    }

                    //CALCULO DEL PESO NETO
                    $totalN2 = str_replace($reservadas, $pesos2, $formulaAplicar['PN']);
                    if ($evaluar->evaluate('y(x) = ' . $totalN2))
                        $pesoN2 = $evaluar->e("y(0)");

                    //PESO ACONDICIONADO Y PESO ACONDICIONADO A LIQUIDAR, PARA EL CASO DE CULTIVOS CON OTRA FORMULACION
                    $totalA2 = str_replace($reservadas, $pesos2, $otraFormula2[0]);
                    if ($evaluar->evaluate('y(x) = ' . $totalA2))
                        $pesoA2 = $evaluar->e("y(0)");

                    $totalAl2 = str_replace($reservadas, $pesos2, $otraFormula2[1]);
                    if ($evaluar->evaluate('y(x) = ' . $totalAl2))
                        $pesoAl2 = $evaluar->e("y(0)");
            ?>
            <tr>
                <td>Peso Neto <?php echo $movimiento?> Remolque Kgrs</td>
                <td><?php echo $html->input('pesoRecibido2', $general->formato_numero($pesoN2, 3), array('type' => 'text', 'class' => 'estilo_campos', 'readOnly' => true)); ?></td>
            </tr>
            <?php
                if(in_array($infoCultivo[0]['id'], array(10,12))){
                    $suma2 = $sumHum2 + $sumImp2;
                    $desc2 = ($suma2 >= 14) ? ($pesoN2 - $pesoA2) : 0;
            ?>
            <tr>
                <td>Desc. Hum | Imp Remolque Kgrs</td>
                <td><?php echo $html->input('descuento2', $general->formato_numero($desc2, 3), array('type' => 'text', 'class' => 'estilo_campos', 'readOnly' => true)); ?></td>
            </tr>
            <?php
                    if($GPC['mov'] == 'rec'){
            ?>
            <tr>
                <td>Peso Neto Acondicionado Remolque Kgrs</td>
                <td><?php echo $html->input('netoAcondicionado2', $general->formato_numero($pesoA2, 3), array('type' => 'text', 'class' => 'estilo_campos', 'readOnly' => true)); ?></td>
            </tr>
            <?php
                    }else{
            ?>
            <tr>
                <td>Peso Neto Acondicionado Remolque Kgrs</td>
                <td><?php echo $html->input('netoAcondicionado2', $general->formato_numero($pesoAl2, 3), array('type' => 'text', 'class' => 'estilo_campos', 'readOnly' => true)); ?></td>
            </tr>
            <?php
                    }
                }else{
            ?>
            <tr>
                <td>Desc. por Humedad Remolque Kgrs</td>
                <td><?php echo $html->input('descHumedad2', $general->formato_numero($pesoH2, 3), array('type' => 'text', 'class' => 'estilo_campos', 'readOnly' => true)); ?></td>
            </tr>
            <tr>
                <td>Desc. por Impureza Remolque Kgrs</td>
                <td><?php echo $html->input('descImpurezas2', $general->formato_numero($pesoI2, 3), array('type' => 'text', 'class' => 'estilo_campos', 'readOnly' => true)); ?></td>
            </tr>
            <?php
                }
                   // if($GPC['mov'] == 'rec'){
            ?>
            <tr>
                <td>Peso Neto a Liquidar Remolque Kgrs</td>
                <td><?php echo $html->input('netoAcondicionadoL2', $general->formato_numero($pesoAl2, 3), array('type' => 'text', 'class' => 'estilo_campos', 'readOnly' => true)); ?></td>
            </tr>
            <?php
                    //}
                }   
                //EN CASO DE SER NEGATIVOS LOS RESULTADOS
                if (($pesoN < 0) || ($pesoH < 0) || ($pesoI < 0) || ($pesoAl < 0)){
            ?>
            <tr class="error" align="center">
                <td colspan="2" style="padding-top: 20px;">Favor, verifique los pesos, el calculo no puede ser negativo</td>
            </tr>
            <script type="text/javascript">
                $('#Guardar').attr('disabled', true);
            </script>
            <?php
                }else{
            ?>
            <script type="text/javascript">
                $('#Guardar').removeAttr('disabled');
            </script>
            <?php
                }
            }
        break;
        case 'boleta_v':
            $id = split('=', $GPC['formula']);
            $id = $id[1];
            
            $formula = new Formulas();
            $evaluar = new LaMermelex();
            $evaluar->suppress_errors = true;
            $orden = " ORDER BY id_centro_acopio DESC, id_cultivo, id_analisis, id";
            $formulas = $formula->formulaCultivo($_SESSION['s_ca_id'], $GPC['id_cultivo'], $orden, $id);

            //PROMEDIANDO LOS VALORES DE HUMEDAD E IMPUREZA
            $sumHum = (!empty($GPC['humedad1'])) ? $GPC['humedad1'] : 0;
            $sumHum2 = (!empty($GPC['humedad2'])) ? $GPC['humedad2'] : 0;
            //$promHum = (!empty($GPC['humedad2'])) ? $sumHum/2 : $sumHum;
            $sumImp = (!empty($GPC['impureza1'])) ? $GPC['impureza1'] : 0;
            $sumImp2 = (!empty($GPC['impureza2'])) ? $GPC['impureza2'] : 0;
            //$promImp = (!empty($GPC['impureza2'])) ? $sumImp/2 : $sumImp;

            //VARIABLES A USAR PARA LOS CALCULOS
            $reservadas = array('PL1', 'PL2', 'PV1', 'PV2', 'HUML', 'IMPL', 'PHUM', 'PIMP');
            $GPC['peso_02l'] = (!empty($GPC['peso_02l'])) ? $GPC['peso_02l'] : 0;
            $GPC['peso_02v'] = (!empty($GPC['peso_02v'])) ? $GPC['peso_02v'] : 0;
            
            /*if($id == 1)
                $pesos = array($GPC['peso_01l'], $GPC['peso_02l'], $GPC['peso_01v'], $GPC['peso_02v'], $promHum, $promImp);
            else{*/
                $pesos = array($GPC['peso_01l'], 0, $GPC['peso_01v'], 0, $sumHum, $sumImp);
                $pesos2 = array(0, $GPC['peso_02l'], 0, $GPC['peso_02v'], $sumHum2, $sumImp2);
            //}
            
            //ALMACENAR FORMULAS EN ARREGLO
            $otra = false;
            foreach($formulas as $valor){
                if($valor['codigo'] == 'PL12')
                    $formulaAplicar['PL'] = $valor['formula'];
                elseif($valor['codigo'] == 'PV12')
                    $formulaAplicar['PV'] = $valor['formula'];
                elseif($valor['codigo'] == 'PN')
                    $formulaAplicar['PN'] = $valor['formula'];
                elseif($valor['codigo'] == 'PA')
                    $formulaAplicar['PA'] = $valor['formula'];
                else{
                    if(empty($valor['condicion'])){
                        $humImp[] = $valor['formula'];
                        $humImp2[] = $valor['formula'];
                    }else{
                        if($valor['id_analisis'] == 1){
                            $rango = split('<',$valor['condicion']);
                            if(($sumHum >= $rango[0]) && ($sumHum < $rango[1]))
                                $humImp[] = $valor['formula'];
                            if(($sumHum2 >= $rango[0]) && ($sumHum2 < $rango[1]))
                                $humImp2[] = $valor['formula'];
                        }elseif($valor['id_analisis'] == 2){
                            $rango = split('<',$valor['condicion']);
                            if(($sumImp >= $rango[0]) && ($sumImp < $rango[1]))
                                $humImp[] = $valor['formula'];
                            if(($sumImp2 >= $rango[0]) && ($sumImp2 < $rango[1]))
                                $humImp2[] = $valor['formula'];
                        }else{
                            $rango = split('=',$valor['condicion']);
                            $rangoEvaluar = split('<', $rango[1]);
                            $calculoFormula = str_replace($reservadas, $pesos, $rango[0]);
                            if ($evaluar->evaluate('y(x) = ' . $calculoFormula)){
                                $condicionFormula = $evaluar->e("y(0)");
                            }
                            if(($condicionFormula >= $rangoEvaluar[0]) && ($condicionFormula <= $rangoEvaluar[1]))
                                $otraFormula[] = $valor['formula'];
                            else{
                                $otraFormula[] = $valor['formula'];
                                $otra = true;
                            }
                        }
                    }
                }
            }
            if($otra){
                //$otraFormula[0] = $formulaAplicar['PN'];
                $otraFormula[1] = $formulaAplicar['PN'];
            }

            //CALCULO DE LA HUMEDAD Y LA IMPUREZA
            if(!empty($humImp)){
                //CALCULO DE LA IMPUREZA
                $totalH = str_replace($reservadas, $pesos, $humImp[0]);
                if ($evaluar->evaluate('y(x) = ' . $totalH))
                    $pesoH = $evaluar->e("y(0)");
                $pesos[] = $pesoH;

                //CALCULO DE LA IMPUREZA
                $totalI = str_replace($reservadas, $pesos, $humImp[1]);
                if ($evaluar->evaluate('y(x) = ' . $totalI))
                    $pesoI = $evaluar->e("y(0)");
                $pesos[] = $pesoI;
            }

            if(!empty($otraFormula)){
                //PESO ACONDICIONADO Y PESO ACONDICIONADO A LIQUIDAR, PARA EL CASO DE CULTIVOS CON OTRA FORMULACION
                $totalA = str_replace($reservadas, $pesos, $otraFormula[0]);
                if ($evaluar->evaluate('y(x) = ' . $totalA))
                $pesoA = $evaluar->e("y(0)");
                
                if(!empty($otraFormula[1])){
                    $totalAl = str_replace($reservadas, $pesos, $otraFormula[1]);
                    if ($evaluar->evaluate('y(x) = ' . $totalAl))
                    $pesoAl = $evaluar->e("y(0)");
                }
            }else{
                //PESO ACONDICIONADO A LIQUIDAR, PARA EL CASO DE CULTIVOS CON FORMULACION POR HUMEDAD E IMPUREZAS
                $totalAl = str_replace($reservadas, $pesos, $formulaAplicar['PA']);
                if ($evaluar->evaluate('y(x) = ' . $totalAl))
                    $pesoAl = $evaluar->e("y(0)");
            }
            
            //CALCULO DEL PESO NETO
            $totalN = str_replace($reservadas, $pesos, $formulaAplicar['PN']);
            if ($evaluar->evaluate('y(x) = ' . $totalN))
                $pesoN = $evaluar->e("y(0)");

            //PESO ACONDICIONADO Y PESO ACONDICIONADO A LIQUIDAR, PARA EL CASO DE CULTIVOS CON OTRA FORMULACION
            $totalA = str_replace($reservadas, $pesos, $otraFormula[0]);
            if ($evaluar->evaluate('y(x) = ' . $totalA))
                $pesoA = $evaluar->e("y(0)");

            $totalAl = str_replace($reservadas, $pesos, $otraFormula[1]);
            if ($evaluar->evaluate('y(x) = ' . $totalAl))
                $pesoAl = $evaluar->e("y(0)");
            
            $movimiento = ($id == 1) ? 'Recibido' : 'Despachado Motriz';
            $label = ($id == 1) ? '' : 'Motriz';
            ?>
            <tr>
                <td>Peso Neto <?php echo $movimiento?> Kgrs</td>
                <td><?php echo $html->input('pesoRecibido1', $general->formato_numero($pesoN, 3), array('type' => 'text', 'class' => 'estilo_campos', 'readOnly' => true)); ?></td>
            </tr>
            <?php
                if(in_array($GPC['id_cultivo'], array(10,12))){
                    $suma = $GPC['humedad1'] + $GPC['impureza1'];
                    $desc1 = ($suma >= 14) ? ($pesoN - $pesoA) : 0;
            ?>
            <tr>
                <td>Desc. Hum | Imp <?php echo $label?> Kgrs</td>
                <td><?php echo $html->input('descuento1', $general->formato_numero($desc1, 3), array('type' => 'text', 'class' => 'estilo_campos', 'readOnly' => true)); ?></td>
            </tr>
            <?php
                    if($id == 1){
            ?>
            <tr>
                <td>Peso Neto Acondicionado <?php echo $label?> Kgrs</td>
                <td><?php echo $html->input('netoAcondicionado1', $general->formato_numero($pesoA, 3), array('type' => 'text', 'class' => 'estilo_campos', 'readOnly' => true)); ?></td>
            </tr>
            <?php
                    }else{
            ?>
            <tr>
                <td>Peso Neto Acondicionado <?php echo $label?> Kgrs</td>
                <td><?php echo $html->input('netoAcondicionado1', $general->formato_numero($pesoAl, 3), array('type' => 'text', 'class' => 'estilo_campos', 'readOnly' => true)); ?></td>
            </tr>
            <?php
                    }
                }else{
            ?>
            <tr>
                <td>Desc. por Humedad <?php echo $label?> Kgrs</td>
                <td><?php echo $html->input('descHumedad1', $general->formato_numero($pesoH, 3), array('type' => 'text', 'class' => 'estilo_campos', 'readOnly' => true)); ?></td>
            </tr>
            <tr>
                <td>Desc. por Impureza <?php echo $label?> Kgrs</td>
                <td><?php echo $html->input('descImpurezas1', $general->formato_numero($pesoI, 3), array('type' => 'text', 'class' => 'estilo_campos', 'readOnly' => true)); ?></td>
            </tr>
            <?php
                }
                //if($id == 1){
            ?>
            <tr>
                <td>Peso Neto a Liquidar <?php echo $label?> Kgrs</td>
                <td><?php echo $html->input('netoAcondicionadoL1', $general->formato_numero($pesoAl, 3), array('type' => 'text', 'class' => 'estilo_campos', 'readOnly' => true)); ?></td>
            </tr>
            <?php
                //}
            if(($GPC['humedad2'] != '' || $GPC['impureza2'] != '' || $GPC['peso_02l'] != '' || $GPC['peso_02v'] != '')){
                //CALCULO DE LA HUMEDAD Y LA IMPUREZA
                if(!empty($humImp)){
                    //CALCULO DE LA IMPUREZA
                    $totalH2 = str_replace($reservadas, $pesos2, $humImp2[0]);
                    if ($evaluar->evaluate('y(x) = ' . $totalH2))
                        $pesoH2 = $evaluar->e("y(0)");
                    $pesos2[] = $pesoH2;

                    //CALCULO DE LA IMPUREZA
                    $totalI2 = str_replace($reservadas, $pesos2, $humImp2[1]);
                    if ($evaluar->evaluate('y(x) = ' . $totalI2))
                        $pesoI2 = $evaluar->e("y(0)");
                    $pesos2[] = $pesoI2;
                }

                if(!empty($otraFormula)){
                    //PESO ACONDICIONADO Y PESO ACONDICIONADO A LIQUIDAR, PARA EL CASO DE CULTIVOS CON OTRA FORMULACION
                    $totalA2 = str_replace($reservadas, $pesos2, $otraFormula[0]);
                    if ($evaluar->evaluate('y(x) = ' . $totalA2))
                    $pesoA2 = $evaluar->e("y(0)");

                    if(!empty($otraFormula[1])){
                        $totalAl2 = str_replace($reservadas, $pesos2, $otraFormula[1]);
                        if ($evaluar->evaluate('y(x) = ' . $totalAl2))
                        $pesoAl2 = $evaluar->e("y(0)");
                    }
                }else{
                    //PESO ACONDICIONADO A LIQUIDAR, PARA EL CASO DE CULTIVOS CON FORMULACION POR HUMEDAD E IMPUREZAS
                    $totalAl2 = str_replace($reservadas, $pesos2, $formulaAplicar['PA']);
                    if ($evaluar->evaluate('y(x) = ' . $totalAl2))
                        $pesoAl2 = $evaluar->e("y(0)");
                }

                //CALCULO DEL PESO NETO
                $totalN2 = str_replace($reservadas, $pesos2, $formulaAplicar['PN']);
                if ($evaluar->evaluate('y(x) = ' . $totalN2))
                    $pesoN2 = $evaluar->e("y(0)");

                //PESO ACONDICIONADO Y PESO ACONDICIONADO A LIQUIDAR, PARA EL CASO DE CULTIVOS CON OTRA FORMULACION
                $totalA2 = str_replace($reservadas, $pesos2, $otraFormula[0]);
                if ($evaluar->evaluate('y(x) = ' . $totalA2))
                    $pesoA2 = $evaluar->e("y(0)");

                $totalAl2 = str_replace($reservadas, $pesos2, $otraFormula[1]);
                if ($evaluar->evaluate('y(x) = ' . $totalAl2))
                    $pesoAl2 = $evaluar->e("y(0)");
            ?>
            <tr>
                <td>Peso Neto Despachado Remolque Kgrs</td>
                <td><?php echo $html->input('pesoRecibido2', $general->formato_numero($pesoN2, 3), array('type' => 'text', 'class' => 'estilo_campos', 'readOnly' => true)); ?></td>
            </tr>
            <?php
                if(in_array($GPC['id_cultivo'], array(10,12))){
                    $suma2 = $GPC['humedad2'] + $GPC['impureza2'];
                    $desc2 = ($suma2 >= 14) ? ($pesoN2 - $pesoA2) : 0;
            ?>
            <tr>
                <td>Desc. Hum | Imp Remolque Kgrs</td>
                <td><?php echo $html->input('descuento2', $general->formato_numero($desc2, 3), array('type' => 'text', 'class' => 'estilo_campos', 'readOnly' => true)); ?></td>
            </tr>
            <?php
                    if($id == 1){
            ?>
            <tr>
                <td>Peso Neto Acondicionado Remolque Kgrs</td>
                <td><?php echo $html->input('netoAcondicionado2', $general->formato_numero($pesoA2, 3), array('type' => 'text', 'class' => 'estilo_campos', 'readOnly' => true)); ?></td>
            </tr>
            <?php
                    }else{
            ?>
            <tr>
                <td>Peso Neto Acondicionado Remolque Kgrs</td>
                <td><?php echo $html->input('netoAcondicionado2', $general->formato_numero($pesoAl2, 3), array('type' => 'text', 'class' => 'estilo_campos', 'readOnly' => true)); ?></td>
            </tr>
            <?php
                    }
                }else{
            ?>
            <tr>
                <td>Desc. por Humedad Remolque Kgrs</td>
                <td><?php echo $html->input('descHumedad2', $general->formato_numero($pesoH2, 3), array('type' => 'text', 'class' => 'estilo_campos', 'readOnly' => true)); ?></td>
            </tr>
            <tr>
                <td>Desc. por Impureza Remolque Kgrs</td>
                <td><?php echo $html->input('descImpurezas2', $general->formato_numero($pesoI2, 3), array('type' => 'text', 'class' => 'estilo_campos', 'readOnly' => true)); ?></td>
            </tr>
            <?php
                }
                //if($id == 1){
            ?>
            <tr>
                <td>Peso Neto a Liquidar Remolque Kgrs</td>
                <td><?php echo $html->input('netoAcondicionadoL2', $general->formato_numero($pesoAl2, 3), array('type' => 'text', 'class' => 'estilo_campos', 'readOnly' => true)); ?></td>
            </tr>
            <?php
                //}
            }
            //EN CASO DE SER NEGATIVOS LOS RESULTADOS
            if (($pesoN < 0) || ($pesoH < 0) || ($pesoI < 0) || ($pesoAl < 0)){
            ?>
            <tr class="error" align="center">
                <td colspan="2" style="padding-top: 20px;">Favor, verifique los pesos, el calculo no puede ser negativo</td>
            </tr>
            <?php
                }
        break;
    }
?>
