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

// Artefact to avoid scriptLog display even if debug level = 4. Comment the line to have it displayed again.
$noScriptLog=true;
require_once "../tool/projeqtor.php";

$notif = new Notification();
$currentUser = getCurrentUserId ();
$crit = array('idStatusNotification'=>1,'idUser'=>$currentUser);
$notifsList=$notif->getSqlElementsFromCriteria($crit, false);
foreach ($notifsList as $result) {
  if ($result->notificationDate>date("Y-m-d") or ($result->notificationDate==date("Y-m-d") and $result->notificationTime>date('H:i:s'))) continue;
  $titleText = new Html2Text($result->title);
  $title = $titleText->getText();
  $body = new Html2Text($result->content);
  $message=$body->getText();
  $idNotification = $result->id;
  $notification_array="#!#!#!#!#!#$title#!#!#!#!#!#$message#!#!#!#!#!#$idNotification";
  echo $notification_array;
}