<?
require_once('../lib/core.lib.php');

$formulas = new Formulas();
$cultivo = new Cultivo();

$idCA = $_SESSION['s_ca_id'];
$listaCultivos = $cultivo->find('', '', 'id, nombre', 'list', 'codigo');

$listaCondicion = array(1 => 'Si', 2 => 'No');
$listaTipo = array(1 => 'An&aacute;lisis', 2 => 'Otro');
$parametros->listaParametros(1, 6);
$formulas->listaFormulas($idCA);

/*$phrase  = "You should eat fruits, vegetables, and fiber every day.";
$healthy = array("fruits", "vegetables", "fiber");
$yummy   = array("pizza", "beer", "ice cream");
$newphrase = str_replace($healthy, $yummy, $phrase);*/

switch ($GPC['ac']) {
    case 'guardar':
        /*if (!empty($GPC['Cultivo']['id_org']) && !empty($GPC['Cultivo']['codigo']) && !empty($GPC['Cultivo']['nombre'])) {
            $GPC['Cultivo']['tipificado'] = (!empty($GPC['Cultivo']['tipificado'])) ? $GPC['Cultivo']['tipificado'] : 'f';
            $cultivo->save($GPC['Cultivo']);
            $id = $cultivo->id;
        }
        if (!empty($id)) {
            header("location: cultivo_listado.php?msg=exitoso");
            die();
        } else {
            header("location: cultivo_listado.php?msg=error");
            die();
        }*/
        break;
    case 'editar':
        //$infoCultivo = $cultivo->find(array('id' => $GPC['id']));
        break;
}
require('../lib/common/header.php');

$validator = new Validator('form1');
$validator->printIncludes();
$validator->setRules('id_cultivo', array('required' => array('value' => true, 'message' => 'Requerido')));
$validator->setRules('tipo_for', array('required' => array('value' => true, 'message' => 'Requerido')));
$validator->setRules('multiple_cond', array('required' => array('value' => true, 'message' => 'Requerido')));
$validator->setRules('condicion', array('required' => array('value' => true, 'message' => 'Requerido')));
$validator->setRules('codigo', array('required' => array('value' => true, 'message' => 'Requerido')));
$validator->setRules('formula_exp', array('required' => array('value' => true, 'message' => 'Requerido')));
$validator->printScript();
?>
<script type="text/javascript">
    function cancelar(){
        window.location = 'formula_listado.php';
    }

    function asignarFormula(valor){
        var campo = $('#formula_exp').val();
        $('#formula_exp').attr('value', campo+valor);
        $('#formula_eval').attr('value', campo+valor);
        $('#para').attr('disabled', true);
        $('#parc').attr('disabled', false);
        $('input.operador').attr('disabled', false);
        $('input.constante').attr('disabled', true);
        $('input.btnFormula').attr('disabled', true);
    }
    
    $(document).ready(function(){
        $(".positive").numeric({ negative: false }, function() { alert("No negative values"); this.value = ""; this.focus(); });
        $('input.operador').attr('disabled', true);
        
        $('#tipo_for').change(function(){
            var cul = $(this).val();
            if(cul != '')
                $('#opciones').load('../ajax/cosecha_programa.php?ac=formu&cul='+cul);
            else
                $('#opciones').html('');
        });
        
        $('#multiple_cond').change(function(){
            var cond = $(this).val();
            if(cond == 1){
                $('#unica_formula').css('display', 'block');
                $('#multiple').css('display', 'none');
                $('#btnCondicion').css('display', 'none');
            }else if(cond == 2){
                $('#unica_formula').css('display', 'block');
                $('#multiple').css('display', 'block');
                $('#btnCondicion').css('display', 'block');
            }
            else{
                $('#unica_formula').css('display', 'none');
                $('#multiple').css('display', 'none');
                $('#btnCondicion').css('display', 'none');
            }
        });
        
        $('.parentesis').click(function(){
            var valor = $(this).val();
            var campo = $('#formula_exp').val();
            if(valor == '('){
                $('#formula_exp').attr('value', campo+valor);
                $('#formula_eval').attr('value', campo+valor);
                $('#parc').attr('disabled', true);
                $('input.operador').attr('disabled', true);
                $('#res').attr('disabled', false);
                $('#agregar').attr('disabled', false);
                $('#numero').attr('disabled', false);
                $('input.constante').attr('disabled', false);
                $('input.btnFormula').attr('disabled', false);
            }else if (valor == ')' && campo == ''){
                alert('Abra un parentesis o elija una constante');
            }else{
                $('#formula_exp').attr('value', campo+valor);
                $('#formula_eval').attr('value', campo+valor);
                $('input.operador').attr('disabled', false);
                $('#agregar').attr('disabled', true);
                $('#numero').attr('disabled', true);
                $('input.constante').attr('disabled', false);
                $('input.btnFormula').attr('disabled', false);
            }
        });
        
        $('#borrar').click(function(){
            contenido = $('#formula_exp').val();
            $('.valores').attr('value', '');
            $('#formula_exp').attr('value', '');
            $('#formula_eval').attr('value', '');
            $('input.parentesis').attr('disabled', false);
            $('input.operador').attr('disabled', true);
            $('#agregar').attr('disabled', false);
            $('#numero').attr('disabled', false);
            $('input.constante').attr('disabled', false);
            $('input.btnFormula').attr('disabled', false);
            $("#resultado").html('');
        });
        
        $('#agregar').click(function(){
            var valor = $('#numero').val();
            var campo = $('#formula_exp').val();
            if(valor != ''){
                $('#formula_exp').attr('value', campo+valor);
                $('#formula_eval').attr('value', campo+valor);
                $('#para').attr('disabled', true);
                $('#parc').attr('disabled', false);
                $('input.operador').attr('disabled', false);
                $('#agregar').attr('disabled', true);
                $('#numero').val('');
                $('#numero').attr('disabled', true);
                $('input.constante').attr('disabled', true);
                $('input.btnFormula').attr('disabled', true);
            }else{
                alert('Valor Invalido');
            }
        });
        
        $('.constante').click(function(){
            var valor = $(this).val();
            var campo = $('#formula_exp').val();
            $('#formula_exp').attr('value', campo+valor);
            $('#formula_eval').attr('value', campo+valor);
            $('#para').attr('disabled', true);
            $('#parc').attr('disabled', false);
            $('input.operador').attr('disabled', false);
            $('#agregar').attr('disabled', true);
            $('#numero').val('');
            $('#numero').attr('disabled', true);
            $('input.constante').attr('disabled', true);
            $('input.btnFormula').attr('disabled', true);
        });
        
        $('.operador').click(function(){
            var valor = $(this).val();
            var campo = $('#formula_exp').val();
            $('#formula_exp').attr('value', campo+valor);
            $('#formula_eval').attr('value', campo+valor);
            $('input.parentesis').attr('disabled', false);
            $('input.operador').attr('disabled', true);
            $('#agregar').attr('disabled', false);
            $('#numero').attr('disabled', false);
            $('input.constante').attr('disabled', false);
            $('input.btnFormula').attr('disabled', false);
        });
        
        $('.valores').change(function(){
            var id = $(this).attr('id');
            var campo_valor = $(this).val();
            var formula = $('#formula_eval').val();
            var reemplazo = new RegExp(id,'g');
            if(campo_valor != ''){
                $('#formula_eval').attr('value', formula.replace(reemplazo, campo_valor));
            }
        });
        
        $('#Recuperar').click(function(){
            $('.valores').attr('value', '');
            $('.valores').attr('disabled', false);
            $('#formula_eval').val($('#formula_exp').val());
            $("#resultado").html('');
        });
        
        $('#Comprobar').click(function(){
            var formula = $('#formula_eval').val();
            var resultado;
            $("#resultado").html('');
            if(formula != ''){
                //FALTA VERIFICAR QUE EXISTA LA MISMA CANTIDAD DE PARENTESIS EN LA FORMULA
                var total = eval(formula).toFixed(3);
                if(total < 0){
                    resultado = "<tr><th class='error' align='center'><h3>El resultado debe ser Mayor o igual a Cero (0)";
                }else{
                    resultado = "<tr><th class='msj_verde' align='center'><h3>El resultado es: "+total;
                }
                    resultado += "</h3></th></tr>";
                    $("#resultado").append(resultado);
            }
        });
        
        $('#agregarcon').click(function(){
            $('#nroCondicion').val(parseInt($('#nroCondicion').val())+1);
            var nro = $('#nroCondicion').val();
            var infoCondicion = "<tr align='center'><th width='100'>Condicion Nro "+nro+"</th>";
            infoCondicion += "<td><input type='text' maxlength='7' value='' name='conddesde_"+nro+"' id='conddesde_"+nro+"' class='formula_campos positive menor_cond' onChange='CondicionNueva(this.id)'></td>";
            infoCondicion += "<td><input type='text' maxlength='7' value='' name='condhasta_"+nro+"' id='condhasta_"+nro+"' class='formula_campos positive menor_cond' onChange='CondicionNueva(this.id)'></td>";
            infoCondicion += "<td><input type='text' maxlength='7' value='' name='conddesc_"+nro+"' id='conddesc_"+nro+"' class='formula_campos positive menor_cond' onChange='CondicionNueva(this.id)'></td>";
            infoCondicion += "</td></tr>";
            $("#nueva_condicion").append(infoCondicion);
        });
    });
    
    function CondicionNueva(id){
        var campo_id = id.split('_');
        var campo_valor = document.getElementById(id).value;
        for(i=1; i < parseInt(campo_id[1]); i++){
            var comparar = document.getElementById('condhasta_'+i).value;
            if(i < campo_id[1]){
                if(campo_valor <= comparar){
                    alert('Esta condicion ya fue evaluada');
                    document.getElementById(id).value = '';
                    return false;
                }
            }
        }
    }
</script>
<form name="form1" id="form1" method="POST" action="?ac=guardar" enctype="multipart/form-data">
    <? echo $html->input('Cultivo.id', $infoCultivo[0]['id'], array('type' => 'hidden')); ?>
    <? echo $html->input('nroCondicion', 2, array('type' => 'hidden')); ?>
    <div id="titulo_modulo">
        NUEVA F&Oacute;RMULA<br/><hr/>
    </div>
    <fieldset>
        <legend>Informaci&oacute;n del Cultivo</legend>
        <table align="center" width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td width="130">Cultivo:</td>
                <td><? echo $html->select('id_cultivo', array('options' => $listaCultivos, 'default' => 'Seleccione', 'class' => 'inputGrilla')) ?></td>
            </tr>
            <tr>
                <td>Tipo de Formulaci&oacute;n:</td>
                <td><? echo $html->select('tipo_for', array('options' => $listaTipo, 'default' => 'Seleccione', 'class' => 'inputGrilla')) ?></td>
            </tr>
            <tbody id="opciones"></tbody>
            <tr>
                <td>Condici&oacute;n &Uacute;nica:</td>
                <td>
                    <? echo $html->select('multiple_cond', array('options' => $listaCondicion, 'selected' => $GPC['condicion'], 'default' => 'Seleccione', 'class' => 'inputGrilla')) ?>
                    <span id="btnCondicion" style="display: none"><? echo $html->input('agregarcon', 'Agregar Condici&oacute;n', array('type' => 'button')); ?></span>
                </td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
        </table>
    </fieldset>
    <fieldset id="unica_formula" style="display: none;">
        <legend>F&oacute;rmula 1</legend>
        <table align="center" width="100%" border="0" cellpadding="0" cellspacing="0" class="tabla_formula">
            <tr>
                <td>
                    <span class="msj_rojo">* </span>C&oacute;digo de la Formula: 
                    <? echo $html->input('codigo', '', array('type' => 'text', 'length' => '5', 'class' => 'cuadricula')); ?>
                </td>
            </tr>
            <tr>
                <td align="center" style="padding: 15px 0">
                    <? echo $html->input('para', '(', array('type' => 'button', 'class' => 'parentesis')); ?>
                    <? echo $html->input('parc', ')', array('type' => 'button', 'class' => 'parentesis')); ?>
                    <? echo $html->input('sum', '+', array('type' => 'button', 'class' => 'operador')); ?>
                    <? echo $html->input('res', '-', array('type' => 'button', 'class' => 'operador')); ?>
                    <? echo $html->input('mul', '*', array('type' => 'button', 'class' => 'operador')); ?>
                    <? echo $html->input('div', '/', array('type' => 'button', 'class' => 'operador')); ?>
                    <? echo $html->input('borrar', 'Borrar', array('type' => 'button')); ?>
                </td>
            </tr>
            <tr>
                <td align="center" style="padding: 15px 0; line-height: 30px;">
                    <?
                    echo $html->input('agregar', '<-', array('type' => 'button', 'class' => 'formula_campos'));
                    echo $html->input('numero', '', array('type' => 'text', 'length' => '7', 'class' => 'formula_campos positive'));
                    foreach($parametros->lista as $parametro){
                        echo $html->input($parametro['parametro_llave'], $parametro['parametro_llave'], array('type' => 'button', 'class' => 'formula_campos constante'));
                    }
                    foreach($formulas->listaF as $formula){
                        echo $html->input($formula['codigo'], $formula['codigo'], array('type' => 'button', 'class' => 'formula_campos btnFormula', 'onClick' => "asignarFormula('".$formula['formula']."')"));
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td align="center">
                    <textarea name="formula_exp" id="formula_exp" rows="1" readOnly="readOnly"><?=$GPC['formula']?></textarea>
                </td>
            </tr>
            <tr>
                <td style="padding: 10px 0;"><hr/></td>
            </tr>
            <tr>
                <th>COMPROBAR F&Oacute;RMULA<br/><br/></th>
            </tr>
            <tr>
                <td align="center">
                    <?
                    foreach($parametros->lista as $parametro){
                        echo $parametro['parametro_llave']."&nbsp;&nbsp;&nbsp;";
                        echo $html->input($parametro['parametro_llave'], '', array('type' => 'text', 'length' => '7', 'class' => 'cuadricula valores positive'));
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td align="center">
                    <textarea name="formula_eval" id="formula_eval" rows="1" readOnly="readOnly"></textarea>
                </td>
            </tr>
            <tbody id="resultado"></tbody>
            <tr align="center">
                <td style="padding-top: 20px;">
                    <? echo $html->input('Comprobar', 'Comprobar', array('type' => 'button')); ?>
                    <? echo $html->input('Recuperar', 'Corregir Valores', array('type' => 'button')); ?>
                </td>
            </tr>
        </table>
    </fieldset>
    
    <fieldset id="multiple" style="display: none;">
        <legend>F&oacute;rmula 2</legend>
        <table align="center" width="100%" border="0" cellpadding="0" cellspacing="0" class="tabla_formula">
            <tr>
                <td>
                    <span class="msj_rojo">* </span>C&oacute;digo de la Formula: 
                    <? echo $html->input('codigo', '', array('type' => 'text', 'length' => '5', 'class' => 'cuadricula')); ?>
                </td>
            </tr>
            <tr>
                <td align="center" style="padding: 15px 0">
                    <? echo $html->input('para', '(', array('type' => 'button', 'class' => 'parentesis')); ?>
                    <? echo $html->input('parc', ')', array('type' => 'button', 'class' => 'parentesis')); ?>
                    <? echo $html->input('sum', '+', array('type' => 'button', 'class' => 'operador')); ?>
                    <? echo $html->input('res', '-', array('type' => 'button', 'class' => 'operador')); ?>
                    <? echo $html->input('mul', '*', array('type' => 'button', 'class' => 'operador')); ?>
                    <? echo $html->input('div', '/', array('type' => 'button', 'class' => 'operador')); ?>
                    <? echo $html->input('borrar', 'Borrar', array('type' => 'button')); ?>
                </td>
            </tr>
            <tr>
                <td align="center" style="padding: 15px 0; line-height: 30px;">
                    <?
                    echo $html->input('agregar', '<-', array('type' => 'button', 'class' => 'formula_campos'));
                    echo $html->input('numero', '', array('type' => 'text', 'length' => '7', 'class' => 'formula_campos positive'));
                    foreach($parametros->lista as $parametro){
                        echo $html->input($parametro['parametro_llave'], $parametro['parametro_llave'], array('type' => 'button', 'class' => 'formula_campos constante'));
                    }
                    foreach($formulas->listaF as $formula){
                        echo $html->input($formula['codigo'], $formula['codigo'], array('type' => 'button', 'class' => 'formula_campos btnFormula', 'onClick' => "asignarFormula('".$formula['formula']."')"));
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td align="center">
                    <textarea name="formula_exp" id="formula_exp" rows="1" readOnly="readOnly"><?=$GPC['formula']?></textarea>
                </td>
            </tr>
            <tr>
                <td style="padding: 10px 0;"><hr/></td>
            </tr>
            <tr>
                <th>COMPROBAR F&Oacute;RMULA<br/><br/></th>
            </tr>
            <tr>
                <td align="center">
                    <?
                    foreach($parametros->lista as $parametro){
                        echo $parametro['parametro_llave']."&nbsp;&nbsp;&nbsp;";
                        echo $html->input($parametro['parametro_llave'], '', array('type' => 'text', 'length' => '7', 'class' => 'cuadricula valores positive'));
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td align="center">
                    <textarea name="formula_eval" id="formula_eval" rows="1" readOnly="readOnly"></textarea>
                </td>
            </tr>
            <tbody id="resultado"></tbody>
            <tr align="center">
                <td style="padding-top: 20px;">
                    <? echo $html->input('Comprobar', 'Comprobar', array('type' => 'button')); ?>
                    <? echo $html->input('Recuperar', 'Corregir Valores', array('type' => 'button')); ?>
                </td>
            </tr>
        </table>
    </fieldset>
    <table align="center" width="100%">
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr align="center">
            <td>
                <? echo $html->input('Guardar', 'Guardar', array('type' => 'submit')); ?>
                <? echo $html->input('Cancelar', 'Cancelar', array('type' => 'reset', 'onClick' => 'cancelar()')); ?>
            </td>
        </tr>
    </table>
</form>
<?
require('../lib/common/footer.php');
?>