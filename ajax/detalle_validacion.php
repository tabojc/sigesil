<?php
    require_once('../lib/core.lib.php');
    $listaNacion = array('V' => 'V', 'E' => 'E', 'J' => 'J', 'G' => 'G');
    
    switch ($GPC['ac']){
        case 'productor':
            $productor = new Productor();
            $infoProductor = $productor->find(array('ced_rif' => $GPC['ced']));
            if(!empty($infoProductor)){
                echo $html->select('nacionalidad',array('options'=>$listaNacion));
                echo "&nbsp;".$html->input('Productor.ced_rif', null, array('type' => 'text', 'length' => '10', 'style' => 'width: 151px', 'class' => 'crproductor positive'));
                ?>
                <script type="text/javascript">
                    $(document).ready(function(){
                        alert('Cedula/Rif ya Existe');
                    });
                </script>
                <?php
            }else{
                echo $html->select('nacionalidad',array('options'=>$listaNacion, 'selected' => substr(trim($GPC['ced']), 0, 1)));
                echo "&nbsp;".$html->input('Productor.ced_rif', substr(trim($GPC['ced']), 1), array('type' => 'text', 'length' => '10', 'style' => 'width: 151px', 'class' => 'crproductor positive'));
            }
        break;
        case 'cliente':
            $cliente = new Cliente();
            $infoCliente = $cliente->find(array('ced_rif' => $GPC['ced']));
            if(!empty($infoCliente)){
                echo $html->select('nacionalidad',array('options'=>$listaNacion));
                echo "&nbsp;".$html->input('Cliente.ced_rif', null, array('type' => 'text', 'length' => '10', 'style' => 'width: 151px', 'class' => 'crproductor positive'));
                ?>
                <script type="text/javascript">
                    $(document).ready(function(){
                        alert('Cedula/Rif ya Existe');
                    });
                </script>
                <?php
            }else{
                echo $html->select('nacionalidad',array('options'=>$listaNacion, 'selected' => substr(trim($GPC['ced']), 0, 1)));
                echo "&nbsp;".$html->input('Cliente.ced_rif', substr(trim($GPC['ced']), 1), array('type' => 'text', 'length' => '10', 'style' => 'width: 151px', 'class' => 'crproductor positive'));
            }
        break;
        case 'chofer':
            $chofer = new Chofer();
            $infoChofer = $chofer->find(array('ced_rif' => $GPC['ced']));
            if(!empty($infoChofer)){
                echo $html->select('nacionalidad',array('options'=>$listaNacion));
                echo "&nbsp;".$html->input('Chofer.ced_rif', null, array('type' => 'text', 'length' => '10', 'style' => 'width: 151px', 'class' => 'crproductor positive'));
                ?>
                <script type="text/javascript">
                    $(document).ready(function(){
                        alert('Cedula/Rif ya Existe');
                    });
                </script>
                <?php
            }else{
                echo $html->select('nacionalidad',array('options'=>$listaNacion, 'selected' => substr(trim($GPC['ced']), 0, 1)));
                echo "&nbsp;".$html->input('Chofer.ced_rif', substr(trim($GPC['ced']), 1), array('type' => 'text', 'length' => '10', 'style' => 'width: 151px', 'class' => 'crproductor positive'));
            }
        break;
        case 'usuario':
            $usuario = new Usuario();
            $infoUsuario = $usuario->find(array('cedula' => $GPC['ced']));
            if(!empty($infoUsuario)){
                echo $html->input('Usuario.cedula', null, array('type' => 'text', 'class' => 'estilo_campos positive'));
                ?>
                <script type="text/javascript">
                    $(document).ready(function(){
                        alert('Cedula/Rif ya Existe');
                    });
                </script>
                <?php
            }else{
                echo $html->input('Usuario.cedula', $GPC['ced'], array('type' => 'text', 'class' => 'estilo_campos positive'));;
            }
        break;
        case 'analisis':
            $analisis = new Analisis();
            $GPC['cod'] = ($GPC['cod'] < 10 && strlen($GPC['cod']) == 1) ? '0'.$GPC['cod'] : $GPC['cod'];
            $infoAnalisis = $analisis->find(array('codigo' => $GPC['cod']));
            if(!empty($infoAnalisis)){
                echo $html->input('Analisis.codigo', null, array('type' => 'text', 'class' => 'estilo_campos positive'));
                ?>
                <script type="text/javascript">
                    $(document).ready(function(){
                        alert('Codigo ya Existe');
                    });
                </script>
                <?php
            }else{
                echo $html->input('Analisis.codigo', $GPC['cod'], array('type' => 'text', 'class' => 'estilo_campos positive'));
            }
        break;
        case 'cultivo':
            $cultivo = new Cultivo();
            $GPC['cod'] = ($GPC['cod'] < 10 && strlen($GPC['cod']) == 1) ? '0'.$GPC['cod'] : $GPC['cod'];
            $infoCultivo = $cultivo->find(array('codigo' => $GPC['cod']));
            if(!empty($infoCultivo)){
                echo $html->input('Cultivo.codigo', null, array('type' => 'text', 'class' => 'estilo_campos positive'));
                ?>
                <script type="text/javascript">
                    $(document).ready(function(){
                        alert('Codigo ya Existe');
                    });
                </script>
                <?php
            }else{
                echo $html->input('Cultivo.codigo', $GPC['cod'], array('type' => 'text', 'class' => 'estilo_campos positive'));
            }
        break;
    }
?>