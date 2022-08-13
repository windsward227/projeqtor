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
 * Presents an object. 
 */
require_once "../tool/projeqtor.php";
require_once "../tool/formatter.php";
  scriptLog('   ->/view/votingAttributionFollowUpMain.php');  
?>
<input type="hidden" name="objectClassManual" id="objectClassManual" value="VotingAttributionFollowUp" />
  <div id="listDiv" dojoType="dijit.layout.ContentPane" region="top" style="height:75px;">
   <?php include 'votingAttributionFollowUpList.php'?>
  </div>
  <div id="detailDivAttributionFollowUp" dojoType="dijit.layout.ContentPane" region="center" style="height:100%;width:100%;overflow-y:auto;">
    <form dojoType="dijit.form.Form" name="formVotingAttributionFollowUp" id="formVotingAttributionFollowUp"  method="Post" >
      <div style="height:100%;width:100%;overflow-y:auto;">
      <?php VotingAttribution::drawVotingAttributionFollowUpGlobal($idResource,$idClient, $element, $left);?>
      </div>
    </form>
  </div>


