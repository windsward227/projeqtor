<?php
use PhpOffice\PhpSpreadsheet\Shared\Date;
/*** COPYRIGHT NOTICE *********************************************************
 *
 * Copyright 2009-2017 ProjeQtOr - Pascal BERNARD - support@projeqtor.org
 * Contributors : -
 * 
 * This file is part of ProjeQtOr.
 * 
 * ProjeQtOr is free software: you can redistribute it and/or modify it under 
 * the terms of the GNU Affero General Public License as published by the Free 
 * Software Foundation, either version 3 of the License, or (at your option) 
 * any later version.
 * 
 * ProjeQtOr is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS 
 * FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for 
 * more details.
 *
 * You should have received a copy of the GNU Affero General Public License along with
 * ProjeQtOr. If not, see <http://www.gnu.org/licenses/>.
 *
 * You can get complete code of ProjeQtOr, other resource, help and information
 * about contributors at http://www.projeqtor.org 
 *     
 *** DO NOT REMOVE THIS NOTICE ************************************************/

include_once '../tool/projeqtor.php';
//echo "workDetail.php";


$paramProject='';
if (array_key_exists('idProject',$_REQUEST)) {
  $paramProject=pq_trim($_REQUEST['idProject']);
  Security::checkValidId($paramProject);
}

$idOrganization = pq_trim(RequestHandler::getId('idOrganization'));
$paramActivityType = pq_trim(RequestHandler::getId('idActivityType'));

$paramTeam='';
if (array_key_exists('idTeam',$_REQUEST)) {
  $paramTeam=pq_trim($_REQUEST['idTeam']);
  Security::checkValidId($paramTeam);
}
$paramYear='';
if (array_key_exists('yearSpinner',$_REQUEST)) {
	$paramYear=$_REQUEST['yearSpinner'];
	$paramYear=Security::checkValidYear($paramYear);
};
$paramMonth='';
if (array_key_exists('monthSpinner',$_REQUEST)) {
	$paramMonth=$_REQUEST['monthSpinner'];
  $paramMonth=Security::checkValidMonth($paramMonth);
} else {
  $paramMonth='01';
}
$paramWeek='';
if (array_key_exists('weekSpinner',$_REQUEST)) {
	$paramWeek=$_REQUEST['weekSpinner'];
	$paramWeek=Security::checkValidWeek($paramWeek);
}
$paramNbMonth='';
if (array_key_exists('NbMonthSpinner',$_REQUEST)) {
    $paramNbMonth=$_REQUEST['NbMonthSpinner'];
    $paramNbMonth=Security::checkValidMonth($paramNbMonth);
    if($paramNbMonth == NULL) {
        $paramNbMonth = $paramMonth;
    }
    else {
        $add = $paramMonth + $paramNbMonth - 1;
        if ($add > 13) $add = 12;
        $paramNbMonth = strval($add);
    }
};
$user=getSessionUser();

$periodType=$_REQUEST['periodType']; // not filtering as data as data is only compared against fixed strings
$periodValue='';

if (array_key_exists('periodValue',$_REQUEST))
{
	$periodValue=$_REQUEST['periodValue'];
	$periodValue=Security::checkValidPeriod($periodValue);
}
$scale=(RequestHandler::isCodeSet('scale'))?RequestHandler::getValue('scale'):null;
if($scale=='twoDate'){
  $startDate=RequestHandler::isCodeSet('startDate')?RequestHandler::getValue("startDate"):null;
  $endDate=RequestHandler::isCodeSet('endDate')?RequestHandler::getValue("endDate"):null;
}
$paramLstRole=RequestHandler::isCodeSet('idRole')?RequestHandler::getValue('idRole'):'';

// Header
$headerParameters="";
if ($paramProject!="") {
  $headerParameters.= i18n("colIdProject") . ' : ' . htmlEncode(SqlList::getNameFromId('Project', $paramProject)) . '<br/>';
}
if ($idOrganization!="") {
  $headerParameters.= i18n("colIdOrganization") . ' : ' . htmlEncode(SqlList::getNameFromId('Organization',$idOrganization)) . '<br/>';
}
if ($paramTeam!="") {
  $headerParameters.= i18n("colIdTeam") . ' : ' . htmlEncode(SqlList::getNameFromId('Team', $paramTeam)) . '<br/>';
}
if ($paramActivityType!="") {
  $headerParameters.= i18n("colIdActivityType") . ' : ' . htmlEncode(SqlList::getNameFromId('ActivityType', $paramActivityType)) . '<br/>';
}
if ($periodType=='year' or $periodType=='month' or $periodType=='week') {
  $headerParameters.= i18n("year") . ' : ' . $paramYear . '<br/>';
}
if($paramLstRole!=""){
  $ass= new Assignment();
  $assTable=$ass->getDatabaseTableName();
  $whereRole=" and idAssignment in (Select ass.id From $assTable as ass where ass.idRole in (0";
  $headerParameters.= i18n("colIdRole") . ' : ' ;
  $cpt=0;
  foreach ($paramLstRole as $role){
   if ($cpt>0) $headerParameters.=', ';
   $headerParameters.=htmlEncode(SqlList::getNameFromId('Role', $role));
   $whereRole.=','.$role;
   $cpt++;
  }
  $whereRole.="))";
  $headerParameters.='<br/>';
}
if($scale=='twoDate'){
  if($startDate)$headerParameters.= i18n("startDate") . ' : ' . htmlFormatDate($startDate) . '<br/>';
  if($endDate)$headerParameters.= i18n("endDate") . ' : ' . htmlFormatDate($endDate) . '<br/>';
}


//ADD qCazelles - Report fiscal year - Ticket #128
if ($periodType=='year' and $paramMonth!="01") {
  if (!$paramMonth ) {
    echo '<div style="background: #FFDDDD;font-size:150%;color:#808080;text-align:center;padding:20px">';
    echo i18n('messageNoData',array(i18n('month'))); // TODO i18n message
    echo '</div>';
    if (!empty($cronnedScript)) goto end; else exit;
  } else {
    $headerParameters.= i18n("startMonth") . ' : ' . i18n(date('F', mktime(0,0,0,$paramMonth,10))) . '<br/>';
    $headerParameters.= "to" . ' : ' . i18n(date('F', mktime(0,0,0,$paramNbMonth,10))) . '<br/>';
  }
}
//END ADD qCazelles - Report fiscal year - Ticket #128
if ($periodType=='month') {
  $headerParameters.= i18n("month") . ' : ' . $paramMonth . '<br/>';
}
if ( $periodType=='week') {
  $headerParameters.= i18n("week") . ' : ' . $paramWeek . '<br/>';
}
if (isset($outMode) and $outMode=='excel') {
  $headerParameters.=pq_str_replace('- ','<br/>',Work::displayWorkUnit()).'<br/>';
}
include "header.php";

$where="(".getAccesRestrictionClause('Activity',false,true,true,true)." or idProject in ".Project::getAdminitrativeProjectList().")";
//$where="1=1 ";
$where.=($periodType=='week')?" and week='" . $periodValue . "'":'';
$where.=($periodType=='month')?" and month='" . $periodValue . "'":'';
//CHANGE qCazelles - Report start month - Ticket #128
//Old
//$where.=($periodType=='year')?" and year='" . $periodValue . "'":'';
//New
if ($periodType=='year') {
    if ($paramMonth<10) $paramMonth='0'.intval($paramMonth);
    if ($paramNbMonth<10) $paramNbMonth='0'.intval($paramNbMonth);
    $where.=" and ((year='" . $periodValue . "' and month>='" . $periodValue.$paramMonth . "' and month<='" . $periodValue.$paramNbMonth ."')".
        " or (year='" . ($periodValue + 1) . "' and month<'" . ($periodValue + 1) . $paramMonth . "' 
          and month>'" . ($periodValue + 1) . $paramNbMonth . "' ))";
}
//END CHANGE qCazelles - Report start month - Ticket #128
if($scale=='twoDate'){
  
  if($startDate and $endDate){
    $where.=" and workDate between '$startDate' and '$endDate' ";
  }
  else if ($startDate and !$endDate)$where.=" and workDate >= '$startDate'";
  else $where.=" and workDate <= '$endDate'";
}

if ($paramProject!='') {
  $where.=  "and idProject in " . getVisibleProjectsList(false, $paramProject) ;
}

if(isset($whereRole)){
  $where.=$whereRole;
}

$order="";
//echo $where;
$work=new Work();
$lstWork=$work->getSqlElementsFromCriteria(null,false, $where, $order);
$result=array();
$activities=array();
$project=array();
$description=array();
$parent=array();
$resources=array();
$sumActi=array();
foreach ($lstWork as $work) {
  $act = new Activity($work->refId);
  $actType = new activityType($act->idActivityType);
  if ($paramActivityType == "" or ($paramActivityType == $act->idActivityType and $work->refType=='Activity')) {
    if (!array_key_exists($work->idResource, $resources)) {
      $resources[$work->idResource] = SqlList::getNameFromId('Resource', $work->idResource);
    }
    $refType = $work->refType;
    $refId = $work->refId;
    $key = $refType . "#" . $refId;
    if (!array_key_exists($key, $activities)) {
      if ($refType) {
        $obj = new $refType($refId);
      } else {
        $obj = new Ticket();
      }
      $key = SqlList::getFieldFromId('Project', $obj->idProject, 'sortOrder') . '-' . $refType . "#" . $refId;
      $activities[$key] = $obj->name;
      $description[$key] = $obj->description;
      if ($refType == 'Project') {
        $parent[$key] = "[" . i18n('Project') . "]";
      } else {
        if (property_exists($obj, 'idActivity') and $obj->idActivity) {
          $parent[$key] = SqlList::getNameFromId('Activity', $obj->idActivity);
        } else {
          $parent[$key] = "";
        }
      }
      $project[$key] = SqlList::getNameFromId('Project', $obj->idProject);
    }
    if (!array_key_exists($work->idResource, $result)) {
      $result[$work->idResource] = array();
    }
    if (!array_key_exists($key, $result[$work->idResource])) {
      $result[$work->idResource][$key] = 0;
    }
    $result[$work->idResource][$key] += $work->work;
  }
}
ksort($activities);
if (checkNoData($result)) if (!empty($cronnedScript)) goto end; else exit;

// title
echo '<table style="width:95%" align="center">';
echo '<tr>';
echo '<td class="reportTableHeader" rowspan="2" style="width:20%" '.excelFormatCell('header',60).'>' . i18n('Resource') . '</td>';
echo '<td class="reportTableHeader" rowspan="2" style="width:10%" '.excelFormatCell('header',20).'>' . i18n('colWork') . '</td>';
echo '<td class="reportTableHeader" colspan="3" '.excelFormatCell('header',120).'>' . i18n('Activity') . '</td>';
echo '</tr><tr>';
echo '<td class="reportTableColumnHeader" style="width:20%" '.excelFormatCell('subheader',40).'>' . i18n('colIdProject') . '</td>';
echo '<td class="reportTableColumnHeader" style="width:25%" '.excelFormatCell('subheader',40).'>' . i18n('colName') . '</td>';
//echo '<td class="reportTableColumnHeader" style="width:25%">' . i18n('colDescription') . '</td>';
echo '<td class="reportTableColumnHeader" style="width:25%" '.excelFormatCell('subheader',40).'>' . i18n('colParentActivity') . '</td>';
echo '</tr>';

$sum=0;
asort($resources);
//gautier #4342
if($idOrganization){
  $orga = new Organization($idOrganization);
  $listResOrg=$orga->getResourcesOfAllSubOrganizationsListAsArray();
  foreach ($resources as $idR=>$nameR){
    if(! in_array($idR, $listResOrg))unset($resources[$idR]);
  }
}
foreach ($resources as $idR=>$nameR) {
	if ($paramTeam) {
    $res=new ResourceAll($idR);//florent ticket #5038
  }
  if (!$paramTeam or $res->idTeam==$paramTeam) {
	  $sumRes=0;
	  echo '<tr>';
	  echo '<td class="reportTableLineHeader" style="width:20%" rowspan="' . (count($result[$idR]) +1) . '" '.excelFormatCell('subheader',null,null,null,null,'left').'>' . htmlEncode($nameR) . '</td>';
	  foreach ($activities as $key=>$nameA) {
	    if (array_key_exists($idR, $result)) {
	      if (array_key_exists($key, $result[$idR])) {
	        $val=$result[$idR][$key];
	        $sumRes+=$val; 
	        $sum+=$val;
	        echo '<td class="reportTableData" style="width:10%" '.excelFormatCell('data',null,null,null,null,null,null,null,'work').'>' . Work::displayWorkWithUnit($val). '</td>';
	        echo '<td class="reportTableData" style="width:20%; text-align:left;" '.excelFormatCell('data',null,null,null,null,'left').'>' . htmlEncode($project[$key]) . '</td>';
	        echo '<td class="reportTableData" style="width:25%; text-align:left;" '.excelFormatCell('data',null,null,null,null,'left').'>' . htmlEncode($nameA) . '</td>'; 
	//        echo '<td class="reportTableData" style="width:25%; text-align:left;">' . htmlEncode($description[$key]) . '</td>'; 
	        echo '<td class="reportTableData" style="width:25%; text-align:left;" '.excelFormatCell('data',null,null,null,null,'left').'>' . htmlEncode($parent[$key]) . '</td>'; 
	        echo '</tr><tr>';
	      } 
	    }
	  }
    echo '<td class="reportTableColumnHeader" '.excelFormatCell('subheader',null,null,null,null,null,null,null,'work').'>' . Work::displayWorkWithUnit($sumRes) . '</td>';
    echo '<td class="reportTableColumnHeader" style="text-align:left;" colspan="3" '.excelFormatCell('subheader',null,null,null,null,'left').'>' . i18n('sum') . " " . $nameR . '</td>';
    echo '</tr>';
  }
}
echo '<tr>';
echo '<td class="reportTableHeader" '.excelFormatCell('header',null,null,null,null,'left').'>' . i18n('sum') . '</td>';
echo '<td class="reportTableHeader" '.excelFormatCell('header',null,null,null,null,null,null,null,'work').' >' . Work::displayWorkWithUnit($sum) . '</td>';
echo '<td colspan="3"></td>';
echo '</tr>';
echo '</table>';

end:
