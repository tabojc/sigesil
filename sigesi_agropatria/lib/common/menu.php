<?
$menu = array();

    switch ($_SESSION['s_perfil_id']){
        case GERENTE:
            $menu['Maestro'] = array('Centros de Acopio' => DOMAIN_ROOT . 'admin/centros_acopio_listado.php',
                                    'Cultivo' => DOMAIN_ROOT . 'admin/cultivo_listado.php',
                                    'Tipos de Cultivo' => DOMAIN_ROOT . 'admin/tipo_cultivo_listado.php',
                                    'Programa' => DOMAIN_ROOT . 'admin/programa_listado.php',
                                    'Silos' => DOMAIN_ROOT.'admin/silos_listado.php',
                                    'Productor' => DOMAIN_ROOT.'admin/productor_listado.php'
            );

            $menu['Reportes'] = array('Programas' => 'reporte_programas/index',
                                    'Cosecha' => 'reporte_cosecha/index',
                                    'Cultivos' => 'reporte_cultivos/index',
                                    'Productores' => 'reporte_productores/index',
                                    'Recepciones' => 'reporte_recepciones/index',
                                    'Despacho' => 'reporte_despacho/index'
            );

            $menu['Panel de Control'] = array('Silos' => 'silos/index',
                                        'Usuarios' => 'admin_usuarios/index',
                                        'Formulas' => 'admin/formulacion/index',
                                        'Configuración' => 'admin_configuracion_sistema/index'
            );

            $menu['Cuenta'] = array('Config. Cuenta' => DOMAIN_ROOT.'admin/cuenta.php',
                                'Salir' => DOMAIN_ROOT . 'pages/cerrar_sesion.php'
            );
        break;
        case ADMINISTRADOR:
            $menu['Maestro'] = array('Programa' => DOMAIN_ROOT . 'admin/programa_listado.php',
                                    'Silos' => DOMAIN_ROOT.'admin/silos_listado.php',
                                    'Productor' => DOMAIN_ROOT.'admin/productor_listado.php'
            );

            $menu['Procesos'] = array('Recepción' => 'recepcion/index',
                                    'Despacho' => 'despacho/index'
            );

            $menu['Reportes'] = array('Programas' => 'reporte_programas/index',
                                    'Cosecha' => 'reporte_cosecha/index',
                                    'Cultivos' => 'reporte_cultivos/index',
                                    'Productores' => 'reporte_productores/index',
                                    'Recepciones' => 'reporte_recepciones/index',
                                    'Despacho' => 'reporte_despacho/index'
            );

            $menu['Panel de Control'] = array('Silos' => 'silos/index',
                                        'Usuarios' => 'admin_usuarios/index',
                                        'Formulas' => 'admin/formulacion/index',
                                        'Configuración' => 'admin_configuracion_sistema/index'
            );

            $menu['Cuenta'] = array('Config. Cuenta' => DOMAIN_ROOT.'admin/cuenta.php',
                                'Salir' => DOMAIN_ROOT . 'pages/cerrar_sesion.php'
            );
        break;
    }
?>
<div id="accordion">
    <? foreach($menu as $campoPadre => $menuPadre){ ?>
    <div>
        <h3><a href="<?=$menuPadre?>"><?=$campoPadre?></a></h3>
        <div>
            <ul id="lista_accordion">
                <? foreach($menuPadre as $campoHijo => $menuHijo){ ?>
                <li><a href="<?=$menuHijo?>"><?=$campoHijo?></a></li>
                <? } ?>
            </ul>
        </div>
    </div>
    <? } ?>
</div>