<?
/*******************************************************************************
* contact.grid.inc.php
* contact操作类
* Customer class

* @author			Solo Fu <solo.fu@gmail.com>
* @classVersion		1.0
* @date				18 Oct 2007

* Functions List

	getAllRecords				获取所有记录
	getRecordsFiltered			获取记录集
	getNumRows					获取记录集条数
	deleteRecord				删除contact记录, 同时删除与之相关的note
	新增getRecordsFilteredMore  用于获得多条件搜索记录集
	新增getNumRowsMore          用于获得多条件搜索记录条数

* Revision 0.045  2007/10/18 13:30:00  last modified by solo
* Desc: page created

********************************************************************************/

require_once 'db_connect.php';
require_once 'contact.common.php';
require_once 'include/astercrm.class.php';

class Customer extends astercrm
{

	/**
	*  Obtiene todos los registros de la tabla paginados.
	*
	*  	@param $start	(int)	Inicio del rango de la p&aacute;gina de datos en la consulta SQL.
	*	@param $limit	(int)	L&iacute;mite del rango de la p&aacute;gina de datos en la consultal SQL.
	*	@param $order 	(string) Campo por el cual se aplicar&aacute; el orden en la consulta SQL.
	*	@return $res 	(object) Objeto que contiene el arreglo del resultado de la consulta SQL.
	*/
	function &getAllRecords($start, $limit, $order = null, $creby = null){
		global $db;
		
		$sql = "SELECT contact.*,note.note,customer.customer FROM contact LEFT JOIN customer ON customer.id = contact.customerid LEFT JOIN note on note.contactid = contact.id ";

		if ($_SESSION['curuser']['usertype'] == 'admin'){
			$sql .= " ";
		}else{
			$sql .= " WHERE contact.groupid = ".$_SESSION['curuser']['groupid']." ";
		}

		if($order == null){
			$sql .= " ORDER BY cretime DESC LIMIT $start, $limit";//.$_SESSION['ordering'];
		}else{
			$sql .= " ORDER BY $order ".$_SESSION['ordering']." LIMIT $start, $limit";
		}

		Customer::events($sql);
		$res =& $db->query($sql);
		return $res;
	}
	
	/**
	*  Obtiene todos registros de la tabla paginados y aplicando un filtro
	*
	*  @param $start		(int) 		Es el inicio de la p&aacute;gina de datos en la consulta SQL
	*	@param $limit		(int) 		Es el limite de los datos p&aacute;ginados en la consultal SQL.
	*	@param $filter		(string)	Nombre del campo para aplicar el filtro en la consulta SQL
	*	@param $content 	(string)	Contenido a filtrar en la conslta SQL.
	*	@param $order		(string) 	Campo por el cual se aplicar&aacute; el orden en la consulta SQL.
	*	@return $res		(object)	Objeto que contiene el arreglo del resultado de la consulta SQL.
	*/

	function &getRecordsFilteredMore($start, $limit, $filter, $content, $order,$table, $ordering = ""){
		global $db;

		$i=0;
		$joinstr='';
		foreach ($content as $value){
			$value = preg_replace("/'/","\\'",$value);
			$value=trim($value);
			if (strlen($value)!=0 && strlen($filter[$i]) != 0){
				$joinstr.="AND $filter[$i] like '%".$value."%' ";
			}
			$i++;
		}

		$sql = "SELECT contact.*,note.note,customer.customer FROM contact LEFT JOIN customer ON customer.id = contact.customerid LEFT JOIN note on note.contactid = contact.id WHERE ";
		if ($_SESSION['curuser']['usertype'] == 'admin'){
			$sql .= " 1 ";
		}else{
			$sql .= " contact.groupid = ".$_SESSION['curuser']['groupid']." ";
		}

		if ($joinstr!=''){
			$joinstr=ltrim($joinstr,'AND'); //去掉最左边的AND
			$sql .= " AND ".$joinstr."  "
					." ORDER BY ".$order
					." ".$_SESSION['ordering']
					." LIMIT $start, $limit $ordering";
		}
		Customer::events($sql);
		$res =& $db->query($sql);
		return $res;
	}

	
	/**
	*  Devuelte el numero de registros de acuerdo a los par&aacute;metros del filtro
	*
	*	@param $filter	(string)	Nombre del campo para aplicar el filtro en la consulta SQL
	*	@param $order	(string)	Campo por el cual se aplicar&aacute; el orden en la consulta SQL.
	*	@return $row['numrows']	(int) 	N&uacute;mero de registros (l&iacute;neas)
	*/
	
	function &getNumRows($filter = null, $content = null){
		global $db;

		
		if ($_SESSION['curuser']['usertype'] == 'admin'){
			$sql = " SELECT COUNT(*)  FROM contact LEFT JOIN customer ON customer.id = contact.customerid LEFT JOIN note on note.contactid = contact.id ";
		}else{
			$sql = " SELECT COUNT(*) FROM contact LEFT JOIN customer ON customer.id = contact.customerid LEFT JOIN note on note.contactid = contact.id WHERE contact.groupid = ".$_SESSION['curuser']['groupid']." ";
		}

		Customer::events($sql);
		$res =& $db->getOne($sql);
		return $res;		
	}

	function &getNumRowsMore($filter = null, $content = null,$table){
		global $db;
		
			$i=0;
			$joinstr='';
			foreach ($content as $value){
				$value = preg_replace("/'/","\\'",$value);
				$value=trim($value);
				if (strlen($value)!=0 && strlen($filter[$i]) != 0){
					$joinstr.="AND $filter[$i] like '%".$value."%' ";
				}
				$i++;
			}

			$sql = "SELECT COUNT(*) FROM contact LEFT JOIN customer ON customer.id = contact.customerid LEFT JOIN note on note.contactid = contact.id WHERE ";
			if ($_SESSION['curuser']['usertype'] == 'admin'){
				$sql .= " ";
			}else{
				$sql .= " contact.groupid = ".$_SESSION['curuser']['groupid']." AND ";
			}

			if ($joinstr!=''){
				$joinstr=ltrim($joinstr,'AND'); //去掉最左边的AND
				$sql .= " ".$joinstr;
			}else {
				$sql .= " 1";
			}
		Customer::events($sql);
		$res =& $db->getOne($sql);
//		print $sql;
//		print "\n";
//		print $res;
//		exit;
		return $res;
	}

	function &getRecordsFilteredMorewithstype($start, $limit, $filter, $content, $stype,$order,$table){
		global $db;

		$joinstr = astercrm::createSqlWithStype($filter,$content,$stype);

		$sql = "SELECT contact.*,note.note, customer.customer FROM contact LEFT JOIN customer ON customer.id = contact.customerid LEFT JOIN note on note.contactid = contact.id WHERE ";
		if ($_SESSION['curuser']['usertype'] == 'admin'){
			$sql .= " 1 ";
		}else{
			$sql .= " contact.groupid = ".$_SESSION['curuser']['groupid']." ";
		}

		if ($joinstr!=''){
			$joinstr=ltrim($joinstr,'AND'); //去掉最左边的AND
			$sql .= " AND ".$joinstr."  "
					." ORDER BY ".$order
					." ".$_SESSION['ordering']
					." LIMIT $start, $limit $ordering";
		}
		Customer::events($sql);
		$res =& $db->query($sql);
		return $res;
	}

	function &getNumRowsMorewithstype($filter, $content,$stype,$table){
		global $db;
		
			$joinstr = astercrm::createSqlWithStype($filter,$content,$stype);

			$sql = "SELECT COUNT(*) FROM contact LEFT JOIN customer ON customer.id = contact.customerid LEFT JOIN note on note.contactid = contact.id WHERE ";
			if ($_SESSION['curuser']['usertype'] == 'admin'){
				$sql .= " ";
			}else{
				$sql .= " contact.groupid = ".$_SESSION['curuser']['groupid']." AND ";
			}

			if ($joinstr!=''){
				$joinstr=ltrim($joinstr,'AND'); //去掉最左边的AND
				$sql .= " ".$joinstr;
			}else {
				$sql .= " 1";
			}
		Customer::events($sql);
		$res =& $db->getOne($sql);
		return $res;
	}
	
	/**
	*  Borra un registro de la tabla.
	*
	*	@param $id		(int)	Identificador del registro a ser borrado.
	*	@return $res	(object) Devuelve el objeto con la respuesta de la sentencia SQL ejecutada del DELETE.
	*/
	
	function deleteRecord($id){
		global $db;
		
		//backup all datas

		//delete all customers
		$sql = "DELETE FROM contact WHERE id = $id";
		Customer::events($sql);
		$res =& $db->query($sql);

		//delete all note
		$sql = "DELETE FROM note WHERE contactid = $id";
		Customer::events($sql);
		$res =& $db->query($sql);

		return $res;
	}
}
?>
