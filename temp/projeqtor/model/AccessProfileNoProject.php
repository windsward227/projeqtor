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
 * Profile defines right to the application or to a project.
 */ 
require_once('_securityCheck.php');
class AccessProfileNoProject extends SqlElement {

  // extends SqlElement, so has $id
  public $_sec_Description;
  public $id;    // redefine $id to specify its visiblez place 
  public $name;
  public $idAccessScopeNoProjectRead;
  public $idAccessScopeNoProjectCreate;
  public $idAccessScopeNoProjectUpdate;
  public $idAccessScopeNoProjectDelete;
  public $sortOrder=0;
  public $isNonProject;
  public $isExtended;
  public $idle;
  public $description;

  //public $_sec_void;
  
  public $_isNameTranslatable = true;
  private static $_databaseCriteria = array('isNonProject'=>'1');
  private static $_databaseTableName = 'accessprofile';
  private static $_databaseColumnName = array(
      'idAccessScopeNoProjectRead'   => 'idAccessScopeRead',
      'idAccessScopeNoProjectCreate' => 'idAccessScopeCreate',
      'idAccessScopeNoProjectUpdate' => 'idAccessScopeUpdate',
      'idAccessScopeNoProjectDelete' => 'idAccessScopeDelete'
  );
  private static $_colCaptionTransposition = array(
      'idAccessScopeNoProjectRead'   => 'idAccessScopeRead',
      'idAccessScopeNoProjectCreate' => 'idAccessScopeCreate',
      'idAccessScopeNoProjectUpdate' => 'idAccessScopeUpdate',
      'idAccessScopeNoProjectDelete' => 'idAccessScopeDelete'
  );
  
  private static $_fieldsAttributes=array("name"=>"required", 
                                  "idAccessScopeNoProjectRead"=>"required",
                                  "idAccessScopeNoProjectCreate"=>"required",
                                  "idAccessScopeNoProjectUpdate"=>"required",
                                  "idAccessScopeNoProjectDelete"=>"required",
                                  "isNonProject"=>"hidden",
                                  "isExtended"=>"hidden"
  );  

  private static $_layout='
    <th field="id" formatter="numericFormatter" width="5%" ># ${id}</th>
    <th field="name" width="45%" formatter="translateFormatter">${name}</th>
    <th field="nameAccessScopeNoProjectCreate" width="15%" formatter="translateFormatter">${idAccessScopeCreate}</th>
    <th field="nameAccessScopeNoProjectUpdate" width="15%" formatter="translateFormatter">${idAccessScopeUpdate}</th>
    <th field="nameAccessScopeNoProjectDelete" width="15%" formatter="translateFormatter">${idAccessScopeDelete}</th>
    <th field="sortOrder" width="5%">${sortOrderShort}</th>         
    <th field="idle" width="5%" formatter="booleanFormatter" >${idle}</th>
    ';
  
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

    /** ==========================================================================
   * Return the specific fieldsAttributes
   * @return the fieldsAttributes
   */
  protected function getStaticFieldsAttributes() {
    return self::$_fieldsAttributes;
  }
 
    /** ==========================================================================
   * Return the specific layout
   * @return the layout
   */
  protected function getStaticLayout() {
    return self::$_layout;
  }
// ============================================================================**********
// GET STATIC DATA FUNCTIONS
// ============================================================================**********
  

  /** ========================================================================
   * Return the specific database criteria
   * @return the databaseTableName
   */
  protected function getStaticDatabaseCriteria() {
    return self::$_databaseCriteria;
  }
  /** ========================================================================
   * Return the specific databaseTableName
   * @return the databaseTableName
   */
  protected function getStaticDatabaseTableName() {
    $paramDbPrefix=Parameter::getGlobalParameter('paramDbPrefix');
    return $paramDbPrefix . self::$_databaseTableName;
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
  /**
   * ============================================================================
   * Return the specific colCaptionTransposition
   *
   * @return the colCaptionTransposition
   */
  protected function getStaticColCaptionTransposition($fld = null) {
    return self::$_colCaptionTransposition;
  }
 
  // ============================================================================**********
  // MAIN FUNCTIONS
  // ============================================================================**********
  /**=========================================================================
   * Overrides SqlElement::save() function to add specific treatments
   * @see persistence/SqlElement#save()
   * @return the return message of persistence/SqlElement#save() method
   */
  public function save() {
    $this->isExtended=0;
    if (!$this->idAccessScopeNoProjectRead) $this->idAccessScopeNoProjectRead=4;
    $accessScopeUpdate=SqlList::getFieldFromId('AccessScope', $this->idAccessScopeNoProjectUpdate, 'accessCode',false);
    $accessScopeDelete=SqlList::getFieldFromId('AccessScope', $this->idAccessScopeNoProjectDelete, 'accessCode',false);
    if ($accessScopeUpdate=='RES' or $accessScopeUpdate=='OWN' or $accessScopeDelete=='RES' or $accessScopeDelete=='OWN') {
      $this->isExtended=1;
    }
    $result = parent::save();
    return $result;
  }
}
?>