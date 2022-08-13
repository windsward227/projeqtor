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
 * Client is the owner of a project.
 */  
require_once('_securityCheck.php'); 
class WorkTokenClientContract extends SqlElement {

  public $id;
  public $idWorkToken;
  public $idClientContract;
  public $description;
  public $quantity;
  public $duration;
  public $amount;
  public $fullyConsumed;
  public $_isNameTranslatable = true;
  
  private static $_databaseTableName = 'worktokenclientcontract';
  private static $_databaseColumnName = array(
      'description'   => 'name',
  );
  private static $_databaseCriteria = array();
  /** ==========================================================================
   * Constructor
   * @param $id the id of the object in the database (null if not stored yet)
   * @return void
  */
  /** ========================================================================
   * Return the specific databaseTableName
   * @return the databaseTableName
  */
  protected function getStaticDatabaseTableName() {
    $paramDbPrefix=Parameter::getGlobalParameter('paramDbPrefix');
    return $paramDbPrefix . self::$_databaseTableName;
  }
  
  /** ========================================================================
   * Return the specific database criteria
   * @return the databaseTableName
   */
  protected function getStaticDatabaseCriteria() {
    return self::$_databaseCriteria;
  }
  
  /** ==========================================================================
   * Construct
   * @return void
   */
  function __construct($id = NULL, $withoutDependentObjects=false) {
    parent::__construct($id,$withoutDependentObjects);
  }
  
  
  /** ==========================================================================
   * Destructor
   * @return void
   */
  function __destruct() {
    parent::__destruct();
  }
  
  /**
   * ========================================================================
   * Return the specific databaseColumnName
   *
   * @return the databaseTableName
   */
  protected function getStaticDatabaseColumnName() {
    return self::$_databaseColumnName;
  }
  
  public static function drawWorkTokenClientContract($obj,$print){
    $workClientContract= new  WorkTokenClientContract();
    $critArray= array("idClientContract"=>$obj->id);
    $lstWorkClientContract= $workClientContract->getSqlElementsFromCriteria($critArray);

    $canDelete=securityGetAccessRightYesNo('menu'.get_class($obj), 'delete', $obj)=="YES";
    $canUpdate=securityGetAccessRightYesNo('menu'.get_class($obj), 'update', $obj)=="YES";
    $canCreate=securityGetAccessRightYesNo('menu'.get_class($obj), 'create', $obj)=="YES";
    if (!(securityGetAccessRightYesNo('menu'.get_class($obj), 'update', $obj)=="YES")) {
      $canCreate=false;
      $canUpdate=false;
      $canDelete=false;
    }
    if ($obj->idle==1) {
      $canUpdate=false;
      $canCreate=false;
      $canDelete=false;
    }
    $layout=Parameter::getUserParameter('paramLayoutObjectDetail');
    $destinationWidth=(RequestHandler::getNumeric('destinationWidth')*0.95)-10;
    $widthEl=($layout=='col')?'':'width:'.$destinationWidth.'px;';
    echo '<tr><td colspan=2 style="width:100%;"><div  dojotype="dijit.layout.ContentPane" ><div style="'.$widthEl.' overflow-x:auto;  overflow-y:hidden; text-align: -webkit-center;"><table style="width:100%;">';
    echo '<tr>';
    if (!$print) {
      echo '<td class="assignHeader" style="width:5%" rowspan="3" >';
      if ($obj->id!=null and !$print  and !$obj->idle) {
        echo '<a onClick="addTokenClientContract('.$obj->id.','.$obj->idProject.');" title="'.i18n('addTokenClientContract').'" /> '.formatSmallButton('Add').'</a>';
      }
      echo '</td>';
    }
    
    echo'</tr>';
    echo  '<tr  style="width:100%;height:100%;text-align:right;">';
    echo  '<td class="assignHeader" style="width:2%;" rowspan="2" >'.i18n("colId").'</td>';
    echo  '<td class="assignHeader" style="width:17%;" colspan="2" rowspan="2">'.lcfirst(i18n("token")).'</td>';
    echo  '<td class="assignHeader" style="width:15%;" rowspan="2" >'.lcfirst(i18n("colDescription")).'</td>';
    echo  '<td class="assignHeader" style="width:22%;" colspan="3">'.i18n("colTokenOrdered").'</td>';
    echo  '<td class="assignHeader" style="width:27%;" colspan="4">'.i18n("colTokenUsed").'</td>';
    echo  '<td class="assignHeader" style="width:16%;" colspan="2">'.i18n("colTokenLeft").'</td>';
    echo '</tr>';
    echo'<tr style="width:100%;height:100%;text-align:right;">';
    echo  '<td class="assignHeader" style="width:5%;" >'.lcfirst(i18n("quantity")).'</td>';//
    echo  '<td class="assignHeader" style="width:5%;"  >'.i18n("duration").'</td>';
    echo  '<td class="assignHeader" style="width:12%;" >'.i18n("colAmount").'</td>';
    
    echo  '<td class="assignHeader" style="width:5%;">'.i18n("colCountTotal").'</td>';
    echo  '<td class="assignHeader" style="width:5%;">'.i18n("colTotalMarkup").'</td>';
    echo  '<td class="assignHeader" style="width:5%">'.i18n("duration").'</td>';
    echo  '<td class="assignHeader" style="width:12%;">'.i18n("colCost").'</td>';
    
    echo  '<td class="assignHeader" style="width:5%;">'.i18n("duration").'</td>';
    echo  '<td class="assignHeader" style="width:12%;">'.i18n("colAmount").'</td>';
    echo'</tr>';
      $totalAllQuantity=0;
      $totalAllDuration=0;
      $totalAllAmount=0;
      $totalAllTokenUsed=0;
      $totalAllTokenMarkupUsed=0;
      $totalAllTokenDurationUsed=0;
      $totalAllCostUsed=0;
      $totalAllLeftTokenDuration=0;
      $totalAllLeftCost=0;
    
    foreach ($lstWorkClientContract as $val){
      $workTokenUsed=0;
      $workTokenMarkupUsed=0;
      $durationVal=Work::displayImputation($val->duration);
      $workTokenCCW=new WorkTokenClientContractWork();
      $workToken=new TokenDefinition($val->idWorkToken);
      $amount=$workToken->amount;
      $lstWorkTCCW=$workTokenCCW->getSqlElementsFromCriteria(array('idWorkTokenClientContract'=>$val->id,'billable'=>1));
      foreach ($lstWorkTCCW as $wTCCW){
        $workTokenUsed+=$wTCCW->workTokenQuantity;
        if($wTCCW->workTokenMarkupQuantity>0){
          $newWTM=new WorkTokenMarkup($wTCCW->idWorkTokenMarkup);
//           $valCal=$wTCCW->workTokenQuantity-$wTCCW->workTokenQuantity+($wTCCW->workTokenMarkupQuantity*$newWTM->coefficient);
           $workTokenMarkupUsed+=$wTCCW->workTokenMarkupQuantity;
        }
      }
      $totalCostUsed=$amount*$workTokenMarkupUsed;
      $tokenDurationUsed=Work::displayImputation($workTokenMarkupUsed*$workToken->duration);
      $leftTokenDuration=$durationVal-$tokenDurationUsed;
      $totalLeftCost=$val->amount-$totalCostUsed;
      $used =($workTokenUsed > 0)?1:0;
      
      $totalAllQuantity+=$val->quantity;
      $totalAllDuration+=$durationVal;
      $totalAllAmount+=$val->amount;
      $totalAllTokenUsed+=$workTokenUsed;
      $totalAllTokenMarkupUsed+=$workTokenMarkupUsed;
      $totalAllTokenDurationUsed+=$tokenDurationUsed;
      $totalAllCostUsed+=$totalCostUsed;
      $totalAllLeftTokenDuration+=$leftTokenDuration;
      $totalAllLeftCost+=$totalLeftCost;
      
      $rightRead=securityGetAccessRightYesNo('menu'.get_class($workToken),'read',$workToken);
      $gotoStyle="";
      $goto='assignData';
      $class='assignData';
      if ( securityCheckDisplayMenu(null, get_class($workToken)) and $rightRead=="YES") {
        $goto=' onClick="gotoElement(\''.get_class($workToken).'\',\''.htmlEncode($workToken->id).'\');" ';
        $gotoStyle='cursor: pointer;';
        $class.=' classLinkName';
      }
      
      echo '<tr  style="width:100%;height:100%;text-align:right;">';
      echo  '<td class="assignData" style="width:10%;white-space:nowrap;text-align:center;">';
      if ($canUpdate) {
        echo  '<a onClick="editTokenClientContract(\''.$val->id.'\',\''.$obj->idProject.'\','.$used.');" '.'title="'.i18n('editWorkTokenClientContract').'" > '.formatSmallButton('Edit').'</a>';
      }
      if ($canDelete and $used==0) {
        echo  '<a onClick="removeTokenClientContract(\''.$val->id.'\');" '.'title="'.i18n('removeWorkTokenClientContract').'" > '.formatSmallButton('Remove').'</a>';
      }
      echo '</td>';
      echo  '<td class="assignData" style="text-align:left;">#'.$val->id.'</td>';
      echo  '<td style="border-bottom:1px solid #AAAAAA;"><div '.$goto.' class="linkIconHover " style="margin-left:10px;"  >'.formatIcon(get_class($workToken), 16).'</div></td>';
      echo  '<td class="'.$class.'" style="text-align:left;vertical-align: middle;border-left:unset;'.$gotoStyle.'" '.$goto.'><div>'.$workToken->name.'&nbsp;#'.$workToken->id.'</div></td>';
      echo  '<td class="assignData" style="vertical-align: middle;text-align:left;">'.nl2br($val->description).'</td>';
      echo  '<td class="assignData" style="vertical-align: middle;text-align:center;">'.htmlDisplayNumericWithoutTrailingZeros($val->quantity).'</td>';
      echo  '<td class="assignData" style="vertical-align: middle;text-align:center;">'.$durationVal.'&nbsp;'.Work::displayShortImputationUnit().'</td>';
      echo  '<td class="assignData" style="vertical-align: middle;text-align:right;padding-right:2px;">'.htmlDisplayCurrency($val->amount).'</td>';
      echo  '<td class="assignData" style="vertical-align: middle;text-align:center;">'.htmlDisplayNumericWithoutTrailingZeros($workTokenUsed).'</td>';
      echo  '<td class="assignData" style="vertical-align: middle;text-align:center;">'.htmlDisplayNumericWithoutTrailingZeros($workTokenMarkupUsed).'</td>';
      echo  '<td class="assignData" style="vertical-align: middle;text-align:center;">'.$tokenDurationUsed.'&nbsp;'.Work::displayShortImputationUnit().'</td>';
      echo  '<td class="assignData" style="vertical-align: middle;text-align:right;padding-right:2px;">'.htmlDisplayCurrency($totalCostUsed).'</td>';
      echo  '<td class="assignData" style="vertical-align: middle;text-align:center;">'.$leftTokenDuration.'&nbsp;'.Work::displayShortImputationUnit().'</td>';
      echo  '<td class="assignData" style="vertical-align: middle;text-align:right;padding-right:2px;">'.htmlDisplayCurrency($totalLeftCost).'</td>';
      echo'</tr>';
    }
    echo '<tr>';
      echo  '<td class="assignHeader" style="vertical-align: middle;text-align:center;" colspan="5">'.pq_ucfirst(i18n('colCountTotal')).'</td>';
      echo  '<td class="assignHeader" style="vertical-align: middle;text-align:center;">'.htmlDisplayNumericWithoutTrailingZeros($totalAllQuantity).'</td>';
      echo  '<td class="assignHeader" style="vertical-align: middle;text-align:center;">'.$totalAllDuration.'&nbsp;'.Work::displayShortImputationUnit().'</td>';
      echo  '<td class="assignHeader" style="vertical-align: middle;text-align:right;padding-right:2px;font-size: 9pt;">'.htmlDisplayCurrency($totalAllAmount).'</td>';
      echo  '<td class="assignHeader" style="vertical-align: middle;text-align:center;">'.htmlDisplayNumericWithoutTrailingZeros($totalAllTokenUsed).'</td>';
      echo  '<td class="assignHeader" style="vertical-align: middle;text-align:center;">'.htmlDisplayNumericWithoutTrailingZeros($totalAllTokenMarkupUsed).'</td>';
      echo  '<td class="assignHeader" style="vertical-align: middle;text-align:center;">'.$totalAllTokenDurationUsed.'&nbsp;'.Work::displayShortImputationUnit().'</td>';
      echo  '<td class="assignHeader" style="vertical-align: middle;text-align:right;padding-right:2px;">'.htmlDisplayCurrency($totalAllCostUsed).'</td>';
      echo  '<td class="assignHeader" style="vertical-align: middle;text-align:center;">'.$totalAllLeftTokenDuration.'&nbsp;'.Work::displayShortImputationUnit().'</td>';
      echo  '<td class="assignHeader" style="vertical-align: middle;text-align:right;padding-right:2px;font-size: 9pt;">'.htmlDisplayCurrency($totalAllLeftCost).'</td>';
    echo '</tr>';
    echo '</table>';
    echo '</div></div></td></tr>';
  }
}
?>