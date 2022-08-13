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
class WorkTokenClientContractWork extends SqlElement {

  public $id;
  public $idWork;
  public $time;
  public $idWorkTokenClientContract;
  public $workTokenQuantity;
  public $idWorkTokenMarkup;
  public $workTokenMarkupQuantity;
  public $billable;
  public $_isNameTranslatable = true;
  
  private static $_databaseTableName = 'worktokenclientcontractwork';
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
  
  public function save() {
    $result=parent::save();
    return $result;
  }
  
  public function control(){
    $result="";
    $defaultControl=parent::control();
    if ($defaultControl != 'OK') {
      $result .= $defaultControl;
    }
    $old = $this->getOld();
    if($this->id){
      if( $this->idWorkTokenClientContract!='' and $this->workTokenQuantity!=$old->workTokenQuantity){
        self::calculIfOrderFullyConsumed('save',$old);
      }elseif ($this->idWorkTokenClientContract!=$old->idWorkTokenClientContract){
        self::calculIfOrderFullyConsumed('save',$old);
      }
    }else{
      self::calculIfOrderFullyConsumed('save');
    }
       
    if ($result=="") {
      $result='OK';
    }
    return $result;
  }
  
  public function delete(){
    $result = parent::delete();
    if (! pq_strpos($result,'id="lastOperationStatus" value="OK"')) {
      return $result;
    }
    self::calculIfOrderFullyConsumed('delete');
    return $result;
  }
  
  public function calculIfOrderFullyConsumed($mode,$old=false){

    if ($this->billable==0) {
    	return;
    }
    if($mode!='delete' and $old and $this->idWorkTokenClientContract!=$old->idWorkTokenClientContract){
      $oldWorkTokenCC= new WorkTokenClientContract($old->idWorkTokenClientContract);
      if($oldWorkTokenCC->fullyConsumed==1){
        $where="id<>$this->id and idWorkTokenClientContract=$old->idWorkTokenClientContract";
        $oldSum=$this->sumSqlElementsFromCriteria('workTokenMarkupQuantity', null,$where);
        $oldTotal=$oldSum-$this->workTokenMarkupQuantity;
        if($oldWorkTokenCC->quantity > $oldTotal){
          $oldWorkTokenCC->fullyConsumed=0;
          $oldWorkTokenCC->save();
        }
      }
    }
    $workTokenCC= new WorkTokenClientContract($this->idWorkTokenClientContract);
    $where="";
    if($this->id)$where.="id<>".Sql::fmtId($this->id)." and";
    $where.=" idWorkTokenClientContract=$this->idWorkTokenClientContract";
    $sumQuantityW=$this->sumSqlElementsFromCriteria('workTokenMarkupQuantity', null,$where);
    $total=$sumQuantityW+$this->workTokenMarkupQuantity;
    if($workTokenCC->quantity <=$total and $workTokenCC->fullyConsumed==0){
      $workTokenCC->fullyConsumed=1;
      $workTokenCC->save();
    }else if($workTokenCC->quantity > $total and $workTokenCC->fullyConsumed==1){
      $workTokenCC->fullyConsumed=0;
      $workTokenCC->save();
    }
  }
}
?>