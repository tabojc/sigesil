<?php
    require_once('../lib/core.lib.php');
    
    $centro_acopio = new CentroAcopio();
    $almacen = new Almacen();
    $silos = new Silos();
    
    if($_SESSION['s_perfil_id'] == GERENTEG)
        $idCA = (!empty($GPC['id_ca'])) ? $GPC['id_ca'] : null;
    else
        $idCA = $_SESSION['s_ca_id'];
    
    $centrosA = $centro_acopio->buscarCA($idCA);
    $listaCA = $centro_acopio->find('', '', array('id', 'nombre'), 'list', 'nombre');
    unset($listaCA[1]);
    
    if($GPC['ac'] == 'eliminar'){
        $id = $GPC['id'];
        $almacen->desactivarAL($id, $GPC['estatus']);
        header('location: almacen_listado.php');
        die();
    }
    require('../lib/common/header.php');
?>
<script type="text/javascript">
    function muestraAL(id){
        var idmes='tbodyPN_'+id;
        var imgmes='imgmes_'+id;
        var stylevalues = document.getElementById(idmes).style;

        if(stylevalues.display == 'none'){
            stylevalues.display = '';
            document.getElementById(imgmes).src="../images/menos.png";
        } else {
            stylevalues.display = 'none';
            document.getElementById(imgmes).src="../images/mas.png";
        }
    }
    
    function eliminar(){
        if(confirm('¿Desea Eliminar este Almacen?'))
            return true;
        else
            return false;
    }
    
    $(document).ready(function(){
        $('#Nuevo').click(function(){
           window.location = 'almacen.php';
        });
        
        $('#Regresar').click(function(){
           history.back();
        });
    });
</script>
    <div id="titulo_modulo">
        ALMACENES<br/><hr/>
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
            <table width="100%">
                <?php if($_SESSION['s_perfil_id'] == GERENTEG){ ?>
                <tr>
                    <td width="110">Centro de Acopio </td>
                    <td colspan="2">
                        <?php
                            echo $html->select('id_ca',array('options'=>$listaCA, 'selected' => $GPC['id_ca'], 'default' => 'Todos'));
                        ?>
                    </td>
                </tr>
                <?php } ?>
                <tr id="botones">
                    <td colspan="3">
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
    <table align="center" width="100%">
        <tr align="center" class="titulos_tabla">
            <th>&nbsp;</th>
            <?php if($_SESSION['s_perfil_id'] == GERENTEG){ ?>
            <th>Centro de Acopio</th>
            <?php } ?>
            <th>C&oacute;digo</th>
            <th>Nombre</th>
            <th>Pa&iacute;s</th>
            <th>Estado</th>
            <th>Municipio</th>
            <th>Acci&oacute;n</th>
        </tr>
        <?php
            $i=0;
            foreach($centrosA as $ca){
                $clase = $general->obtenerClaseFila($i);
                $listadoAlmacenes = $almacen->buscarAL('', '', $ca['id']);
        ?>
        <tr class="<?php echo $clase?>">
            <td align="right" width="1">
                <a href="javascript:muestraAL('<?php echo $i?>')"><img src="../images/mas.png" width="16" height="16" id="imgmes_<?php echo $i?>" /></a>
            </td>
            <?php if($_SESSION['s_perfil_id'] == GERENTEG){ ?>
            <td><?php echo $ca['nombre']?></td>
            <?php } ?>
            <td align="center">-</td>
            <td align="center">-</td>
            <td align="center">-</td>
            <td align="center">-</td>
            <td align="center">-</td>
            <td align="center">-</td>
        </tr>
        <tbody id="tbodyPN_<?php echo $i?>" style="display:none">
            <?php foreach($listadoAlmacenes as $dataAlmacen) { ?>
            <tr class="terceraclase">
                <?php if($_SESSION['s_perfil_id'] == GERENTEG){ ?>
                <td colspan="2">&nbsp;</td>
                <?php }else{ ?>
                <td>&nbsp;</td>
                <?php } ?>
                <td align="center"><?php echo $dataAlmacen['codigo']?></td>
                <td><?php echo $dataAlmacen['nombre']?></td>
                <td align="center"><?php echo $dataAlmacen['pais']?></td>
                <td align="center"><?php echo $dataAlmacen['estado']?></td>
                <td align="center"><?php echo $dataAlmacen['municipio']?></td>
                <td align="center">
                    <?php
                        $urls = array(1 => 'almacen.php?ac=editar&id='.$dataAlmacen['id'], 'almacen_listado.php?ac=eliminar&id='.$dataAlmacen['id']."&estatus=f");
                        $general->crearAcciones($acciones, $urls);
                    ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
        <?php
            $i++;
            }
        ?>
        <tr>
            
            <td colspan="5">&nbsp;</td>
        </tr>
    </table>
<?php
    require('../lib/common/footer.php');
?>