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
 * ActionType defines the type of an issue.
 */ 
require_once('_securityCheck.php');
class AssetType extends ShortType {

  // Define the layout that will be used for lists
  public $_sec_Description;
  public $id;    // redefine $id to specify its visible place
  public $name;
  public $code;
  public $icon;
  public $idWorkflow;
  public $sortOrder=0;
  public $idle;
  public $description;
  public $_sec_Behavior;
  public $mandatoryDescription;
  public $_lib_mandatoryField;
  public $lockIdle;
  public $_lib_statusMustChangeIdle;
  public $lockCancelled;
  public $_lib_statusMustChangeCancelled;
  
  private static $_layout='
    <th field="id" formatter="numericFormatter" width="5%"># ${id}</th>
    <th field="icon" width="5%" formatter="iconFormatter">${icon}</th>
    <th field="name" width="60%">${name}</th>
    <th field="sortOrder" width="5%">${sortOrderShort}</th>
    <th field="nameWorkflow" width="20%" >${idWorkflow}</th>
    <th field="idle" width="5%" formatter="booleanFormatter">${idle}</th>
    ';
  
  private static $_databaseCriteria = array('scope'=>'Asset');
  
  
  private static $_fieldsAttributes=array(
      'lockCancelled'=>'hidden',
      'name'=>'required',
      'idWorkflow'=>'required',
      'mandatoryDescription'=>'nobr',
      'lockIdle'=>'nobr',
      'lockCancelled'=>'hidden',
      '_lib_statusMustChangeCancelled'=>'hidden'
  );
  
   /** ==========================================================================
   * Constructor
   * @param $id the id of the object in the database (null if not stored yet)
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

// ============================================================================**********
// GET STATIC DATA FUNCTIONS
// ============================================================================**********
  /** ==========================================================================
   * Return the specific layout
   * @return the layout
   */
  protected function getStaticLayout() {
    return self::$_layout;
  }
  /** ========================================================================
   * Return the specific database criteria
   * @return the databaseTableName
   */
  protected function getStaticDatabaseCriteria() {
    return self::$_databaseCriteria;
  }
  
  /** ==========================================================================
   * Return the specific fieldsAttributes
   * @return the fieldsAttributes
   */
  protected function getStaticFieldsAttributes() {
    return array_merge(parent::getStaticFieldsAttributes(),self::$_fieldsAttributes);
    //return self::$_fieldsAttributes;
  }
}
?>