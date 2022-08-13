<?php
/*** COPYRIGHT NOTICE *********************************************************
 *
 * Copyright 2009-2016 ProjeQtOr - Pascal BERNARD - support@projeqtor.org
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
include_once ("../tool/projeqtor.php");
$keyDownEventScript=NumberFormatter52::getKeyDownEvent();
$mode = RequestHandler::getValue('mode',false,null);
$idProject=RequestHandler::getValue('idProject',false,null);
$idClientContract=RequestHandler::getValue('idClientContract',false,null);
$id=RequestHandler::getValue('idWorkTokenClientContract',false,null);
$used =(RequestHandler::isCodeSet('used') and RequestHandler::getValue('used')!=0)? RequestHandler::getValue('used'):null;
$detailHeight=50;
$detailWidth=800;
$selection=null;
if($id){
  $workTokenClientContract = new WorkTokenClientContract($id);
  $selection=$workTokenClientContract->idWorkToken;
  $idWorkTokenClientContract=$workTokenClientContract->idClientContract;
}

$tokenDef= new TokenDefinition();
$project= new Project($idProject);
$lstProjId=$project->getTopProjectList(true);
$lstProjId=implode(",", $lstProjId);
$where="idProject in ($lstProjId) and idle!=1";
$lstTokenDef=$tokenDef->getSqlElementsFromCriteria(null,null,$where);

?>
<div>
<form dojoType="dijit.form.Form" id='workTokenClientContractForm' name='workTokenClientContractForm' onSubmit="return false;">
  <input id="idClientContract" name="idClientContract" type="hidden" value="<?php echo $idClientContract;?>" />
  <input id="mode" name="mode" type="hidden" value="<?php echo $mode;?>" />
  <?php 
//   foreach ($lstTokenDef as $idV=>$val){
//     echo '<input type="hidden" id="'.$val->id.'_Amount" value="'.$val->amount.'"/>';
//     echo '<input type="hidden" id="'.$val->id.'_Duration" value="'.$val->duration.'"/>';
//   }
  ?>
  <?php 
    if(isset($workTokenClientContract)) echo '<input id="idWorkTokenClientContract" name="idWorkTokenClientContract" type="hidden" value="'.$id.'" />';
  ?>
    <table style="width:100%;padding:5%;">
      <tr><td>&nbsp;</td></tr>
      <tr>
        <td style="width:100px;" class="dialogLabel" >
          <label for="tokentType" ><?php echo lcfirst(i18n("TokenDefinition"));?>&nbsp;<?php if(!isNewGui()){?>:<?php }?>&nbsp;</label>
        </td>
        <td>
          <select dojoType="dijit.form.FilteringSelect"  <?php echo autoOpenFilteringSelect();?> id="tokentType" name="tokentType" style="width:300px;"
                class="input required" required="required" <?php if($used) echo "readonly";?>>
                <?php 
                  if(!$id) echo '<option value=""></option>';
                  foreach ($lstTokenDef as $id=>$val){
                    echo '<option value="'. $val->id.'" '.(($id and $val->id==$selection)?"selected":"").'>'.$val->name.'</option>';
                  }
                ?>
          </select> 
        </td>
      </tr>
      <tr>
        <td class="dialogLabel" >
          <label for="quantity" ><?php echo lcfirst(i18n("quantity"));?>&nbsp;<?php if(!isNewGui()){?>:<?php }?>&nbsp;</label>
        </td>
        <td>
          <span class="nobr">
            <div id="quantity" name="quantity" 
                 dojoType="dijit.form.NumberTextBox" 
                 constraints="{min:0}" 
                 style="width:50px; text-align: right;border-left: 3px solid rgb(255, 0, 0);" 
                 value="<?php echo (isset($workTokenClientContract))? $workTokenClientContract->quantity:'';?>"
                 required="true" <?php if($used) echo "readonly";?>>
                 <?php echo $keyDownEventScript;?>
            </div>
          </span>
        </td>
      </tr>
      <tr>
        <td style="width:100px;" class="dialogLabel" >
          <label for="labelToken" ><?php echo i18n("colDescription");?>&nbsp;<?php if(!isNewGui()){?>:<?php }?>&nbsp;</label>
        </td>
        <td>
          <div dojoType="dijit.form.Textarea" id="labelToken" name="labelToken"
          style="width:314px;"
          class="input"><?php echo (isset($workTokenClientContract))?pq_htmlspecialchars($workTokenClientContract->description):'';?></div>   
        </td>
      </tr>
      <tr><td>&nbsp;</td></tr>
      <tr><td>&nbsp;</td></tr>
      <tr>
        <td align="center" colspan="2">
          <input type="hidden" id="workTokenClientContractAction">
          <button class="mediumTextButton" dojoType="dijit.form.Button" type="button" onclick="dijit.byId('dialogWorkTokenClientContract').hide();">
            <?php echo i18n("buttonCancel");?>
          </button>
          <button class="mediumTextButton" dojoType="dijit.form.Button" type="submit" id="dialogworkTokenClientContractSubmit" onclick="protectDblClick(this);saveWorkTokenClientContract();return false;">
            <?php echo i18n("buttonOK");?>
          </button>
        </td>
      </tr>
    </table>
  </form>
</div>