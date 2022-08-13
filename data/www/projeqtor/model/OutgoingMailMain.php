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

/** ============================================================================
 * Action is establised during meeting, to define an action to be followed.
 */ 
require_once('_securityCheck.php'); 

class OutgoingMailMain extends SqlElement {

  // List of fields that will be exposed in general user interface
  public $_sec_description;
  public $id;    // redefine $id to specify its visible place 
  public $name;
  public $idOutgoingMailType;
  public $idProject;
  public $sendDate;
  public $idDeliveryMode;
  public $idResponsible;
  public $idStatus;
  public $idle;
  public $description;
  public $_sec_descriptionDestinataire;
  public $idProvider;
  public $idClient;
  public $idAffectable;
  public $address;
  public $descriptionRecipient;
  public $_sec_ApproverMail;
  public $idApprovalStatus;
  public $_Approver=Array();
  public $_spe_buttonSendMail;
  public $_sec_Link;
  public $_Link = array();
  public $_Attachment=array();
  public $_Note=array();
  public $_nbColMax=3;
  
  // Define the layout that will be used for lists
  private static $_layout='
    <th field="id" formatter="numericFormatter" width="5%" ># ${id}</th>
    <th field="nameProject" width="20%" >${idProject}</th>
    <th field="name" width="50" >${name}</th>
    <th field="nameOutgoingMailType" width="20%">${idOutgoingMailType}</th> 
    <th field="idle" width="5%" formatter="booleanFormatter">${idle}</th>
    ';

  private static $_fieldsAttributes=array( 
                                          "name"=>"required", 
                                          "idStatus"=>"required",
                                          "idProject"=>"required",
                                          "idOutgoingMailType"=>"required",
                                          "idApprovalStatus"=>"readonly",
                                          "address"=>"readonly",
  );  
  
  private static $_colCaptionTransposition = array('idResponsible'=> 'responsible','idDeliveryMode'=>'receiveMode','idDeliveryMode'=>'sendMode','idAffectable'=>'Receiver');
  
  private static $_databaseColumnName = array('idAffectable' =>'idContact');
    
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
  
  /** ==========================================================================
   * Return the specific fieldsAttributes
   * @return the fieldsAttributes
   */
  protected function getStaticFieldsAttributes() {
    return self::$_fieldsAttributes;
  }
  
  /** ============================================================================
   * Return the specific colCaptionTransposition
   * @return the colCaptionTransposition
   */
  protected function getStaticColCaptionTransposition($fld=null) {
    return self::$_colCaptionTransposition;
  }

  /** ========================================================================
   * Return the specific databaseTableName
   * @return the databaseTableName
   */
  protected function getStaticDatabaseColumnName() {
    return self::$_databaseColumnName;
  }
  
// ============================================================================**********
// GET VALIDATION SCRIPT
// ============================================================================**********
  
  /** ==========================================================================
   * Return the validation sript for some fields
   * @return the validation javascript (for dojo framework)
   */
  public function getValidationScript($colName) {
    $colScript = parent::getValidationScript($colName);
    if ($colName=="idProvider") {
      $colScript .= '<script type="dojo/connect" event="onChange" >';
      $colScript .= '  if (avoidRecursiveRefresh) return;';
      $colScript .= '  avoidRecursiveRefresh=true;';
      $colScript .= '  setTimeout(\'avoidRecursiveRefresh=false;\',500);';
      $colScript .= '  if (dijit.byId("idProvider").get("value")!="") { ';
      $colScript .= '   if (dijit.byId("idClient").get("value")!="") { ';
      $colScript .= '     dijit.byId("idClient").set("value", ""); ';
      $colScript .= '     dijit.byId("idClient").set("readOnly",true);';
      $colScript .= '   } ';
      $colScript .= '  }';
      $colScript .= '  if (dijit.byId("idProvider").get("value")==" ") { ';
      $colScript .= '     dijit.byId("idClient").set("readOnly",false);';
      $colScript .= '   } ';
      $colScript .= '  formChanged();';
      $colScript .= '</script>';
    }
    if($colName=="idClient"){
      $colScript .= '<script type="dojo/connect" event="onChange" >';
      $colScript .= '  if (avoidRecursiveRefresh) return;';
      $colScript .= '  avoidRecursiveRefresh=true;';
      $colScript .= '  setTimeout(\'avoidRecursiveRefresh=false;\',500);';
      $colScript .= '  if (dijit.byId("idClient").get("value")!="") { ';
      $colScript .= '   if (dijit.byId("idProvider").get("value")!="") { ';
      $colScript .= '     dijit.byId("idProvider").set("value",""); ';
      $colScript .= '     dijit.byId("idProvider").set("readOnly",true);';
      $colScript .= '   } ';
      $colScript .= '  }';
      $colScript .= '  if (dijit.byId("idClient").get("value")==" ") { ';
      $colScript .= '     dijit.byId("idProvider").set("readOnly",false);';
      $colScript .= '   } ';
      $colScript .= '  formChanged();';
      $colScript .= '</script>';     
    }
    return $colScript;
  }
  

  /**=========================================================================
   * Overrides SqlElement::save() function to add specific treatments
   * @see persistence/SqlElement#save()
   * @return the return message of persistence/SqlElement#save() method
   */
  public function save() {
    if(!$this->id){
      $this->idApprovalStatus = 1;
    }
    if($this->id){
      $address = "";
      if($this->idAffectable){
        $contact = new Contact($this->idAffectable,true);
        $address = $contact->street.' '.$contact->complement.' '.$contact->zip.' '.$contact->city.' '.$contact->state.' '.$contact->country;
      }
      if($this->idClient and pq_trim($address) == ""){
        $client = new Client($this->idClient,true);
        $address = $client->street.' '.$client->complement.' '.$client->zip.' '.$client->city.' '.$client->state.' '.$client->country;
      }
      if($this->idProvider and pq_trim($address) == ""){
        $provider = new Provider($this->idProvider,true);
        $address = $provider->street.' '.$provider->complement.' '.$provider->zip.' '.$provider->city.' '.$provider->state.' '.$provider->country;
      }
     if($address)$this->address = $address;
    }
    
    $result = parent::save();
    return $result;
    
  }
 
  public function setAttributes(){
    if ($this->idProvider) {
      self::$_fieldsAttributes['idClient']='readonly';
    }
    if ($this->idClient) {
      self::$_fieldsAttributes['idProvider']='readonly';
    }
  }
  
  public function getApprovalStatus() {
    $approver = new Approver();
    $lstApprover = $approver->getSqlElementsFromCriteria(array('refType'=>'OutgoingMail','refId'=>$this->id));
    if(!$lstApprover){
      $result = 1;
    }else{
      $reject = false;
      $approved = true;
      $notReject = false;
      foreach ($lstApprover as $app){
        if($app->approved != 1)$approved = false;
        if($app->disapproved == 1) $reject = true;
        if($app->approved==0 and $app->disapproved==0)$notReject = true;
      }
      if($approved)$result = 4;
      if($reject)$result=2;
      if($notReject and !$reject)$result=3;
    }
    return $result;
  }
  public function drawSpecificItem($item){
    global $print;
    $result="";
    if ($item=='buttonSendMail') {
    		if ($print or ! $this->id) {
    		  return "";
    		}
    		$result .= '<tr><td colspan="2">';
    		$result .= '<button id="sendInfoToApprovers" dojoType="dijit.form.Button" showlabel="true" ';
    		$result .= ' title="' . i18n('sendInfoToApprovers') . '" class="roundedVisibleButton">';
    		$result .= '<span>' . i18n('sendInfoToApprovers') . '</span>';
    		$result .=  '<script type="dojo/connect" event="onClick" args="evt">';
    		$result .= '   if (checkFormChangeInProgress()) {return false;}';
    		$result .=  '  var email="";';
    		$result .=  '  if (dojo.byId("email")) {email = dojo.byId("email").value;}';
    		$result .=  '  loadContent("../tool/sendMail.php","resultDivMain","objectForm",true);';
    		$result .= '</script>';
    		$result .= '</button>';
    		$result .= '</td></tr>';
    		return $result;
    }
  }
  public function sendMailToApprovers($onlyNotApproved=true) {
    $crit=array('refType'=>'OutgoingMail', 'refId'=>$this->id);
    if ($onlyNotApproved) {
      $crit['approved']='0';
    }
    $app=new Approver();
    $appList=$app->getSqlElementsFromCriteria($crit);
    $dest="";
    foreach ($appList as $app) {
      $res=new Affectable($app->idAffectable);
      $resMail=(($res->name)?$res->name:$res->userName);
      $resMail.=(($res->email)?' <'.$res->email.'>':'');
      $resMail=$res->email;
      $dest.=($dest)?', ':'';
      $dest.=$resMail;
    }
    // TODO : define specific message for decisions
    $title=$this->parseMailMessage(Parameter::getGlobalParameter('paramMailTitleApprover'));
    $msg=$this->parseMailMessage(Parameter::getGlobalParameter('paramMailBodyApprover'));
    $result=(sendMail($dest,$title,$msg))?'OK':'';
    if ($result) {
      return $dest;
    } else {
      return 0;
    }
  }
}
?>