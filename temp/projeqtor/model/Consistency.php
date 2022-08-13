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

/* ============================================================================
 * Technical class to implement consistency checks
 */ 
require_once('_securityCheck.php');

class Consistency {

   /** ==========================================================================
   * Constructor
   * @param $id the id of the object in the database (null if not stored yet)
   * @return void
   */ 
  function __construct($id = NULL, $withoutDependentObjects=false) {

  }

   /** ==========================================================================
   * Destructor
   * @return void
   */ 
  function __destruct() {
    
  }
  
  // =================================================================================================================
  // WBS Ordering
  // =================================================================================================================
  
  /**
   * Check consistency of WBS ordering
   * @param string $display
   * @param string $correct
   */
  public static function checkWbs($correct=false,$trace=false) {
    debugTraceLog("checkWbs - correct WBS index in Projects Structure");
    $pe=new PlanningElement();
    $peList=$pe->getSqlElementsFromCriteria(null,null,"1=1",'wbsSortable asc');
    $lastWbs='';
    $lastPe=$pe;
    $errors=0;
    $arrayWbs=array();
    foreach ($peList as $idx=>$pe) {
      $currentWbs=$pe->wbsSortable;
      if ($trace) echo "$pe->wbsSortable - $pe->refType #$pe->refId - $pe->refName<br/>";
      if (!$pe->refType or !$pe->refId) {
        displayError(i18n("checkPlanningElementEmpty",array(i18n($pe->refType),$pe->refId,$pe->id)));
        $errors++;
        if ($correct) {
          $resultDeletePE=$pe->delete();
          if (getLastOperationStatus($resultDeletePE=="OK")) {
            displayOK(i18n("checkFixed"),true);
          } else {
            displayMsg(i18n("checkNotFixed"),true);
          }
        }
        continue;
      }
      // check for duplicate WBS
      if ($pe->wbsSortable==$lastWbs) {
        displayError(i18n("checkWbsDuplicate",array($lastWbs,i18n($lastPe->refType),$lastPe->refId,i18n($pe->refType),$pe->refId)));
        $errors++;
        if ($correct) {
          self::fixOrder($pe,"duplicate",$peList,$idx,get_class($pe));
        }
      }
      // Check Parent
      $parentWbs='';
      if ($pe->topRefType and $pe->topRefId) {
        $key=$pe->topRefType.'#'.$pe->topRefId;
        $parentWbs=(isset($arrayWbs[$key]))?$arrayWbs[$key]:'';
        if (!$parentWbs) { // Possibly just incorrect order
          $parentPe=SqlElement::getSingleSqlElementFromCriteria('PlanningElement', array('refType'=>$pe->topRefType, 'refId'=>$pe->topRefId));
          if ($parentPe and $parentPe->id) {
            $parentWbs=$parentPe->wbsSortable;
          }
        }
        if ($parentWbs=='') {
          displayError(i18n("checkWbsParentNotFound",array($pe->topRefType,$pe->topRefId, i18n($pe->refType), $pe->refId)));
          $errors++;
          if ($correct) {
            displayError(i18n("checkCannotFix"),true);
          }
        }
      }
      if ($parentWbs and $parentWbs!=pq_substr($pe->wbsSortable,0,pq_strlen($parentWbs))) {
        displayError(i18n("checkWbsParentIssue",array($pe->wbsSortable,i18n($pe->refType),$pe->refId,$parentWbs,$pe->topRefType,$pe->topRefId)));
        $errors++;
        if ($correct) {
          self::fixOrder($pe,"parent",$peList,$idx,get_class($pe));
        }
      } else if ($parentWbs and pq_strlen($pe->wbsSortable)!=pq_strlen($parentWbs)+6) {
        displayError(i18n("checkWbsParentIssue",array($pe->wbsSortable,i18n($pe->refType),$pe->refId,$parentWbs,$pe->topRefType,$pe->topRefId)));
        $errors++;
        if ($correct) {
          self::fixOrder($pe,"parent",$peList,$idx,get_class($pe));
        }
      }
      // Check Order
      $order=pq_substr($pe->wbsSortable,-5);
      if ($lastWbs==$parentWbs) { // Previous is parent, so must be 001
        if (intval($order)!=1 and ($pe->wbsSortable!='00000' or $pe->refType!='Project')) {
          displayError(i18n("checkWbsFirst", array($pe->wbsSortable,i18n($pe->refType),$pe->refId)));
          $errors++;
          if ($correct) {
            self::fixOrder($pe,"first",$peList,$idx,get_class($pe));
          }
        }
      } else if (pq_substr($lastWbs,0,-6)==$parentWbs) { // Previous has same root (same parent), number must be is sequence
        if (intval($order)!=intval(pq_substr($lastWbs,-5))+1) {
          displayError(i18n("checkWbsOrder",array($pe->wbsSortable, i18n($pe->refType), $pe->refId, $lastWbs)));
          $errors++;
          if ($correct) {
            self::fixOrder($pe,"order",$peList,$idx,get_class($pe));
          }
        }
      } else { // Change root, current numbering must be is sequence
        $rootPrev=pq_substr($lastWbs,0,pq_strlen($pe->wbsSortable));
        if (intval($order)!=intval(pq_substr($rootPrev,-5))+1) {
          displayError(i18n("checkWbsOrder",array($pe->wbsSortable, i18n($pe->refType), $pe->refId,$lastWbs)));
          $errors++;
          if ($correct) {
            self::fixOrder($pe,"order",$peList,$idx,get_class($pe));
          }
        }
      }
      // Check displayed wbs compared to wbsSortable
  
      // Check project order
      if ($pe->refType=='Project') {
        $prj=new Project($pe->refId);
        $pe=$prj->ProjectPlanningElement;
        if ($prj->sortOrder!=$pe->wbsSortable) {
          displayError(i18n("checkWbsSortOrderProject",array($prj->id,$prj->sortOrder,$pe->wbsSortable)));
          $errors++;
          if ($correct) {
            $prj->sortOrder=$pe->wbsSortable;
            $res=$prj->save();
            if (getLastOperationStatus($res)=='OK'  or getLastOperationStatus($res)=='NO_CHANGE') displayOK(i18n("checkFixed"),true);
            else displayError($res,true);
          }
        }
  
      }
      // Continue
      $key=$pe->refType.'#'.$pe->refId;
      $arrayWbs[$key]=$currentWbs;
      $lastWbs=$currentWbs;
      $lastPe=$pe;
    }
    if (!$errors) {
      displayOK(i18n("checkNoError"));
    }
  }
  
  private static function fixOrder($el, $issue,$elList,$idx,$class) {
    debugTraceLog("fixOrder - $issue");
    
    if($class=="Budget"){
    	$actual = new Budget($el->id);
    	$bs = "bbs";
    	$top="idBudget";
    	$sortableField="bbsSortable";
    }else if($class=="Skill"){
    	$actual = new Skill($el->id);
    	$bs = "sbs";
    	$top="idSkill";
    	$sortableField="sbsSortable";
    }else{
    	$actual = new PlanningElement($el->id);
    	$bs = "wbs";
    	$top="topId";
    	$sortableField="wbsSortable";
    }
//     $actual=($class=="Budget")?new Budget($el->id):new PlanningElement($el->id);
//     $bs=($class=="Budget")?"bbs":"wbs";
//     $top=($class=="Budget")?"idBudget":"topId";
//     $sortableField=($class=="Budget")?"bbsSortable":"wbsSortable";
    if ($el->$sortableField!=$actual->$sortableField) {
      displayOK(i18n("checkFixed"),true);
      return;
    }
    $action="unknown";
    $elNext=null;
    $elPrec=null;
    if ($issue=="duplicate" or $issue=="parent") { // Duplicate or inconsistent with Parent => just get a good one (order is sure incorrect)
      $action="recalculate";
    } else if ($issue=="first") {
      $action="recalculateLevel";
    } else if ($issue=="order") {
      $action="recalculate";
      $cur=$idx-1;
      while ($action=="recalculate" and $cur>=0) {
        $elPrec=$elList[$cur];
        if (pq_substr($el->$sortableField,0,pq_strlen($el->$sortableField)-5)!=pq_substr($elPrec->$sortableField,0,pq_strlen($el->$sortableField)-5)) {
          $cur=-1;
          break;
        }
        if (pq_strlen($el->$sortableField)==pq_strlen($elPrec->$sortableField)) {
          $action="moveFromPrec";
        }
        $cur--;
      }
    } else {
      displayError(i18n("checkCannotFix"),true);
    }
    if(!($class=='PlanningElement' and $el->refType=="Project" and Project::isTheLeaveProject($el->refId))){
      if ($action=="recalculate" ) {
        $el->$bs=null;
        $el->$sortableField=null;
        $res=$el->save();
        if (getLastOperationStatus($res)=='OK' or getLastOperationStatus($res)=='NO_CHANGE') displayOK(i18n("checkFixed"),true);
        else displayError($res,true);
      } else if ($action=='moveFromPrec' and $elPrec) {
        $res=$el->moveTo($elPrec->id,'after');    // moveTo function
        if (getLastOperationStatus($res)=='OK' or getLastOperationStatus($res)=='NO_CHANGE') displayOK(i18n("checkFixed"),true);
        else displayError($res,true);
      } else if ($action=="recalculateLevel" and $el->$top) {
        $where="$top=".$el->$top;
        $levelList=$el->getSqlElementsFromCriteria(null,null,$where,$sortableField);
        if (count($levelList)==1) {
          $el->$bs=null;
          $el->$sortableField=null;
          $res=$el->save();
          if (getLastOperationStatus($res)=='OK' or getLastOperationStatus($res)=='NO_CHANGE') displayOK(i18n("checkFixed"),true);
          else displayError($res,true);
        } else if (count($levelList)>1) {
            $first=$levelList[0];
            $second=$levelList[1];
            $res=$second->moveTo($first->id,'before');
            $first=new $class($first->id);
            $second=new $class($second->id);
            $res=$first->moveTo($second->id,'before');
            if (getLastOperationStatus($res)=='OK' or getLastOperationStatus($res)=='NO_CHANGE') displayOK(i18n("checkFixed"),true);
            else displayError($res,true);
        }
      } else {
        if ($issue!='') {
          displayError(i18n("checkCannotFix"),true);
        }
      }
    }
  }
  
  // =================================================================================================================
  // BBS Ordering
  // =================================================================================================================
  
  /**
   * Check consistency of WBS ordering
   * @param string $display
   * @param string $correct
   */
  public static function checkBbs($correct=false,$trace=false) {
    debugTraceLog("checkBbs - correct BBS index in Budgets Structure");
    $budget=new Budget();
    $budgetList=$budget->getSqlElementsFromCriteria(null,null,"1=1",'bbsSortable asc');
    $lastBbs='';
    $lastBudget=$budget;
    $errors=0;
    $arrayBbs=array();
    foreach ($budgetList as $idx=>$bud) {
      $currentBbs=$bud->bbsSortable;
      if ($trace) echo "$bud->bbsSortable -Budget #$bud->id - $bud->name <br/>";

      // check for duplicate BBS
      if ($bud->bbsSortable==$lastBbs) {
        displayError(i18n("checkBbsDuplicate",array($lastBbs,$lastBudget->id,$bud->id)));
        $errors++;
        if ($correct) {
          //======= Fix order duplicate BBS ======///
          self::fixOrder($bud,"duplicate",$budgetList,$idx,get_class($bud));
        }
      }
      // Check Parent
      $parentBbs='';
      if ($bud->idBudget) {
        $key=$bud->idBudget;
        $parentBbs=(isset($arrayBbs[$key]))?$arrayBbs[$key]:'';
        if (!$parentBbs) { // Possibly just incorrect order
          $parentBudget=SqlElement::getSingleSqlElementFromCriteria('Budget', array('id'=>$key));
          if ($parentBudget and $parentBudget->id) {
            $parentBbs=$parentBudget->bbsSortable;
          }
        }
        if ($parentBbs=='') {
          displayError(i18n("checkBbsParentNotFound",array($key,$bud->id)));
          $errors++;
          if ($correct) {
            displayError(i18n("checkCannotFix"),true);
          }
        }
      }
      
      if ($parentBbs and $parentBbs!=pq_substr($bud->bbsSortable,0,pq_strlen($parentBbs))) {
        displayError(i18n("checkBbsParentIssue",array($bud->bbsSortable,$bud->id,$parentBbs,$bud->idBudget)));
        $errors++;
        if ($correct) {
          //======= Fix order to parent bbs ======///
          self::fixOrder($bud,"parent",$budgetList,$idx,get_class($bud));
        }
      } else if ($parentBbs and pq_strlen($bud->bbsSortable)!=pq_strlen($parentBbs)+6) {
        displayError(i18n("checkBbsParentIssue",array($bud->bbsSortable,$bud->id,$parentBbs,$bud->idBudget)));
        $errors++;
        if ($correct) {
          //======= Fix order to parent bbs ======///
          self::fixOrder($bud,"parent",$budgetList,$idx,get_class($bud));
        }
      }
      // Check Order
      $order=pq_substr($bud->bbsSortable,-5);
      if ($lastBbs==$parentBbs) { // Previous is parent, so must be 001
        if (intval($order)!=1 and $bud->bbsSortable!='00000' ) {
          displayError(i18n("checkBbsFirst", array($bud->bbsSortable,$bud->id)));
          $errors++;
          if ($correct) {
            //======= Fix order, move on first position ======///
            self::fixOrder($bud,"first",$budgetList,$idx,get_class($bud));
          }
        }
      } else if (pq_substr($lastBbs,0,-6)==$parentBbs) { // Previous has same root (same parent), number must be is sequence
        if (intval($order)!=intval(pq_substr($lastBbs,-5))+1) {
          displayError(i18n("checkBbsOrder",array($bud->bbsSortable, $bud->id, $lastBbs)));
          $errors++;
          if ($correct) {
            //======= Fix order wrong sequence ======///
            self::fixOrder($bud,"order",$budgetList,$idx,get_class($bud));
          }
        }
      } else { // Change root, current numbering must be is sequence
        $rootPrev=pq_substr($lastBbs,0,pq_strlen($bud->bbsSortable));
        if (intval($order)!=intval(pq_substr($rootPrev,-5))+1) {
          displayError(i18n("checkBbsOrder",array($bud->bbsSortable,$bud->id,$lastBbs)));
          $errors++;
          if ($correct) {
            //======= Fix order to numbering must be is sequence ======///
            self::fixOrder($bud,"order",$budgetList,$idx,get_class($bud));
          }
        }
      }
  
      // Continue
      $key=$bud->id;
      $arrayBbs[$key]=$currentBbs;
      $lastBbs=$currentBbs;
      $lastBudget=$bud;
    }
    if (!$errors) {
      displayOK(i18n("checkNoError"));
    }
  }
  
  // =================================================================================================================
  // SBS Ordering
  // =================================================================================================================
  
  /**
   * Check consistency of WBS ordering
   * @param string $display
   * @param string $correct
   */
  public static function checkSbs($correct=false,$trace=false) {
  	debugTraceLog("checkSbs - correct SBS index in Skills Structure");
  	$skill=new Skill();
  	$skillList=$skill->getSqlElementsFromCriteria(null,null,"1=1",'sbsSortable asc');
  	$lastSbs='';
  	$lastSkill=$skill;
  	$errors=0;
  	$arraySbs=array();
  	foreach ($skillList as $idx=>$skl) {
  		$currentSbs=$skl->sbsSortable;
  		if ($trace) echo "$skl->sbsSortable -Skill #$skl->id - $skl->name <br/>";
  
  		// check for duplicate SBS
  		if ($skl->sbsSortable==$lastSbs) {
  			displayError(i18n("checkSbsDuplicate",array($lastSbs,$lastSkill->id,$skl->id)));
  			$errors++;
  			if ($correct) {
  				//======= Fix order duplicate SBS ======///
  				self::fixOrder($skl,"duplicate",$skillList,$idx,get_class($skl));
  			}
  		}
  		// Check Parent
  		$parentSbs='';
  		if ($skl->idSkill) {
  			$key=$skl->idSkill;
  			$parentSbs=(isset($arraySbs[$key]))?$arraySbs[$key]:'';
  			if (!$parentSbs) { // Possibly just incorrect order
  				$parentSkill=SqlElement::getSingleSqlElementFromCriteria('Skill', array('id'=>$key));
  				if ($parentSkill and $parentSkill->id) {
  					$parentSbs=$parentSkill->sbsSortable;
  				}
  			}
  			if ($parentSbs=='') {
  				displayError(i18n("checkSbsParentNotFound",array($key,$skl->id)));
  				$errors++;
  				if ($correct) {
  					displayError(i18n("checkCannotFix"),true);
  				}
  			}
  		}
  
  		if ($parentSbs and $parentSbs!=pq_substr($skl->sbsSortable,0,pq_strlen($parentSbs))) {
  			displayError(i18n("checkSbsParentIssue",array($skl->sbsSortable,$skl->id,$parentSbs,$skl->idSkill)));
  			$errors++;
  			if ($correct) {
  				//======= Fix order to parent bbs ======///
  				self::fixOrder($skl,"parent",$skillList,$idx,get_class($skl));
  			}
  		} else if ($parentSbs and pq_strlen($skl->sbsSortable)!=pq_strlen($parentSbs)+6) {
  			displayError(i18n("checkSbsParentIssue",array($skl->sbsSortable,$skl->id,$parentSbs,$skl->idSkill)));
  			$errors++;
  			if ($correct) {
  				//======= Fix order to parent bbs ======///
  				self::fixOrder($skl,"parent",$skillList,$idx,get_class($skl));
  			}
  		}
  		// Check Order
  		$order=pq_substr($skl->sbsSortable,-5);
  		if ($lastSbs==$parentSbs) { // Previous is parent, so must be 001
  			if (intval($order)!=1 and $skl->sbsSortable!='00000' ) {
  				displayError(i18n("checkSbsFirst", array($skl->sbsSortable,$skl->id)));
  				$errors++;
  				if ($correct) {
  					//======= Fix order, move on first position ======///
  					self::fixOrder($skl,"first",$skillList,$idx,get_class($skl));
  				}
  			}
  		} else if (pq_substr($lastSbs,0,-6)==$parentSbs) { // Previous has same root (same parent), number must be is sequence
  			if (intval($order)!=intval(pq_substr($lastSbs,-5))+1) {
  				displayError(i18n("checkSbsOrder",array($skl->sbsSortable, $skl->id, $lastSbs)));
  				$errors++;
  				if ($correct) {
  					//======= Fix order wrong sequence ======///
  					self::fixOrder($skl,"order",$skillList,$idx,get_class($skl));
  				}
  			}
  		} else { // Change root, current numbering must be is sequence
  			$rootPrev=pq_substr($lastSbs,0,pq_strlen($skl->sbsSortable));
  			if (intval($order)!=intval(pq_substr($rootPrev,-5))+1) {
  				displayError(i18n("checkSbsOrder",array($skl->sbsSortable,$skl->id,$lastSbs)));
  				$errors++;
  				if ($correct) {
  					//======= Fix order to numbering must be is sequence ======///
  					self::fixOrder($skl,"order",$skillList,$idx,get_class($skl));
  				}
  			}
  		}
  
  		// Continue
  		$key=$skl->id;
  		$arraySbs[$key]=$currentSbs;
  		$lastSbs=$currentSbs;
  		$lastSkill=$skl;
  	}
  	if (!$errors) {
  		displayOK(i18n("checkNoError"));
  	}
  }
  
  // =================================================================================================================
  // Work Duplicate
  // =================================================================================================================
  
  public static function checkDuplicateWork($correct=false,$trace=false) {
    debugTraceLog("checkDuplicateWork - Check for duplicates on idAssignment, idResource, refType, refId, idWorkElement");
    $errors=0;
    // Direct Query : valid here for technical needs on grouping
    $work=new Work();
    $workTable=$work->getDatabaseTableName();
    $query="SELECT idAssignment as idassignment, idResource as idResource, refType as reftype, refId as refid, idWorkElement as idworkelement, day as day, count(*) as cpt from $workTable group by idAssignment, idResource, refType, refId, idWorkElement, day having count(*)>1";
    $result=Sql::query($query);
    while ($line = Sql::fetchLine($result)) {
      $idAss=$line['idassignment'];
      $refType=$line['reftype'];
      $refId=$line['refid'];
      $idWork=$line['idworkelement'];
      $day=$line['day'];
      $cpt=$line['cpt'];
      $lstWork=$work->getSqlElementsFromCriteria(array('idAssignment'=>$idAss,'day'=>$day, 'refType'=>$refType, 'refId'=>$refId, 'idWorkElement'=>$idWork),null,'id asc');
      $wk=reset($lstWork);
      $resName=SqlList::getNameFromId('Affectable', $wk->idResource);
      displayError(i18n("checkDuplicateWorkFound",array($resName,htmlFormatDate($wk->workDate),i18n($wk->refType),$wk->refId)));
      $errors++;
      if ($correct) {
        $nb=0;
        $res='';
        foreach ($lstWork as $work) {
          if ($nb==0 and $work->work!=0) {
            $nb++;
            // Do not delete first not null
          } else {            
            $res=$work->delete();
          }
          
        }
        if (getLastOperationStatus($res)=='OK'  or getLastOperationStatus($res)=='NO_CHANGE') displayOK(i18n("checkFixed"),true);
        else displayError($res,true);
      }
    }
    if (!$errors) {
      displayOK(i18n("checkNoError"));
      
    }
  }
  
  // =================================================================================================================
  // Work On Ticket
  // =================================================================================================================
  
  public static function checkWorkOnTicket($correct=false,$trace=false) {
    debugTraceLog("checkWorkOnTicket - Check work on WorkElement compared to sum of Work");
    $errors=0;
    // Direct Query : valid here for technical needs on grouping
    $work=new Work();
    $workTable=$work->getDatabaseTableName();
    $we=new workElement();
    $weTable=$we->getDatabaseTableName();
    $tk=new Ticket();
    $tkTable=$tk->getDatabaseTableName();
    $query="SELECT we.refType as reftype, we.refId as refid, we.realWork as realwork, (select sum(work) from $workTable w where w.idWorkElement=we.id) as sumwork from $weTable we where realwork!=(select sum(work) from $workTable w where w.idWorkElement=we.id) ";
    $result=Sql::query($query);
    while ($line = Sql::fetchLine($result)) {
      $refType=$line['reftype'];
      $refId=$line['refid'];
      $realWork=$line['realwork'];
      $sumWork=$line['sumwork'];
      if (Work::displayWorkWithUnit($realWork)==Work::displayWorkWithUnit($sumWork)) continue; // It is just a rounding issue
      displayError(i18n("checkIncorrectWork",array(i18n($refType),$refId,Work::displayWorkWithUnit($realWork),Work::displayWorkWithUnit($sumWork))));
      $errors++;
      if ($correct) {
        $elt=new $refType($refId);
        $res=$elt->save();
        $we=SqlElement::getSingleSqlElementFromCriteria('WorkElement', array('refType'=>$refType, 'refId'=>$refId));
        if ($we->id) {
          $we->realWork=$sumWork;
          $resWe=$we->simpleSave();
        }
        if (getLastOperationStatus($res)=='OK' or (isset($resWe) and getLastOperationStatus($resWe)=='OK') ) {
          displayOK(i18n("checkFixed"),true);
        } else {
          displayMsg(i18n("checkNotFixed"),true);
          $query="SELECT idResource as idres, sum(work) as sumwork from $workTable w where w.refType='$refType' and w.refId=$refId group by idResource";
          $resultRes=Sql::query($query);
          while ($lineRes = Sql::fetchLine($resultRes)) {
            $idRes=$lineRes['idres'];
            $sumWork=$lineRes['sumwork'];
            displayMsg('&nbsp;-&nbsp;'.SqlList::getNameFromId('Affectable', $idRes).' : '.Work::displayWorkWithUnit($sumWork),true);
          } 
        }
      }
    }
    $query="SELECT w.id as idwork, w.refType as reftype, w.refId as refid, w.workDate as date, w.idResource as res, (select idActivity from $tkTable t where t.id=w.refId) as idact from $workTable w where w.idAssignment is null and (select idActivity from $tkTable t where t.id=w.refId) is not null";
    $result=Sql::query($query);
    while ($line = Sql::fetchLine($result)) {
      $refType=$line['reftype'];
      $refId=$line['refid'];
      $idActivity=$line['idact'];
      $date=$line['date'];
      $workId=$line['idwork'];
      $idRes=$line['res'];
      displayError(i18n("checkIncorrectAssignmentOnTicket",array(i18n($refType),$refId,$idActivity,htmlFormatDate($date,false,false),SqlList::getNameFromId('Resource', $idRes))));
      $errors++;
      if ($correct) {
        $obj=new $refType($refId);
        $work=new Work($workId);
        $work->refType='Activity';
        $work->refId=$idActivity;
        if (!$work->idWorkElement) {
          $work->idWorkElement=$obj->WorkElement->id;
        }
        $ass=new Assignment();
        $assList=$ass->getSqlElementsFromCriteria(array('idResource'=>$idRes,'refType'=>'Activity','refId'=>$idActivity));
        if (count($assList)>0) {
          $ass=$assList[0];
          $work->idAssignment=$ass->id;
          $res=$work->save();
          if (getLastOperationStatus($res)=='OK') {
            displayOK(i18n("checkFixed"),true);
          }
        }
        
      }
    }
    if (!$errors) {
      displayOK(i18n("checkNoError"));
  
    }
  }
  
  // =================================================================================================================
  // Work On Activity
  // =================================================================================================================
  
  public static function checkWorkOnActivity($correct=false,$trace=false) {
    debugTraceLog("checkWorkOnActivity - Check work on PlanningElement compared to sum of Work");
    $errors=0;
    // Direct Query : valid here for technical needs on grouping
    $work=new Work();
    $workTable=$work->getDatabaseTableName();
    $pe=new PlanningElement();
    $peTable=$pe->getDatabaseTableName();
    $we=new WorkElement();
    $weTable=$we->getDatabaseTableName();
    $query="SELECT pe.refType as reftype, pe.refId as refid, pe.realWork as realwork, pe.leftWork as leftwork, pe.plannedWork as plannedwork,"
          ."  coalesce((select sum(work) from $workTable w where w.refType=pe.refType and w.refId=pe.refId),0)"
          ."+coalesce((select sum(pesum.realWork) from $peTable pesum where pesum.topId=pe.id),0)"
          ."+coalesce((select sum(coalesce(wesum.realWork,0)) from $weTable wesum where pe.refType='Project' and wesum.idProject=pe.refId and wesum.idActivity is null),0)"
          ." as sumwork "
          ."FROM $peTable pe "
          ."WHERE pe.isManualProgress=0 and ( (pe.realWork+pe.leftWork)!=pe.plannedWork or pe.realwork!="
           ."coalesce((select sum(work) from $workTable w where w.refType=pe.refType and w.refId=pe.refId),0)"
           ."+coalesce((select sum(pesum.realWork) from $peTable pesum where pesum.topId=pe.id),0)"
           ."+coalesce((select sum(coalesce(wesum.realWork,0)) from $weTable wesum where pe.refType='Project' and wesum.idProject=pe.refId and wesum.idActivity is null),0)"
           ." )";
    $result=Sql::query($query);
    while ($line = Sql::fetchLine($result)) {
      $refType=$line['reftype'];
      $refId=$line['refid'];
      $realWork=$line['realwork'];
      $leftWork=$line['leftwork'];
      $plannedWork=$line['plannedwork'];
      $sumWork=$line['sumwork'];      
      if(!$sumWork){
        $sumWork = 0;
      }
      if (Work::displayWorkWithUnit($realWork)==Work::displayWorkWithUnit($sumWork) and Work::displayWorkWithUnit($realWork+$leftWork)==Work::displayWorkWithUnit($plannedWork)) {
        continue; // It is just a rounding issue
      } else if (abs($realWork+$leftWork-$plannedWork)<0.01) {
        continue; // It is just a rounding issue
      }
      $errorDisplayed=false;
      if (round($realWork,2)!=round($sumWork,2)) {
        displayError(i18n("checkIncorrectWork",array(i18n($refType),$refId,Work::displayWorkWithUnit($realWork),Work::displayWorkWithUnit($sumWork))));
        $errorDisplayed=true;
      }
      if (round($realWork+$leftWork,2)!=round($plannedWork,2)) {
        displayError(i18n("checkIncorrectSumWork",array(i18n($refType),$refId,Work::displayWorkWithUnit($realWork),Work::displayWorkWithUnit($leftWork),Work::displayWorkWithUnit($plannedWork))));
        $errorDisplayed=true;
      }
      if (!$errorDisplayed) {
        $msg=(pq_substr(i18n("checkUnknownError"),0,1)=='[')?"Unknown error for $refType #$refId":i18n("checkUnknownError",array(i18n($refType),$refId));
        displayError($msg);
        traceLog($msg. " at Consistency::checkWorkOnActivity() | realWork=$realWork | leftWork=$leftWork | plannedWork=$plannedWork | sumWork=$sumWork");
      }
      $errors++;
      if ($correct) {
        $res=PlanningElement::updateSynthesis($refType,$refId);
        if (getLastOperationStatus($res)!='OK') {
          displayMsg(i18n("checkNotFixed"),true);
          $query="SELECT idResource as idres, sum(work) as sumwork from $workTable w where w.refType='$refType' and w.refId=$refId group by idResource";
          $resultRes=Sql::query($query);
          while ($lineRes = Sql::fetchLine($resultRes)) {
            $idRes=$lineRes['idres'];
            $sumWork=$lineRes['sumwork'];
            displayMsg('&nbsp;-&nbsp;'.SqlList::getNameFromId('Affectable', $idRes).' : '.Work::displayWorkWithUnit($sumWork),true);
          }
        } else {
          displayOK(i18n("checkFixed"),true);
        }
      }
    }
    if (!$errors) {
      displayOK(i18n("checkNoError"));
  
    }
  }
  
  // =================================================================================================================
  // Work On Activity
  // =================================================================================================================
  
  public static function checkWorkOnMeeting($correct=false,$trace=false) {
    debugTraceLog("checkWorkOnMeeting - Check work directly entered on Periodic Meeting");
    Sql::commitTransaction(); // Commit all what is already done as this part may rollback
    Sql::beginTransaction();
    $errors=0;
    // Direct Query : valid here for technical needs on grouping
    $work=new Work();
    $workTable=$work->getDatabaseTableName();
    $query="SELECT w.id as id, w.refType as reftype, w.refId as refid, w.idResource as idresource, w.work as work, w.workDate as workdate"
      ." FROM $workTable w "
      ." WHERE w.refType='PeriodicMeeting'";
    $result=Sql::query($query);
    while ($line = Sql::fetchLine($result)) {
      $refType=$line['reftype'];
      $refId=$line['refid'];
      $work=$line['work'];
      $date=$line['workdate'];
      $id=$line['id'];
      $idResource=$line['idresource'];
      $errorDisplayed=false;
      if (true) {
        $for=SqlList::getNameFromId('Resource', $idResource);
        $for.=' ('.Work::displayWorkWithUnit($work);
        $for.=', '.htmlFormatDate($date).')';
        displayError(i18n("checkIncorrectWorkOnMeeting",array(i18n($refType),$refId,$for)));
        $errorDisplayed=true;
      }
      $errors++;
      if ($correct) {
        $meet=SqlElement::getSingleSqlElementFromCriteria('Meeting', array('idPeriodicMeeting'=>$refId, 'meetingDate'=>$date));
        if (!$meet or ! $meet->id) {
          $meet=new Meeting();
          $meetList=$meet->getSqlElementsFromCriteria(array('idPeriodicMeeting'=>$refId, 'meetingDate'=>$date));
          $meet=reset($meetList);
        }
        if ($meet and $meet->id) {
          $ass=SqlElement::getSingleSqlElementFromCriteria('Assignment', array('refType'=>'Meeting', 'refId'=>$meet->id, 'idResource'=>$idResource));
          if ($ass and $ass->id) {
            $wOld=new Work($id);
            $wNew=SqlElement::getSingleSqlElementFromCriteria('Work', array('refType'=>'Meeting', 'refId'=>$meet->id, 'idResource'=>$idResource,'idAssignment'=>$ass->id,'workDate'=>$date));
            if ($wNew and $wNew->id) {
              $wNew->work+=$work;
            } else {
              $wNew=clone($wOld);
              $wNew->id=null;
              $wNew->refType='Meeting';
              $wNew->refId=$meet->id;
              $wNew->idAssignment=$ass->id;
            }
            $resOld=$wOld->delete();
            $resNew=$wNew->saveForced();
            $ass->saveWithRefresh();
            if (getLastOperationStatus($resNew)=='OK' and getLastOperationStatus($resOld)=='OK') {
              displayOK(i18n("checkFixed"),true);
              Sql::commitTransaction();
              Sql::beginTransaction();
            } else {
              traceLog("Delete old work on Periodic Meeting : $resOld");
              traceLog("Create new work on Meeting : $resNew");
              displayMsg(i18n("checkNotFixed").' (error on update)',true);
              Sql::rollbackTransaction();
              Sql::beginTransaction();
            }
          } else {
            displayMsg(i18n("checkNotFixed").' (assignment not found)',true);
          }
        } else {
          displayMsg(i18n("checkNotFixed").' (meeting not found)',true);
        }
      }
    }
    if (!$errors) {
      displayOK(i18n("checkNoError"));
  
    }
  }
  // =================================================================================================================
  // Work On Assignment
  // =================================================================================================================
  
  public static function checkWorkOnAssignment($correct=false,$trace=false) {
    debugTraceLog("checkWorkOnAssignment - Check work on Assignment compared to sum of Work");
    $errors=0;
    // Direct Query : valid here for technical needs on grouping
    $work=new Work();
    $workTable=$work->getDatabaseTableName();
    $ass=new Assignment();
    $assTable=$ass->getDatabaseTableName();
    $query="SELECT ass.id as id, ass.refType as reftype, ass.refId as refid, ass.realWork as realwork, ass.leftWork as leftwork, ass.plannedWork as plannedwork,"
        ."  (select sum(work) from $workTable w where w.idAssignment=ass.id) as sumwork, ass.idResource as idresource "
        ."FROM $assTable ass "
        ."WHERE realwork!=(select sum(work) from $workTable w where w.idAssignment=ass.id) "
        ."   OR (coalesce(ass.realWork,0)+coalesce(ass.leftWork,0))!=coalesce(ass.plannedWork,0) ";
    $result=Sql::query($query);
    while ($line = Sql::fetchLine($result)) {
      $id=$line['id'];
      $refType=$line['reftype'];
      $refId=$line['refid'];
      $realWork=$line['realwork'];
      $leftWork=$line['leftwork'];
      $plannedWork=$line['plannedwork'];
      $idResource=$line['idresource'];
      $sumWork=$line['sumwork'];
      if (Work::displayWorkWithUnit($realWork)==Work::displayWorkWithUnit($sumWork) and Work::displayWorkWithUnit($realWork+$leftWork)==Work::displayWorkWithUnit($plannedWork)) continue; // It is just a rounding issue
      if ($realWork!=$sumWork) displayError(i18n("checkIncorrectWork",array(i18n($refType),$refId,Work::displayWorkWithUnit($realWork),Work::displayWorkWithUnit($sumWork))));
      if ($realWork+$leftWork!=$plannedWork) displayError(i18n("checkIncorrectSumWork",array(i18n($refType),$refId.' ['.i18n('Resource').' #'.$idResource.']',Work::displayWorkWithUnit($realWork),Work::displayWorkWithUnit($leftWork),Work::displayWorkWithUnit($plannedWork))));
      $errors++;
      if ($correct) {
        $ass=new Assignment($id);
        $res=$ass->saveWithRefresh();
        if (getLastOperationStatus($res)!='OK') {
          displayMsg(i18n("checkNotFixed"),true);
        } else {
          displayOK(i18n("checkFixed"),true);
        }
      }
    }
    // Check Resource 
    debugTraceLog("checkWorkOnAssignment - Check Resource on Work compared to Resource on Assignment");
    $res=new Resource();
    $resTable=$res->getDatabaseTableName();
    $query="SELECT a.id as assid, a.idResource as assress, w.id as workid, w.idResource as workres, w.workDate as workdate, w.refType as reftype, w.refid as refid "
        ." FROM $workTable w, $assTable a, $resTable r "
        ." where w.idAssignment=a.id and a.idResource=r.id and r.isResourceTeam=0 and a.idResource!=w.idResource";
    $result=Sql::query($query);
    while ($line = Sql::fetchLine($result)) {
      $wRes=SqlList::getNameFromId('Affectable', $line['workres']);
      $aRes=SqlList::getNameFromId('Affectable', $line['assress']);
      $date=htmlFormatDate($line['workdate']);
      displayError(i18n('errorWorkResource',array($wRes,$aRes,$date,$line['workid'],$line['assid'],i18n($line['reftype']),$line['refid'])));
      $errors++;
      if ($correct) {
        $w=new Work($line['workid']);
        $w->idResource=$line['assress'];
        $res=$w->save();
        if (getLastOperationStatus($res)!='OK') {
          displayMsg(i18n("checkNotFixed"),true);
          debugTraceLog($res);
        } else {
          displayOK(i18n("checkFixed"),true);
        }
      }
    }
    debugTraceLog("checkWorkOnAssignment - Check Work with no project");
    // Check work with no project
    $query="SELECT w.id as id, w.idAssignment as assid, w.refType as reftype, w.refId as refid"
        ." FROM $workTable w "
        ." WHERE w.idProject is null or w.idProject=0 ";
    $result=Sql::query($query);
    while ($line = Sql::fetchLine($result)) {
      $id=$line['id'];
      $refType=$line['reftype'];
      $refId=$line['refid'];
      $assid=$line['assid'];
      displayError(i18n('checkWorkWithoutProject',array($id,i18n($refType),$refId)));
      $errors++;
      if ($correct) {
        $w=new Work($id);
        $proj=null;
        $ass=new Assignment($assid);
        if ($ass->idProject) {
          $proj=$ass->idProject;
        } else if ($refType and $refId) {
          $obj=new $refType($refId);
          if (property_exists($obj, 'idProject') and $obj->idProject) {
            $proj=$obj->idProject;
          }
        }
        if ($proj) {
          $w->idProject=$proj;
          $res=$w->save();
        } else if (! $refType and ! $refId) {
          $res=$w->delete();
        } else {
          $res="No Project found for Assignment #$assid and for $refType #$refId";
        }
        if (getLastOperationStatus($res)!='OK') {
          displayMsg(i18n("checkNotFixed"),true);
          debugTraceLog($res);
        } else {
          displayOK(i18n("checkFixed"),true);
        }
      }
    }  
    debugTraceLog("checkWorkOnAssignment - Check Work with no resource or not existing resource");
    $query="SELECT w.id as id, w.idResource as idres, w.idAssignment as idass, w.refType as reftype, w.refid as refid"
        ." FROM $workTable w"
        ." WHERE (w.idResource is null or w.idResource=0 or not exists (select'x' from $resTable res where res.id=w.idResource)" 
        ."    or refType is null or refId is null )";
    $result=Sql::query($query);
    while ($line = Sql::fetchLine($result)) {
      $id=$line['id'];
      $idres=$line['idres'];
      $idass=$line['idass'];
      $refType=$line['reftype'];
      $refId=$line['refid'];
      $w=new Work($id);
      displayError(i18n("checkInvalidReferenceOnWork",array($id,$idres,$refType,$refId)));
      $errors++;
      if ($correct) {
        $ass=new Assignment($idass);
        if ($ass->id and $ass->idResource and $ass->refType and $ass->refId) {
          $res=new Affectable($idres);
          if ($res->id) {
            $w->idResource=$ass->idResource;
            if (!$refType or !$refId) {
              $w->refType=$ass->refType;
              $w->refId=$ass->refId;
            }
            $res=$w->save();
            $ass->saveWithRefresh();
          } else {
            $res=$w->delete();
            $ass->delete();
          }
        } else {
          $res=$w->delete();
        }
        if (getLastOperationStatus($res)=='OK') {
          displayOK(i18n("checkFixed"),true);
        } else {
          displayMsg(i18n("checkNotFixed"),true);
        }
      }
    } 
    if (!$errors) {
      displayOK(i18n("checkNoError"));
  
    }
  }
  
  // =================================================================================================================
  // Idle consistency from Activity / PlanningElement / Assignment
  // =================================================================================================================
  
  public static function checkIdlePropagation($correct=false,$trace=false) {
    debugTraceLog("checkIdlePropagation - Check idle on PlanningElement compared to idle on Activity, Meeting or TestSession");
    $errors=0;
    // Direct Query : valid here for technical needs on grouping
    $actArray=array('Activity','Meeting','TestSession');
    foreach ($actArray as $type) {
      $ass=new Assignment();
      $assTable=$ass->getDatabaseTableName();
      $act=new $type();
      $actTable=$act->getDatabaseTableName();
      $pe=new PlanningElement();
      $peTable=$pe->getDatabaseTableName();
      $query="SELECT act.id as actid, pe.id as peid, ass.id as assid, act.idle as actidle, pe.idle as peidle, ass.idle as assidle "
          ." FROM $actTable as act left join $peTable as pe on (pe.refType='$type' and pe.refId=act.id) left join $assTable ass on (ass.refType='$type' and ass.refId=act.id)"
          ." WHERE act.idle!=pe.idle or (act.idle!=ass.idle and ass.idle is not null and act.idle=1)";
      $result=Sql::query($query);
      while ($line = Sql::fetchLine($result)) {
        $actId=$line['actid'];
        $peId=$line['peid'];
        $assId=$line['assid'];
        $actIdle=$line['actidle'];
        $peIdle=$line['peidle'];
        $assIdle=$line['assidle'];
        displayError(i18n("checkIncorrectIdle",array(i18n($type),$actId,$actIdle,$peIdle,$assIdle)));
        $errors++;
        if ($correct) {
          if ($assId) {
            $ass=new Assignment($assId);
            $ass->idle=$actIdle;
            if ($ass->idle==1) $ass->leftWork=0;
            $resAss=$ass->save();
          }
          if ($peId) {
            $pe=new PlanningElement($peId);
            $pe->idle=$actIdle;
            if ($pe->idle==1) $pe->leftWork=0;
            $resPe=$pe->save();
          }
          $act=new $type($actId);
          $resAct=$act->save();
          if (getLastOperationStatus($resAct)=='OK' or (isset($resPe) and getLastOperationStatus($resPe)=='OK') or (isset($resAss) and getLastOperationStatus($resAss)=='OK') ) {
            displayOK(i18n("checkFixed"),true);
          } else {
            displayMsg(i18n("checkNotFixed"),true);
          }
        }
      }
    }
    if (!$errors) {
      displayOK(i18n("checkNoError"));
  
    }
  }
  
  // =================================================================================================================
  // Missing (or Extra) Planning Elements
  // =================================================================================================================
  
  public static function checkMissingPlanningElement($correct=false,$trace=false) {
    debugTraceLog("checkMissingPlanningElement - Plannable items that have no or more than 1 PlanningElement");
    $errors=0;
    // Direct Query : valid here for technical needs on grouping
    $work=new Work();
    $workTable=$work->getDatabaseTableName();
    $pe=new PlanningElement();
    $peTable=$pe->getDatabaseTableName();
    $ass=new Assignment();
    $assTable=$ass->getDatabaseTableName();
    
    $query="SELECT ass.refType as reftype, ass.refId as refid, ass.idResource as idresource, ass.id as id from $assTable ass"
         . "  WHERE (select count(*) from $peTable pe where pe.refType=ass.refType and pe.refId=ass.refId )!=1";
    $result=Sql::query($query);
    $stockRefType='';
    $stockRefId='';
    while ($line = Sql::fetchLine($result)) {
      $refType=$line['reftype'];
      $refId=$line['refid'];
      $assId=$line['id'];
      if ($refType==$stockRefType and $refId==$stockRefId) continue;
      $stockRefType=$refType;
      $stockRefId=$refId;
      $listPe=$pe->getSqlElementsFromCriteria(array('refType'=>$refType,"refId"=>$refId));
      if (count($listPe)==0) {
        displayError(i18n("checkPlanningElementMissing",array(i18n($refType),$refId)));
        $errors++;
        if ($correct) {
          $peNameForRefObj=$refType."PlanningElement";
          $pmNameForRefObj="id".$refType."PlanningMode";
          $refObjFromPlan=new $refType($refId);
          if ($refObjFromPlan->id) { // Assignment refers to existing item
            if (property_exists($refObjFromPlan,$peNameForRefObj) and is_object($refObjFromPlan->$peNameForRefObj)
            and property_exists($refObjFromPlan->$peNameForRefObj, $pmNameForRefObj) and !$refObjFromPlan->$peNameForRefObj->$pmNameForRefObj) {
              $planningModeList=SqlList::getList('PlanningMode','applyTo');
              foreach ($planningModeList as $pmId=>$pmApplyTo) {
                if ($pmApplyTo==$refType) {
                  $refObjFromPlan->$peNameForRefObj->$pmNameForRefObj=$pmId;
                  break;
                }
              }
            }
            $resultSaveObjFromPlan=$refObjFromPlan->save();
            if (getLastOperationStatus($resultSaveObjFromPlan=="OK")) {
              displayOK(i18n("checkFixed"),true);
            } else {
              displayMsg(i18n("checkNotFixed"),true);
            }
          } else { // Assignment refers to no existing item : delete
            $ass=new Assignment($assId);
            if ($ass->id) {
            	$resultDeleteInvalidAssignement=$ass->delete();
              if (getLastOperationStatus($resultDeleteInvalidAssignement=="OK")) {
                displayOK(i18n("checkFixed"),true);
              } else {
                displayMsg(i18n("checkNotFixed"),true);
              }
            } else {
            	displayMsg(i18n("checkNotFixed"),true);
            }
          }
        }
      } else {
        displayError(i18n("checkPlanningElementExtra",array(i18n($refType),$refId,count($listPe))));
        $errors++;
        if ($correct) {
          displayMsg(i18n("checkNotFixed"),true);
        }
      }
    }
    
    $act=new Activity();$actTable=$act->getDatabaseTableName();
    $pro=new Project();$proTable=$pro->getDatabaseTableName();
    $mil=new Milestone();$milTable=$mil->getDatabaseTableName();
    $met=new Meeting();$metTable=$met->getDatabaseTableName();
    $pme=new PeriodicMeeting();$pmeTable=$pme->getDatabaseTableName();
    $tst=new TestSession();$tstTable=$tst->getDatabaseTableName();
    debugTraceLog("checkMissingPlanningElement - PlanningElement refering to not existing item (Activity, Project, Milestone, Periodic Meeeting or TestSession)");
    $query ="SELECT pe.refType as reftype, pe.refId as refid, pe.id as id from $peTable pe"
          . "  WHERE pe.refType='Activity' and not exists (select 'x' from $actTable x where x.id=pe.refId)";
    $query.=" UNION "; 
    $query.="SELECT pe.refType as reftype, pe.refId as refid, pe.id as id from $peTable pe"
          . "  WHERE pe.refType='Project' and not exists (select 'x' from $proTable x where x.id=pe.refId)";
    $query.=" UNION ";
    $query.="SELECT pe.refType as reftype, pe.refId as refid, pe.id as id from $peTable pe"
          . "  WHERE pe.refType='Milestone' and not exists (select 'x' from $milTable x where x.id=pe.refId)";
    $query.=" UNION ";
    $query.="SELECT pe.refType as reftype, pe.refId as refid, pe.id as id from $peTable pe"
          . "  WHERE pe.refType='Meeting' and not exists (select 'x' from $metTable x where x.id=pe.refId)";
    $query.=" UNION ";
    $query.="SELECT pe.refType as reftype, pe.refId as refid, pe.id as id from $peTable pe"
          . "  WHERE pe.refType='PeriodicMeeting' and not exists (select 'x' from $pmeTable x where x.id=pe.refId)";
    $query.=" UNION ";
    $query.="SELECT pe.refType as reftype, pe.refId as refid, pe.id as id from $peTable pe"
          . "  WHERE pe.refType='TestSession' and not exists (select 'x' from $tstTable x where x.id=pe.refId)";
    $result=Sql::query($query);
    while ($line = Sql::fetchLine($result)) {
      $refType=$line['reftype'];
      $refId=$line['refid'];
      $id=$line['id'];
      $pe=new PlanningElement($id);
      if ($pe->id) {
        displayError(i18n("checkPlanningElementEmpty",array(i18n($refType),$refId,$id)));
        $errors++;
        if ($correct) {
          $resultDeleteInvalidPlanningElement=$pe->delete();
          if (getLastOperationStatus($resultDeleteInvalidPlanningElement=="OK")) {
            displayOK(i18n("checkFixed"),true);
          } else {
            displayMsg(i18n("checkNotFixed"),true);
          }
        }
      } 
    }
    if (!$errors) {
      displayOK(i18n("checkNoError"));
    }
  }
  
  // =================================================================================================================
  // Budget
  // =================================================================================================================
    
  public static function checkBudget($correct=false,$trace=false) {
    debugTraceLog("checkBudget - Compare amounts on budget with sum of amounts of expenses");
    $errors=0;
    // Direct Query : valid here for technical needs on grouping
    $bud=new Budget();
    $budTable=$bud->getDatabaseTableName();
    $exp=new Expense();
    $expTable=$exp->getDatabaseTableName();
    $query="SELECT bud.id as idbudget, bud.usedAmount as usedamount, bud.usedFullAmount as usedfullamount,"
        ." bud.billedAmount as billedamount, bud.billedFullAmount as billedfullamount,"
        ." sum(exp.plannedAmount) as sumplannedamount, sum(exp.realAmount) as sumrealamount,"
        ." sum(exp.plannedFullAmount) as sumplannedfullamount, sum(exp.realFullAmount) as sumrealfullamount"
        ." FROM $budTable bud, $expTable exp"
        ." WHERE bud.elementary=1 and bud.id=exp.idBudgetItem and exp.cancelled=0"
        ." GROUP BY bud.id, bud.usedAmount, bud.usedFullAmount,bud.billedAmount, bud.billedFullAmount"
        ." HAVING bud.usedAmount<>sum(exp.plannedAmount) or bud.usedFullAmount<>sum(exp.plannedFullAmount)"
        ." or bud.billedAmount<>sum(exp.realAmount) or bud.billedFullAmount<>sum(exp.realFullAmount)";
    $result=Sql::query($query);
    while ($line = Sql::fetchLine($result)) {
      $idBudget=$line['idbudget'];
      $cause="";
      if ($line['usedamount']!=$line['sumplannedamount']) {
        $cause=i18n("colIdBudget")." ".$bud->getColCaption('usedAmount'). " (".$line['usedamount'].") <> "
              .i18n('sum').' '.i18n("colExpense")." ".$exp->getColCaption('plannedAmount'). " (".$line['sumplannedamount'].")";
        displayError(i18n("checkIncorrectBudgetAmounts",array($idBudget,$cause)));
      }
      if ($line['usedfullamount']!=$line['sumplannedfullamount']) {
        $cause=i18n("colIdBudget")." ".$bud->getColCaption('usedFullAmount'). " (".$line['usedfullamount'].") <> "
              .i18n('sum').' '.i18n("colExpense")." ".$exp->getColCaption('plannedFullAmount'). " (".$line['sumplannedfullamount'].")";
        displayError(i18n("checkIncorrectBudgetAmounts",array($idBudget,$cause)));
      }
      if ($line['billedamount']!=$line['sumrealamount']){
        $cause=i18n("colIdBudget")." ".$bud->getColCaption('billedAmount'). " (".$line['billedamount'].") <> "
              .i18n('sum').' '.i18n("colExpense")." ".$exp->getColCaption('realAmount'). " (".$line['sumrealamount'].")";
        displayError(i18n("checkIncorrectBudgetAmounts",array($idBudget,$cause)));
      }
      if ($line['billedfullamount']!=$line['sumrealfullamount']) {
        $cause=i18n("colIdBudget")." ".$bud->getColCaption('billedFullAmount'). " (".$line['billedfullamount'].") <> "
              .i18n('sum').' '.i18n("colExpense")." ".$exp->getColCaption('realFullAmount'). " (".$line['sumrealfullamount'].")";
        displayError(i18n("checkIncorrectBudgetAmounts",array($idBudget,$cause)));
      }
      
      $errors++;
      if ($correct) {
        $bud=new Budget($idBudget);
        $res=$bud->save();
        if (getLastOperationStatus($res)=='OK') {
          displayOK(i18n("checkFixed"),true);
        } else {
          displayMsg(i18n("checkNotFixed"),true);
        }
      }
    }
  
    if (!$errors) {
      displayOK(i18n("checkNoError"));
  
    }
  }
  // =================================================================================================================
  // Idle consistency from Activity / PlanningElement / Assignment
  // =================================================================================================================
  
  public static function checkInvalidFilters($correct=false,$trace=false) {
    debugTraceLog("checkInvalidFilters - Check filters with criteria on list with value 0");
    $errors=0;
    // Direct Query : valid here for technical needs on grouping
    $crit=new FilterCriteria();
    $critTable=$crit->getDatabaseTableName();
    $filter=new Filter();
    $filterTable=$filter->getDatabaseTableName();
    $query="SELECT crit.id as idcrit, crit.dispAttribute as attribute, filter.name as name, filter.refType as reftype, filter.idUser as user "
        ." FROM $critTable crit, $filterTable filter"
        ." WHERE crit.idFilter=filter.id and crit.sqlOperator='IN' and crit.sqlValue='0' and crit.isDynamic=0";
    $result=Sql::query($query);
    while ($line = Sql::fetchLine($result)) {
      $type=$line['reftype'];
      $userName=SqlList::getNameFromId('User', $line['user']);
      $attribute=$line['attribute'];
      $filterName=$line['name'];
      $id=$line['idcrit'];
      displayError(i18n("checkIncorrectFilterCriteria",array($filterName,i18n($type),$userName,$attribute)));
      $errors++;
      if ($correct) {
        $fc=new FilterCriteria($id);
        $res=$fc->delete();
        if (getLastOperationStatus($res)=='OK') {
          displayOK(i18n("checkFixed"),true);
        } else {
          displayMsg(i18n("checkNotFixed"),true);
        }
      }
    }

    if (!$errors) {
      displayOK(i18n("checkNoError"));
  
    }
    
  }
  
  // Check pools
  public static function checkPools($correct=false,$trace=false) {
    debugTraceLog("checkPools - Check pools including not existing resource");
    $errors=0;
    // Direct Query : valid here for technical needs on grouping
    $res=new Resource();
    $resTable=$res->getDatabaseTableName();
    $rta=new ResourceTeamAffectation();
    $rtaTable=$rta->getDatabaseTableName();
    $query="SELECT rta.id as id, rta.idResourceTeam as idpool, rta.idResource as idres"
        ." FROM $rtaTable rta"
        ." WHERE (rta.idResource is null or rta.idResource=0 or not exists (select'x' from $resTable res where res.id=rta.idResource) )";
    $result=Sql::query($query);
    while ($line = Sql::fetchLine($result)) {
      $idpool=$line['idpool'];
      $idres=$line['idres'];
      $id=$line['id'];
      $poolItem=new ResourceTeam($idpool); 
      $pool="#".$poolItem->id." - ".$poolItem->name;
      displayError(i18n("checkInvalidPoolAllocation",array($pool,$idres)));
      $errors++;
      if ($correct) {
        $rta=new ResourceTeamAffectation($id);
        $res=$rta->delete();
        if (getLastOperationStatus($res)=='OK') {
          displayOK(i18n("checkFixed"),true);
        } else {
          displayMsg(i18n("checkNotFixed"),true);
        }
      }
    }
  
    if (!$errors) {
      displayOK(i18n("checkNoError"));
    }
  }
  
  public static function checkProject($correct=false,$trace=false) {
    debugTraceLog("checkProject - Check Project on Work compared to Project on PlanningElement");
    $errors=0;
    // Direct Query : valid here for technical needs on grouping
    $wk=new Work();
    $wkTable=$wk->getDatabaseTableName();
    $pe=new PlanningElement();
    $peTable=$pe->getDatabaseTableName();
    $as=new Assignment(); 
    $asTable=$as->getDatabaseTableName();
    $query="SELECT w.id, w.idProject as idproject, w.refType as reftype, w.refId as refid from $wkTable w where w.idProject <> ( select idProject from $peTable pe where pe.refType=w.refType and pe.refId=w.refId)";
    $result=Sql::query($query);
    while ($line = Sql::fetchLine($result)) {
      $id=$line['id'];
      $idProject=$line['idproject'];
      $refType=$line['reftype'];
      $refId=$line['refid'];
      $obj=new $refType($refId);
      displayError(i18n("checkProjectInvalid",array($idProject,i18n('Work').' #'.$id, $obj->idProject,i18n($refType).' #'.$refId)));
      $errors++;
      if ($correct) {
        $w=new Work($id);
        $w->idProject=$obj->idProject;
        $res=$w->saveForced();
        if (getLastOperationStatus($res)=='OK') {
          displayOK(i18n("checkFixed"),true);
        } else {
          displayMsg(i18n("checkNotFixed"),true);
        }
      }
    }
    
    //
    debugTraceLog("checkProject - Check Project on Assignment compared to Project on PlanningElement");
    // Direct Query : valid here for technical needs on grouping
    $pe=new PlanningElement();
    $peTable=$pe->getDatabaseTableName();
    $as=new Assignment();
    $asTable=$as->getDatabaseTableName();
    $query="SELECT a.id, a.idProject as idproject, a.refType as reftype, a.refId as refid from $asTable a where a.idProject <> ( select idProject from $peTable pe where pe.refType=a.refType and pe.refId=a.refId)";
    $result=Sql::query($query);
    while ($line = Sql::fetchLine($result)) {
      $id=$line['id'];
      $idProject=$line['idproject'];
      $refType=$line['reftype'];
      $refId=$line['refid'];
      $obj=new $refType($refId);
      displayError(i18n("checkProjectInvalid",array($idProject,i18n('Assignment').' #'.$id, $obj->idProject,i18n($refType).' #'.$refId)));
      $errors++;
      if ($correct) {
        $as=new Assignment($id);
        $as->idProject=$obj->idProject;
        $res=$as->saveForced();
        if (getLastOperationStatus($res)=='OK') {
          displayOK(i18n("checkFixed"),true);
        } else {
          displayMsg(i18n("checkNotFixed"),true);
        }
      }
    }
    
    if (!$errors) {
      displayOK(i18n("checkNoError"));
    }
  }
  
  public static function checkPeriodicMeetingAssign($correct=false,$trace=false) {
    debugTraceLog("checkPeriodicMeetingAssign - Check Assignement on Periodic Meeting must be closed, with not left work ");
    $errors=0;
    // Direct Query : valid here for technical needs on grouping
    $as=new Assignment();
    $asTable=$as->getDatabaseTableName();
    $query="SELECT a.id, a.idProject as idproject, a.idResource as idresource, a.refType as reftype, a.refId as refid, a.idle as idle, a.leftWork as leftwork
              from $asTable a where a.refType='PeriodicMeeting' and (a.idle<>1 or a.leftWork is not null or a.leftWork<>0)";
    $result=Sql::query($query);
    while ($line = Sql::fetchLine($result)) {
      $asError=false;
      $id=$line['id'];
      $idProject=$line['idproject'];
      $refType=$line['reftype'];
      $refId=$line['refid'];
      $ilde=$line['idle'];
      $leftwork=($line['leftwork'])?intval($line['leftwork']):0;
      $resource=SqlList::getNameFromId('Resource', $line['idresource']);
      $obj=new $refType($refId);
      if($ilde!=1){
        $asError=true;
        $errors++;
        displayError(i18n("checkPeriodicMeetingAssignInvalidIdle",array($resource ,$refId,$idProject)));
      }
      if($leftwork>0){
        $asError=true;
        $errors++;
        displayError(i18n("checkPeriodicMeetingAssignInvalidLeftWork",array($resource ,$refId,$idProject)));
      }
      
      if ($correct and $asError) {
        $assignment=new Assignment($id);
        $assignment->idle=1;
        $assignment->leftWork=0;
        $assignment->plannedWork=$assignment->realWork+$assignment->leftWork;
        $res=$assignment->saveForced();
        if (getLastOperationStatus($res)=='OK') {
          displayOK(i18n("checkFixed"),true);
        } else {
          displayMsg(i18n("checkNotFixed"),true);
        }
      }
    }
    if (!$errors) {
      displayOK(i18n("checkNoError"));
    }
  }
  
  
}?>