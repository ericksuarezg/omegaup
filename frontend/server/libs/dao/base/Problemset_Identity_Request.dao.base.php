<?php

/** ******************************************************************************* *
  *                    !ATENCION!                                                   *
  *                                                                                 *
  * Este codigo es generado automaticamente. Si lo modificas tus cambios seran      *
  * reemplazados la proxima vez que se autogenere el codigo.                        *
  *                                                                                 *
  * ******************************************************************************* */

/** ProblemsetIdentityRequest Data Access Object (DAO) Base.
 *
 * Esta clase contiene toda la manipulacion de bases de datos que se necesita
 * para almacenar de forma permanente y recuperar instancias de objetos
 * {@link ProblemsetIdentityRequest}.
 * @access public
 * @abstract
 *
 */
abstract class ProblemsetIdentityRequestDAOBase {
    /**
     * Guardar registros.
     *
     * Este metodo guarda el estado actual del objeto {@link ProblemsetIdentityRequest}
     * pasado en la base de datos. La llave primaria indicará qué instancia va
     * a ser actualizada en base de datos. Si la llave primara o combinación de
     * llaves primarias que describen una fila que no se encuentra en la base de
     * datos, entonces replace() creará una nueva fila.
     *
     * @static
     * @throws Exception si la operacion fallo.
     * @param ProblemsetIdentityRequest [$Problemset_Identity_Request] El objeto de tipo ProblemsetIdentityRequest
     * @return Un entero mayor o igual a cero identificando el número de filas afectadas.
     */
    final public static function replace(ProblemsetIdentityRequest $Problemset_Identity_Request) : int {
        if (is_null($Problemset_Identity_Request->identity_id) || is_null($Problemset_Identity_Request->problemset_id)) {
            throw new NotFoundException('recordNotFound');
        }
        if (is_null($Problemset_Identity_Request->request_time)) {
            $Problemset_Identity_Request->request_time = gmdate('Y-m-d H:i:s', Time::get());
        }
        $sql = 'REPLACE INTO Problemset_Identity_Request (`identity_id`, `problemset_id`, `request_time`, `last_update`, `accepted`, `extra_note`) VALUES (?, ?, ?, ?, ?, ?);';
        $params = [
            (int)$Problemset_Identity_Request->identity_id,
            (int)$Problemset_Identity_Request->problemset_id,
            $Problemset_Identity_Request->request_time,
            $Problemset_Identity_Request->last_update,
            is_null($Problemset_Identity_Request->accepted) ? null : (int)$Problemset_Identity_Request->accepted,
            $Problemset_Identity_Request->extra_note,
        ];
        global $conn;
        $conn->Execute($sql, $params);
        return $conn->Affected_Rows();
    }

    /**
     * Actualizar registros.
     *
     * @static
     * @return Filas afectadas
     * @param ProblemsetIdentityRequest [$Problemset_Identity_Request] El objeto de tipo ProblemsetIdentityRequest a actualizar.
     */
    final public static function update(ProblemsetIdentityRequest $Problemset_Identity_Request) : int {
        $sql = 'UPDATE `Problemset_Identity_Request` SET `request_time` = ?, `last_update` = ?, `accepted` = ?, `extra_note` = ? WHERE `identity_id` = ? AND `problemset_id` = ?;';
        $params = [
            $Problemset_Identity_Request->request_time,
            $Problemset_Identity_Request->last_update,
            is_null($Problemset_Identity_Request->accepted) ? null : (int)$Problemset_Identity_Request->accepted,
            $Problemset_Identity_Request->extra_note,
            (int)$Problemset_Identity_Request->identity_id,
            (int)$Problemset_Identity_Request->problemset_id,
        ];
        global $conn;
        $conn->Execute($sql, $params);
        return $conn->Affected_Rows();
    }

    /**
     * Obtener {@link ProblemsetIdentityRequest} por llave primaria.
     *
     * Este metodo cargará un objeto {@link ProblemsetIdentityRequest} de la base
     * de datos usando sus llaves primarias.
     *
     * @static
     * @return @link ProblemsetIdentityRequest Un objeto del tipo {@link ProblemsetIdentityRequest}. NULL si no hay tal registro.
     */
    final public static function getByPK(int $identity_id, int $problemset_id) : ?ProblemsetIdentityRequest {
        $sql = 'SELECT `Problemset_Identity_Request`.`identity_id`, `Problemset_Identity_Request`.`problemset_id`, `Problemset_Identity_Request`.`request_time`, `Problemset_Identity_Request`.`last_update`, `Problemset_Identity_Request`.`accepted`, `Problemset_Identity_Request`.`extra_note` FROM Problemset_Identity_Request WHERE (identity_id = ? AND problemset_id = ?) LIMIT 1;';
        $params = [$identity_id, $problemset_id];
        global $conn;
        $row = $conn->GetRow($sql, $params);
        if (empty($row)) {
            return null;
        }
        return new ProblemsetIdentityRequest($row);
    }

    /**
     * Eliminar registros.
     *
     * Este metodo eliminará el registro identificado por la llave primaria en
     * el objeto ProblemsetIdentityRequest suministrado. Una vez que se ha
     * eliminado un objeto, este no puede ser restaurado llamando a
     * {@link replace()}, ya que este último creará un nuevo registro con una
     * llave primaria distinta a la que estaba en el objeto eliminado.
     *
     * Si no puede encontrar el registro a eliminar, {@link Exception} será
     * arrojada.
     *
     * @static
     * @throws Exception Se arroja cuando no se encuentra el objeto a eliminar en la base de datos.
     * @param ProblemsetIdentityRequest [$Problemset_Identity_Request] El objeto de tipo ProblemsetIdentityRequest a eliminar
     */
    final public static function delete(ProblemsetIdentityRequest $Problemset_Identity_Request) : void {
        $sql = 'DELETE FROM `Problemset_Identity_Request` WHERE identity_id = ? AND problemset_id = ?;';
        $params = [$Problemset_Identity_Request->identity_id, $Problemset_Identity_Request->problemset_id];
        global $conn;

        $conn->Execute($sql, $params);
        if ($conn->Affected_Rows() == 0) {
            throw new NotFoundException('recordNotFound');
        }
    }

    /**
     * Obtener todas las filas.
     *
     * Esta funcion leerá todos los contenidos de la tabla en la base de datos
     * y construirá un arreglo que contiene objetos de tipo {@link ProblemsetIdentityRequest}.
     * Este método consume una cantidad de memoria proporcional al número de
     * registros regresados, así que sólo debe usarse cuando la tabla en
     * cuestión es pequeña o se proporcionan parámetros para obtener un menor
     * número de filas.
     *
     * @static
     * @param $pagina Página a ver.
     * @param $filasPorPagina Filas por página.
     * @param $orden Debe ser una cadena con el nombre de una columna en la base de datos.
     * @param $tipoDeOrden 'ASC' o 'DESC' el default es 'ASC'
     * @return Array Un arreglo que contiene objetos del tipo {@link ProblemsetIdentityRequest}.
     */
    final public static function getAll(
        ?int $pagina = null,
        ?int $filasPorPagina = null,
        ?string $orden = null,
        string $tipoDeOrden = 'ASC'
    ) : array {
        $sql = 'SELECT `Problemset_Identity_Request`.`identity_id`, `Problemset_Identity_Request`.`problemset_id`, `Problemset_Identity_Request`.`request_time`, `Problemset_Identity_Request`.`last_update`, `Problemset_Identity_Request`.`accepted`, `Problemset_Identity_Request`.`extra_note` from Problemset_Identity_Request';
        global $conn;
        if (!is_null($orden)) {
            $sql .= ' ORDER BY `' . $conn->escape($orden) . '` ' . ($tipoDeOrden == 'DESC' ? 'DESC' : 'ASC');
        }
        if (!is_null($pagina)) {
            $sql .= ' LIMIT ' . (($pagina - 1) * $filasPorPagina) . ', ' . (int)$filasPorPagina;
        }
        $allData = [];
        foreach ($conn->GetAll($sql) as $row) {
            $allData[] = new ProblemsetIdentityRequest($row);
        }
        return $allData;
    }

    /**
     * Crear registros.
     *
     * Este metodo creará una nueva fila en la base de datos de acuerdo con los
     * contenidos del objeto ProblemsetIdentityRequest suministrado.
     *
     * @static
     * @return Un entero mayor o igual a cero identificando el número de filas afectadas.
     * @param ProblemsetIdentityRequest [$Problemset_Identity_Request] El objeto de tipo ProblemsetIdentityRequest a crear.
     */
    final public static function create(ProblemsetIdentityRequest $Problemset_Identity_Request) : int {
        if (is_null($Problemset_Identity_Request->request_time)) {
            $Problemset_Identity_Request->request_time = gmdate('Y-m-d H:i:s', Time::get());
        }
        $sql = 'INSERT INTO Problemset_Identity_Request (`identity_id`, `problemset_id`, `request_time`, `last_update`, `accepted`, `extra_note`) VALUES (?, ?, ?, ?, ?, ?);';
        $params = [
            (int)$Problemset_Identity_Request->identity_id,
            (int)$Problemset_Identity_Request->problemset_id,
            $Problemset_Identity_Request->request_time,
            $Problemset_Identity_Request->last_update,
            is_null($Problemset_Identity_Request->accepted) ? null : (int)$Problemset_Identity_Request->accepted,
            $Problemset_Identity_Request->extra_note,
        ];
        global $conn;
        $conn->Execute($sql, $params);
        $affectedRows = $conn->Affected_Rows();
        if ($affectedRows == 0) {
            return 0;
        }

        return $affectedRows;
    }
}
