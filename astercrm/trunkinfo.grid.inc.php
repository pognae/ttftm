<?php
/*******************************************************************************
* survey.grid.inc.php
* survey操作类
* Customer class

* @author			Solo Fu <solo.fu@gmail.com>
* @classVersion		1.0
* @date				18 Oct 2007

* Functions List

	getAllRecords				获取所有记录
	getRecordsFiltered			获取记录集
	getNumRows					获取记录集条数
	formAdd						生成添加trunkinfo的HTML语句
	insertNewSurvey				保存survey
	insertNewOption				保存option
	setSurveyEnable				设定survey的可用情况

* Revision 0.0456  2007/11/6 20:30:00  last modified by solo
* Desc: remove function deleteSurvey

* Revision 0.045  2007/10/18 13:30:00  last modified by solo
* Desc: page created

********************************************************************************/
require_once 'db_connect.php';
require_once 'trunkinfo.common.php';
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

//		$sql = "SELECT trunkinfo.*, groupname, campaignname FROM trunkinfo LEFT JOIN accountgroup ON accountgroup.groupid = trunkinfo.groupid LEFT JOIN campaign ON campaign.id = trunkinfo.campaignid";
		$sql = "SELECT * FROM trunkinfo ";


		if ($_SESSION['curuser']['usertype'] == 'admin'){
			$sql .= " ";
		}else{
			//$sql .= " WHERE trunkinfo.groupid = ".$_SESSION['curuser']['groupid']." ";
		}

		if($order == null){
			$sql .= " ORDER BY cretime DESC LIMIT $start, $limit";//.$_SESSION['ordering'];
		}else{
			$sql .= " ORDER BY $order ".$_SESSION['ordering']." LIMIT $start, $limit";
		}

		Customer::events($sql);
		$res =& $db->query($sql);
//		print_r($res);
//		exit;
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

		//$sql = "SELECT trunkinfo.*, groupname, campaignname FROM trunkinfo LEFT JOIN accountgroup ON accountgroup.groupid = trunkinfo.groupid LEFT JOIN campaign ON campaign.id = trunkinfo.campaignid WHERE ";

		$sql = "SELECT * FROM trunkinfo WHERE ";

		if ($_SESSION['curuser']['usertype'] == 'admin'){
			$sql .= " 1 ";
		}else{
			//$sql .= " trunkinfo.groupid = ".$_SESSION['curuser']['groupid']." ";
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

			$sql = "SELECT COUNT(*) FROM trunkinfo WHERE ";
			if ($_SESSION['curuser']['usertype'] == 'admin'){
				$sql .= " ";
			}else{
				//$sql .= " trunkinfo.groupid = ".$_SESSION['curuser']['groupid']." AND ";
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

		//$sql = "SELECT trunkinfo.*, groupname, campaignname FROM trunkinfo LEFT JOIN accountgroup ON accountgroup.groupid = trunkinfo.groupid LEFT JOIN campaign ON campaign.id = trunkinfo.campaignid WHERE ";

		$sql = "SELECT * FROM trunkinfo WHERE ";

		if ($_SESSION['curuser']['usertype'] == 'admin'){
			$sql .= " 1 ";
		}else{
			//$sql .= " trunkinfo.groupid = ".$_SESSION['curuser']['groupid']." ";
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

			$sql = "SELECT COUNT(*) FROM trunkinfo WHERE ";
			if ($_SESSION['curuser']['usertype'] == 'admin'){
				$sql .= " ";
			}else{
				//$sql .= " trunkinfo.groupid = ".$_SESSION['curuser']['groupid']." AND ";
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

	function insertNewTrunkinfo($f){
		global $db;
		$query= "INSERT INTO trunkinfo SET "
				."trunkname='".$f['trunkname']."', "
				."trunkchannel='".$f['trunkchannel']."', "
				."didnumber='".$f['didnumber']."', "
				."trunk_number='".$f['trunk_number']."', "
				."trunknote='".$f['trunknote']."', "
				."cretime=now(), "
				."creby='".$_SESSION['curuser']['username']."'";
		astercrm::events($query);
		$res =& $db->query($query);
		return $res;
	}

	/**
	*  Imprime la forma para agregar un nuevo registro sobre el DIV identificado por "formDiv".
	*
	*	@param ninguno
	*	@return $html	(string) Devuelve una cadena de caracteres que contiene la forma para insertar 
	*							un nuevo registro.
	*/
	
	function formAdd(){
			global $locate;

			$html = '
			<!-- No edit the next line -->
			<form method="post" name="f" id="f">
			
			<table border="1" width="100%" class="adminlist">
				<tr>
					<td nowrap align="left">'.$locate->Translate("Trunk Name").' *</td>
					<td align="left"><input type="text" id="trunkname" name="trunkname" size="30" maxlength="50"></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Trunk Channel").' *</td>
					<td align="left"><input type="text" id="trunkchannel" name="trunkchannel" size="30" maxlength="50"></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("DID Number").' </td>
					<td align="left"><input type="text" id="didnumber" name="didnumber" size="30" maxlength="30"></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Trunk Number").' </td>
					<td align="left"><input type="text" id="trunk_number" name="trunk_number" size="30" maxlength="30"></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Trunk Note").'</td>
					<td align="left"><textarea rows="8" cols="50" id="trunknote" name="trunknote"></textarea></td>
				</tr>
				<tr>
					<td colspan="2" align="center"><button id="submitButton" onClick=\'xajax_save(xajax.getFormValues("f"));return false;\'>'.$locate->Translate("continue").'</button></td>
				</tr>

			 </table>
			';

		$html .='
			</form>
			'.$locate->Translate("obligatory_fields").'
			';
		
		return $html;
	}

	/**
	*  Imprime la forma para editar un nuevo registro sobre el DIV identificado por "formDiv".
	*
	*	@param $id		(int)		Identificador del registro a ser editado.
	*	@return $html	(string) Devuelve una cadena de caracteres que contiene la forma con los datos 
	*									a extraidos de la base de datos para ser editados 
	*/
	
	function formEdit($id){
		global $locate;
		$trunkinfo =& Customer::getRecordByID($id,'trunkinfo');

		$html = '
			<!-- No edit the next line -->
			<form method="post" name="f" id="f">
			
			<table border="1" width="100%" class="adminlist">
				<tr>
					<td nowrap align="left">'.$locate->Translate("Trunk Name").' *</td>
					<td align="left"><input type="hidden" id="id" name="id" value="'. $trunkinfo['id'].'"><input type="text" id="trunkname" name="trunkname" size="30" maxlength="50" value="'.$trunkinfo['trunkname'].'"></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Trunk Channel").' *</td>
					<td align="left"><input type="text" id="trunkchannel" name="trunkchannel" size="30" maxlength="50" value="'.$trunkinfo['trunkchannel'].'"></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("DID Number").' </td>
					<td align="left"><input type="text" id="didnumber" name="didnumber" size="30" maxlength="50" value="'.$trunkinfo['didnumber'].'"></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Trunk Number").' </td>
					<td align="left"><input type="text" id="trunk_number" name="trunk_number" size="30" maxlength="30" value="'.$trunkinfo['trunk_number'].'"></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Trunk Note").'</td>
					<td align="left"><textarea rows="8" cols="50" id="trunknote" name="trunknote">'.$trunkinfo['trunknote'].'</textarea></td>
				</tr>
				<tr>
					<td colspan="2" align="center"><button id="submitButton" onClick=\'xajax_update(xajax.getFormValues("f"));return false;\'>'.$locate->Translate("continue").'</button></td>
				</tr>

			 </table>
			';

			

		$html .= '
				</form>
				'.$locate->Translate("obligatory_fields").'
				';

		return $html;
	}


	function updateTrunkinfoRecord($f){
		global $db;
		$f = astercrm::variableFiler($f);
		$query= "UPDATE trunkinfo SET "
				."trunkname='".$f['trunkname']."', "
				."trunkchannel='".$f['trunkchannel']."', "
				."didnumber='".$f['didnumber']."', "
				."trunk_number='".$f['trunk_number']."', "
				."trunknote='".$f['trunknote']."', "
				."cretime= now() "
				."WHERE id= ".$f['id']." ";
		astercrm::events($query);
		$res =& $db->query($query);
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
			$sql = " SELECT COUNT(*) FROM trunkinfo LEFT JOIN accountgroup ON accountgroup.id = trunkinfo.groupid";
		}else{
			$sql = " SELECT COUNT(*) FROM trunkinfo LEFT JOIN accountgroup ON accountgroup.id = trunkinfo.groupid WHERE trunkinfo.groupid = ".$_SESSION['curuser']['groupid']." ";
		}
		$sql = " SELECT COUNT(*) FROM trunkinfo ";
		Customer::events($sql);
		$res =& $db->getOne($sql);
		return $res;		
	}
}
?>