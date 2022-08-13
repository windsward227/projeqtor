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
  scriptLog('   ->/view/reportsMain.php');

  $listHeight = Parameter::getUserParameter('contentPaneDetailReportDiv');
?>
<input type="hidden" name="objectClassManual" id="objectClassManual" value="Report" />
<div class="container" dojoType="dijit.layout.BorderContainer" id="mainReportContainer">
  <input typr="hidden" id="listHeightReport" value="<?php echo $listHeight;?>"/>
  <div id="listReportDiv" dojoType="dijit.layout.ContentPane" region="top" splitter="true" style="height:100%;">
   <?php include 'reportsList.php'?>
  </div>
  <div id="detailReportDiv" dojoType="dijit.layout.ContentPane" region="center">
    <script type="dojo/connect" event="resize" args="evt">
              if(dojo.byId("detailReportDiv").offsetHeight > 0){
                dojo.byId('listHeightReport').value = dojo.byId("listReportDiv").offsetHeight;
                saveContentPaneResizing("contentPaneDetailReportDiv", dojo.byId("listReportDiv").offsetHeight, true);
              }
         </script>
   <?php $noselect=true; //include 'objectDetail.php'; ?>
  </div>
</div>   