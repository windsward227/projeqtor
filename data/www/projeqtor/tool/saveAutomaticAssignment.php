<?php
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

/**
 * ===========================================================================
 * Save a note : call corresponding method in SqlElement Class
 * The new values are fetched in $_REQUEST
 */
require_once "../tool/projeqtor.php";
scriptLog('   ->/tool/saveProviderTerm.php');

$class = RequestHandler::getClass('class');
$id = RequestHandler::getId('id');
$value = RequestHandler::getValue('value');
$obj = new $class($id);
$idProject = $obj->idProject;
$valueSwitch = 1;
$tabResource = array();
$tabAss = array();
$tabAssClose = array();
if($value =='off')$valueSwitch =0;

$result="";
Sql::beginTransaction();
$peName=get_class($obj).'PlanningElement';
if(get_class($obj)=='PeriodicMeeting'){
  $peName = 'MeetingPlanningElement';
}
$obj->$peName->automaticAssignment=$valueSwitch;
$result=$obj->save();

$delay = null;
if(get_class($obj)=="Meeting" || get_class($obj)=="PeriodicMeeting") {
   $delay=workTimeDiffDateTime('2000-01-01T'.$obj->meetingStartTime,'2000-01-01T'.$obj->meetingEndTime);
}

if($valueSwitch){
  $aff = new Affectation ();
  $affList = $aff->getSqlElementsFromCriteria (array('idProject'=>$idProject,'idle'=>'0'));
  foreach ( $affList as $aff ) {
    $tabResource[$aff->idResource]=$aff->idResource;
  }
  $ass = new Assignment();
  $listAss = $ass->getSqlElementsFromCriteria(array('idProject'=>$idProject,'refType'=>$class,'refId'=>$id,'idle'=>'0'));
  foreach ($listAss as $assRes){
    $tabAss[$assRes->idResource]=$assRes->idResource;
  }
  $listAssIdle = $ass->getSqlElementsFromCriteria(array('idProject'=>$idProject,'refType'=>$class,'refId'=>$id,'idle'=>'1'));
  foreach ($listAssIdle as $assResIdle){
    $tabAssClose[$assResIdle->idResource]=$assResIdle->idResource;
  }
  foreach ($tabResource as $idRes){
    if(in_array($idRes, $tabAss))continue;
    $Affres = new Affectable($idRes);
    if(!$Affres->isResource)continue;
    $ass = new Assignment();
    if(in_array($idRes,$tabAssClose)){
      $assClose = $ass->getSingleSqlElementFromCriteria('Assignment', array('idProject'=>$idProject,'refType'=>$class,'refId'=>$id,'idle'=>'1'));
      $assClose->idle = 0;
      $assClose->save();
    }else{
      $res = new Resource($Affres->id);
      $ass->idResource = $idRes;
      $ass->idProject = $idProject;
      $ass->refType= $class;
      $ass->refId = $id;
      $ass->rate = 100;
      $ass->idRole = $res->idRole;
      if(get_class($obj)=="Meeting" || get_class($obj)=="PeriodicMeeting") {
        $ass->assignedWork = $delay;
        $ass->leftWork = $delay;     
      }
      $ass->save();
    }
  }
}

if (!$result) {
  $result=$res;
}
// Message of correct saving
displayLastOperationStatus($result);  
?>