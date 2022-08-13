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
$readOnly = false;
$keyDownEventScript=NumberFormatter52::getKeyDownEvent();
$currency=Parameter::getGlobalParameter('currency');
$currencyPosition=Parameter::getGlobalParameter('currencyPosition');
$mode = RequestHandler::getValue('mode',false,null);
$idCommand=RequestHandler::getValue('idCommand',false,null);
$id = RequestHandler::getId('id');
$idWorkUnit = RequestHandler::getId('idWorkUnit');
$idComplexity = RequestHandler::getId('idComplexity');
$quantity = RequestHandler::getNumeric('quantity');
$unitAmount = RequestHandler::getNumeric('unitAmount');
$commandAmount = RequestHandler::getNumeric('commandAmount');
$obj = new Command($idCommand);
$minQuantity = 0.01;
if($id){
  $minQuantityBilled = 0;
  $minQuantityDone = 0;
  $workCommand = new WorkCommand($id);
  $workCommandBilled = new WorkCommandBilled();
  $workCommandDone = new WorkCommandDone();
  $number=$workCommandBilled->countSqlElementsFromCriteria(array('idWorkCommand'=>$id));
  if($number > 0){
    $readOnly = true;
  }
  if($readOnly==false){
    $number=$workCommandDone->countSqlElementsFromCriteria(array('idWorkCommand'=>$id));
    if($number > 0) {
      $readOnly = true;
    }
  }
  
  $listWorkCommandBilled = $workCommandBilled->getSqlElementsFromCriteria(array('idWorkCommand'=>$id));
  foreach ($listWorkCommandBilled as $wB){
    $minQuantityBilled+=$wB->billedQuantity;
  }
  $listWorkCommandDone = $workCommandDone->getSqlElementsFromCriteria(array('idWorkCommand'=>$id));
  foreach ($listWorkCommandDone as $wD){
    $minQuantityDone+=$wD->doneQuantity;
  }
  $minQuantity = $minQuantityBilled;
  if($minQuantity < $minQuantityDone) $minQuantity = $minQuantityDone;
}
?>
  <table>
    <tr>
      <td>
       <form dojoType="dijit.form.Form" id='workCommandForm' name='workCommandForm' onSubmit="return false;">
        <input id="mode" name="mode" type="hidden" value="<?php echo $mode;?>" />
        <input id="id" name="id" type="hidden" value="<?php echo $id;?>" />
        <input id="idCommand" name="idCommand" type="hidden" value="<?php echo $idCommand;?>" />
         <table>
          <tr>
             <td class="dialogLabel"  >
               <label for="workCommandWorkUnit" ><?php echo i18n("colIdWorkUnit") ?>&nbsp;<?php if(!isNewGui()){?>:<?php }?>&nbsp;</label>
             </td>
             <td>
               <select dojoType="dijit.form.FilteringSelect"
              <?php echo autoOpenFilteringSelect();?>
                id="workCommandWorkUnit" name="workCommandWorkUnit" <?php if($readOnly==true){?>readOnly<?php }?>
                class="input" required="required" style="border-left:3px solid red !important;"
                onChange="workCommandChangeIdWorkUnit();" 
                missingMessage="<?php echo i18n('messageMandatory',array(i18n('colIdWorkUnit')));?>" >
                 <?php htmlDrawOptionForReference('idWorkUnit',$idWorkUnit, $obj, false); ?>
               </select> 
             </td>
           </tr>
           <tr>
             <td class="dialogLabel"  >
               <label for="workCommandComplexity" ><?php echo i18n("colIdComplexity") ?>&nbsp;<?php if(!isNewGui()){?>:<?php }?>&nbsp;</label>
             </td>
             <td>
               <select dojoType="dijit.form.FilteringSelect"
              <?php echo autoOpenFilteringSelect();?>
                id="workCommandComplexity" name="workCommandComplexity"
                class="input" required="required" style="border-left:3px solid red !important;"
                onChange="workCommandChangeIdComplexity();"  <?php if($mode!="edit"){?>readOnly<?php }?>  <?php if($mode=="edit" and $readOnly==true){?>readOnly<?php }?> 
                missingMessage="<?php echo i18n('messageMandatory',array(i18n('idComplexity')));?>" >
                 <?php htmlDrawOptionForReference('idComplexity',$idComplexity, $obj, false); ?>
               </select> 
             </td>
           </tr>
           <tr>
             <td class="dialogLabel" >
               <label for="workCommandUnitAmount" ><?php echo i18n("colUnitAmount");?>&nbsp;<?php if(!isNewGui()){?>:<?php }?>&nbsp;</label>
             </td>
             <td>
             <?php if ($currencyPosition=='before') echo $currency;?>
               <input dojoType="dijit.form.NumberTextBox" 
                id="workCommandUnitAmount" name="workCommandUnitAmount"
                readonly 
                style="width:100px;"
                class="input"  value="<?php echo $unitAmount;?>">  
               </input> 
               <?php if ($currencyPosition=='after') echo $currency;?>
             </td>
           </tr>
            <tr>
             <td class="dialogLabel" >
               <label for="workCommandQuantity" ><?php echo i18n("colQuantity");?>&nbsp;<?php if(!isNewGui()){?>:<?php }?>&nbsp;</label>
             </td>
             <td>
               <div dojoType="dijit.form.NumberTextBox" 
                  id="workCommandQuantity" name="workCommandQuantity"
                  style="width:100px;border-left:3px solid red !important;" required="required" 
                  invalidMessage="<?php echo i18n('quantityCanBeInferiorThan',$minQuantity);?>" 
                  onChange="workCommandChangeQuantity();" <?php if($mode!="edit"){?>readOnly<?php }?> constraints="{min:<?php echo $minQuantity;?>}"
                  class="input"  value="<?php echo $quantity; ?>">
                  <?php echo $keyDownEventScript;?>  
               </div>
             </td>
            </tr>
            <tr>
             <td class="dialogLabel" >
               <label for="workCommandAmount" ><?php echo i18n("colAmount");?>&nbsp;<?php if(!isNewGui()){?>:<?php }?>&nbsp;</label>
             </td>
             <td>
             <?php if ($currencyPosition=='before') echo $currency;?>
               <input dojoType="dijit.form.NumberTextBox" 
                id="workCommandAmount" name="workCommandAmount"
                readonly 
                style="width:100px;"
                class="input"  value="<?php echo $commandAmount;?>">  
               </input> 
               <?php if ($currencyPosition=='after') echo $currency;?>
             </td>
           </tr>
           <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
         </table>
        </form>
      </td>
    </tr>
    <tr>
      <td align="center">
        <input type="hidden" id="workCommandAction">
        <button class="mediumTextButton" dojoType="dijit.form.Button" type="button" onclick="dijit.byId('dialogWorkCommand').hide();">
          <?php echo i18n("buttonCancel");?>
        </button>
        <button class="mediumTextButton" dojoType="dijit.form.Button" type="submit" id="dialogWorkCommandSubmit" onclick="protectDblClick(this);saveWorkCommand();return false;">
          <?php echo i18n("buttonOK");?>
        </button>
      </td>
    </tr>
  </table>
