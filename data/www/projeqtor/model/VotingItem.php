<?php


class VotingItem extends SqlElement
{
    public $id;    // redefine $id to specify its visible place
    public $refType;
    public $refId;
    public $targetValue;
    public $actualValue;
    public $pctRate;
    public $locked;
    public $_button_vote;
    
    
    private static $_fieldsAttributes=array(
        "id"=>"hidden",
        "name"=>"hidden",
        "targetValue"=>"readonly",
        "actualValue"=>"readonly",
        "pctRate"=>"readonly",
        "refType"=>"hidden",
        "refId"=>"hidden",
        "locked"=>"hidden",
    );
    
    
    /** ==========================================================================
     * Constructor
     * @param $id the id of the object in the database (null if not stored yet)
     * @return void
     */ 
    function __construct($id = NULL, $withoutDependentObjects=false) {
      parent::__construct($id,$withoutDependentObjects);
    }
    
    public function __destruct() {
      parent::__destruct();
    }
    
    public function save() {
      //calculate TargetValue
      $old = $this->getOld(true);
      $idUseRule=self::getIdUseRule($this->refType,$this->refId);
      $votingUseRule = new VotingUseRule($idUseRule);
      $itemOrigin = new $this->refType($this->refId);
      $locked = self::isBlockingStatus($votingUseRule->blocking__idStatus, $itemOrigin->idStatus);
      $this->locked = $locked;
      if($votingUseRule->fixValue and $votingUseRule->fixValue != 0){
        $this->targetValue = $votingUseRule->fixValue;
      }else{
        if($this->refType=='Ticket'){
          $item = SqlElement::getSingleSqlElementFromCriteria('WorkElement', array('refType'=>'Ticket','refId'=>$this->refId));
          $this->targetValue =  ceil($item->plannedWork * $votingUseRule->workPointConvertion);
        }elseif($this->refType=='Activity'){
          $activity = new Activity($this->refId);
          $this->targetValue = ceil($activity->ActivityPlanningElement->validatedWork * $votingUseRule->workPointConvertion);
        }elseif($this->refType=='Requirement'){
          $requ = new Requirement($this->refId);
          $this->targetValue = ceil($requ->plannedWork * $votingUseRule->workPointConvertion);
        }
      }
      if($this->id){
        $this->actualValue = 0;
        $this->pctRate = 0;
        $voting = new Voting();
        $lstVoting = $voting->getSqlElementsFromCriteria(array('refType'=>$this->refType,'refId'=>$this->refId));
        foreach ($lstVoting as $voteV){
          $this->actualValue += $voteV->value;
        }
        if(!$this->targetValue){
          if($this->actualValue and $this->actualValue >0){
            $this->pctRate = 100;
          }
        }else{
          if($this->actualValue and $this->actualValue >0){
            if($this->targetValue){
              $this->pctRate = $this->actualValue * 100 / $this->targetValue;
            }
          }
        }
      }
      $result = parent::save();
      return $result;
    }
    
    
    
    public function drawSpecificItem($item) {
      if ($item=='vote' and $this->id) {
        $obj = new $this->refType($this->refId);
        if(!$this->locked){
          $voteAttr = new VotingAttribution();
          $typeName=SqlElement::getTypeName($this->refType);
          $idType = $obj->$typeName;
          $canVote = $voteAttr->canVote($this->refType, $this->refId, $idType);
          $voteExist = false;
          $vote = SqlElement::getSingleSqlElementFromCriteria('Voting', array('refType'=>$this->refType,'refId'=>$this->refId,'idUser'=>getCurrentUserId()));
          if($vote->id)$voteExist=true;
          if(!$voteExist){
            $idUser = getCurrentUserId();
            $affectable = new Affectable($idUser);
            if($affectable->isContact){
              $contact = new Contact($idUser);
              if($contact->idClient){
                $vote = SqlElement::getSingleSqlElementFromCriteria('Voting', array('refType'=>$this->refType,'refId'=>$this->refId,'idClient'=>$contact->idClient));
                if($vote->id)$voteExist=true;
              }
            }
          }
          $idRule=self::getIdUseRule($this->refType, $this->refId);
          if($canVote and !$voteExist){
        		echo '<div id="'.$item.'Add" title="' . i18n('addVote') . '" style="padding:2px 2px 0px 0px;" >';
        		echo '<button id="' . $item . 'AddButton" dojoType="dijit.form.Button" style="width:200px;vertical-align: middle;" class="roundedVisibleButton">';
        		echo '<span>' . i18n('addVote') . '</span>';
        		echo '<script type="dojo/connect" event="onClick" args="evt">';
        		echo 'addVote(\''.$this->refType.'\','.$this->refId.',\''.getEditorType().'\',\'add\','.$idRule.');';
        		echo '</script>';
        		echo '</button>';
        		echo '</div>';
          }
          if($voteExist){
            echo '<div id="'.$item.'Edit" title="' . i18n('editVote') . '" style="padding:2px 2px 0px 0px;" >';
            echo '<button id="' . $item . 'EditButton" dojoType="dijit.form.Button" style="width:200px;vertical-align: middle;" class="roundedVisibleButton">';
            echo '<span>' . i18n('editVote') . '</span>';
            echo '<script type="dojo/connect" event="onClick" args="evt">';
            echo 'addVote(\''.$this->refType.'\','.$this->refId.',\''.getEditorType().'\',\'edit\','.$idRule.');';
            echo '</script>';
            echo '</button>';
            echo '</div>';
          }
        }
        $voting = new Voting();
        $voteItemExist = $voting->countSqlElementsFromCriteria(array('refType'=>$this->refType,'refId'=>$this->refId));
        $canSeeVotes=SqlElement::getSingleSqlElementFromCriteria('HabilitationOther', array('idProfile'=>getSessionUser ()->getProfile ( $obj->idProject ), 'scope'=>'canManageVotes'));
        if($canSeeVotes->rightAccess==1 and $voteItemExist){
          echo '<table style="margin-top:20px;width:100%;">';
          echo '  <tr>';
          echo '    <td class="linkHeader" style="width:20%">'.i18n('colIdVoter').'</td>';
          echo '    <td class="linkHeader" style="width:20%">'.i18n('colValue').'</td>';
          echo '    <td class="linkHeader" style="width:20%">'.i18n('colIdUser').'</td>';
          echo '    <td class="linkHeader" style="width:20%">'.i18n('colIdClient').'</td>';
          echo '  </tr>';
          $lstVote =  $voting->getSqlElementsFromCriteria(array('refType'=>$this->refType,'refId'=>$this->refId));
          foreach ($lstVote as $vote){
            echo '<tr>';
            $idVoter=SqlList::getNameFromId('Affectable', $vote->idVoter);
            $idUser = SqlList::getNameFromId('Affectable', $vote->idUser);
            $idClient = SqlList::getNameFromId('Client', $vote->idClient);
            echo '<td align="center" class="assignData">'.htmlEncode($idVoter).'</td>';
            echo '<td align="center" class="assignData">'.htmlDisplayNumericWithoutTrailingZeros($vote->value).'</td>';
            echo '<td align="center" class="assignData">'.htmlEncode($idUser).'</td>';
            echo '<td align="center" class="assignData">'.htmlEncode($idClient).'</td>';
            echo '</tr>';
          }
          echo '</table>';
        }
      }
    }
    
    private static $_colCaptionTransposition = array(
    );
    
    
    public function setAttributes() {
//       $voteAttr = new VotingAttribution();
//       $obj = new $this->refType($this->refId);
//       $idType= "id".$this->refType."Type";
//       $idType = $obj->$idType;
//       $canVote = $voteAttr->canVote($this->refType, $this->refId, $idType);
//       if($canVote){
//         self::$_fieldsAttributes['_button_vote']='';
//       }
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
    
    public static function isVotable($refType,$refIId,$idType=null) {
      $result = false;
      if(Module::isModuleActive('moduleVoting')){
        $voting = new VotingItem();
        if($refType=='TicketSimple')$refType='Ticket';
        $exist = $voting->countSqlElementsFromCriteria(array('refType'=>$refType,'refId'=>$refIId));
        if(!$exist){
          $voting->refId = $refIId;
          $voting->refType = $refType;
          $voting->save();
        }
        $idRule = self::getIdUseRule($refType,$refIId);
        if($idRule)$result = true;
      }
      return $result;
    }
    
    public static function getIdUseRule($refType,$refId) {
      $result = null;
      $item = new $refType($refId,true);
      $useRule = new VotingUseRule();
      if (pq_substr($refType,-4)=='Main') {
        $refType=pq_substr($refType,0,-4);
      }
      if($refType=='TicketSimple')$refType='Ticket';
      $typeName=SqlElement::getTypeName($refType);
      //Filter on project + refType + idType
      $useRulePerfect = $item->getSingleSqlElementFromCriteria('VotingUseRule', array('idType'=>$item->$typeName,'idProject'=>$item->idProject));
      if($useRulePerfect->id)$result=$useRulePerfect->id;
      //Filter project + refType
      if(!$result){
        $useRuleNoIdType = $useRule->getSqlElementsFromCriteria(array('idProject'=>$item->idProject,'refType'=>$refType,'idType'=>null));
        foreach ($useRuleNoIdType as $vote){
          if(!$vote->idType){
            $result=$vote->id;
            break;
          }
        }
      }
      //Filter on refType + idType
      if(!$result){
        $useRuleNoProject = $useRule->getSqlElementsFromCriteria(array('idProject'=>null,'idType'=>$item->$typeName,'refType'=>$refType));
        foreach ($useRuleNoProject as $vote){
          if(!$vote->idProject){
            $result=$vote->id;
            break;
          }
        }
      }
      //Filter on refType
      if(!$result){
        $useRuleNoProjectNoIdType = $useRule->getSqlElementsFromCriteria(array('refType'=>$refType,'idProject'=>null,'idType'=>null));
        foreach ($useRuleNoProjectNoIdType as $vote){
          if(!$vote->idType){
            $result=$vote->id;
            break;
          }
        }
      }
      return $result;
    }
    
    public static function getVotableClassList(){
      return array('Ticket', 'Activity', 'Requirement');
    }
    
    public static function isVotableClass($itemClass){
      $array= self::getVotableClassList();
      $result = false;
      if(in_array($itemClass,$array)){
        $result = true;
      }
      return $result;
    }
    
    public static function workField($itemClass){
      $result = '';
      if($itemClass=='TicketSimple')$itemClass='Ticket';
      if($itemClass=='Ticket'){
        $result = 'plannedWork';
      }elseif($itemClass=='Activity'){
        $result = 'validatedWork';
      }elseif($itemClass=='Requirement'){
        $result='plannedWork';
      }
      return $result;
    }
    
    public static function isBlockingStatus($idStatusUseRule, $idStatusItem){
      if(!$idStatusUseRule)return 0;
      $result = 0;
      $statusUseRule = new Status($idStatusUseRule);
      $statusItem = new Status($idStatusItem);
      if($statusItem->sortOrder >= $statusUseRule->sortOrder)$result=1;
      return $result;
    }

}