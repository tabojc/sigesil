<?php

class Orden extends Model {
    var $table = 'si_ordenes';
    
    function buscarOrden($id = null, $idCA = null, $idCl = null, $idCu = null, $numOrden = null, $porPagina=null, $inicio=null){
        $query = "SELECT o.*, ca.nombre AS nombre_ca, ca.codigo, cl.ced_rif AS ced_cliente, cl.nombre AS nombre_cliente
                    FROM si_ordenes o
                    INNER JOIN si_centro_acopio ca ON ca.id = o.id_centro_acopio
                    INNER JOIN si_cliente cl ON cl.id = o.id_cliente
                    INNER JOIN si_cultivo cu ON cu.id = o.id_cultivo
                    WHERE '1' AND (o.estatus = 't' OR o.estatus = 'N')";
        $query .= (!empty($id)) ? " AND o.id = '$id'" : "";
        $query .= (!empty($idCA)) ? " AND o.id_centro_acopio = '$idCA'" : "";
        $query .= (!empty($idCl)) ? " AND cl.id = '$idCl'" : "";
        $query .= (!empty($idCu)) ? " AND cu.id = '$idCu'" : "";
        $query .= (!empty($numero)) ? " AND o.numero_orden = '$numero'" : "";
        $query .= " ORDER BY id DESC";
        $query .= (!empty($porPagina)) ? " LIMIT $porPagina OFFSET $inicio" : "";
        return $this->_SQL_tool($this->SELECT, __METHOD__, $query);
    }
    
    function buscarSubOrden($idOrden = null, $subOrden = null){
        $query = "SELECT * FROM si_ordenes_det WHERE '1'";
        $query .= (!empty($idOrden)) ? " AND id_orden = '$idOrden'" : "";
        $query .= (!empty($subOrden)) ? " AND id = '$subOrden'" : "";
        return $this->_SQL_tool($this->SELECT, __METHOD__, $query);
    }
    
    function ordenCliente($id = null, $numero = null){
        $query = "SELECT o.*, c.ced_rif, c.nombre AS cliente_nombre, c.telefono AS cliente_telefono
                    FROM si_ordenes o
                    INNER JOIN si_cliente c ON c.id = o.id_cliente
                    WHERE '1'";
        $query .= (!empty($id)) ? " AND o.id = '$id'" : "";
        $query .= (!empty($numero)) ? " AND o.numero_orden = '$numero'" : "";
        return $this->_SQL_tool($this->SELECT, __METHOD__, $query);
    }
    
    function guardarSubOrden($idOrden, $subOrden, $fecha=null, $descripcion = null, $kgs = null){
        $fecha = (!empty($fecha)) ? "'".$fecha."'" : 'null';
        $query = "INSERT INTO si_ordenes_det (id_orden, numero_orden, fecha, descripcion, kilogramos)
                    VALUES ('$idOrden', '$subOrden', $fecha, '$descripcion', '$kgs')";
        $id = $this->_SQL_tool('INSERT', __METHOD__, $query);
        return $id;
    }
    
    function cantDespachada($id = null){
        //SUMA LOS KGRS DE LA TABLA ORDENES
        $query = "SELECT SUM(peso_acon_liq) AS total 
                    FROM si_despacho 
                    WHERE '1' AND id_orden = '$id'
                    GROUP BY id_orden";
        //SUMA LOS KGRS DE LA TABLA ORDENES_DET
        /*$query = "SELECT SUM(kilogramos) AS total 
                    FROM si_ordenes_det 
                    WHERE '1' AND id_orden = '$id'
                    GROUP BY id_orden";*/
        return $this->_SQL_tool($this->SELECT, __METHOD__, $query);
    }
    
    function siguienteNumOrden(){
        $query = "SELECT MAX(numero_orden) AS cod_orden
                    FROM si_ordenes o
                    WHERE '1' AND EXTRACT(YEAR FROM fecha_emision) = '".date('Y')."'";
        return $this->_SQL_tool($this->SELECT, __METHOD__, $query);
    }
    
    function desactivarOr($id, $estatus = 't') {
        $estatus = ($estatus == 't') ? 't' : 'f';
        $query = "UPDATE $this->table SET estatus = '$estatus', modificado = now() WHERE id = '$id'";
        return $result = $this->_SQL_tool("UPDATE", __METHOD__, $query);
    }
}
?>