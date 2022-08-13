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

/** ===========================================================================
 * Save a note : call corresponding method in SqlElement Class
 * The new values are fetched in $_REQUEST
 */
require_once "../tool/projeqtor.php";
scriptLog('   ->/tool/saveWorkUnit.php');

$mode = RequestHandler::getValue('mode',true,null);
$idWorkToken=RequestHandler::getId('tokentType',false,null);
$idClientContract=RequestHandler::getId('idClientContract',false,null);
$labelTokenClientContract=RequestHandler::getValue('labelToken',false,null);
$quantity=RequestHandler::getValue('quantity',false,null);
$idWorkTokenClientContract=RequestHandler::getId('idWorkTokenClientContract',false,null);

if(!$mode or (($mode == 'delete' or $mode == 'edit')  and !$idWorkTokenClientContract) or ( $mode != 'delete'  and !$idWorkToken )){
  traceHack("saveWorkTokenClientContratc.php called anormaly");
  exit() ;
}

if($mode != 'delete'){
  $workToken= new TokenDefinition($idWorkToken);
  $amount=($workToken->amount*$quantity);
  $duration=($quantity*$workToken->duration);
}


Sql::beginTransaction();
$result = "";

if($mode == 'edit'){
  $workTokenCC = new WorkTokenClientContract($idWorkTokenClientContract);
  $workTokenCC->description=$labelTokenClientContract;
  $workTokenCC->idWorkToken=$idWorkToken;
  $workTokenCC->quantity=$quantity;
  $workTokenCC->duration=$duration;
  $workTokenCC->amount=$amount;
  $res = $workTokenCC->save();
}else if($mode == 'delete'){
  $workTokenCC = new WorkTokenClientContract($idWorkTokenClientContract);
  $res=$workTokenCC->delete();
}else{
  $workTokenCC = new WorkTokenClientContract();
  $workTokenCC->description=$labelTokenClientContract;
  $workTokenCC->quantity=$quantity;
  $workTokenCC->duration=$duration;
  $workTokenCC->amount=$amount;
  $workTokenCC->idClientContract=$idClientContract;
  $workTokenCC->idWorkToken=$idWorkToken;
  $res = $workTokenCC->save();
}
// Message of correct saving
displayLastOperationStatus($res);

?>