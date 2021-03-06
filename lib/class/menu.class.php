<?php

class Menu extends Model {
    var $table = 'si_menu';
    
    function menuPorUsuario($idUsuario = null, $idPadre = null){
        $query = "SELECT mp.*, mh.nuevo, mh.modificar, mh.eliminar, mh.imprimir
                    FROM si_menu mp
                    INNER JOIN si_menu_usuario mh ON mh.id_menu = mp.id
                    WHERE mh.id_usuario = '$idUsuario' AND mp.estatus = 't'";
        $query .= (!is_null($idPadre)) ? " AND mp.id_padre = '$idPadre'" : "";
        $query .= " ORDER BY mp.id_padre, mp.orden";
        return $this->_SQL_tool($this->SELECT, __METHOD__, $query);
    }
    
    function asignarMenuUsuario($idM, $idU, $nuevo = 0, $modificar = 0, $eliminar = 0, $imprimir = 0){
        $query = "INSERT INTO si_menu_usuario (id_menu, id_usuario, nuevo, modificar, eliminar, imprimir)
                    VALUES ('$idM', '$idU', '$nuevo', '$modificar', '$eliminar', '$imprimir')";
        $result = $this->_SQL_tool('INSERT', __METHOD__, $query);
        return $result;
    }
    
    function eliminarMenuUsuario($idU, $idP){
        $query = "DELETE FROM si_menu_usuario WHERE id_usuario = '$idU'";
        $this->_SQL_tool($this->DELETE, __METHOD__, $query);
    }
    
    function accionesPagina($idUsuario, $menu){
        $query = "SELECT mu.nuevo, mu.modificar, mu.eliminar, mu.imprimir
                    FROM si_menu_usuario mu
                    INNER JOIN si_menu m ON m.id = mu.id_menu
                    WHERE '1' AND mu.id_usuario = '$idUsuario' AND m.url ILIKE '%$menu%'";
        return $this->_SQL_tool($this->SELECT, __METHOD__, $query);
    }
}
?>