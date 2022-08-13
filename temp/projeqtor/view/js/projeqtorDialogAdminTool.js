///*******************************************************************************
// * COPYRIGHT NOTICE *
// * 
// * Copyright 2009-2017 ProjeQtOr - Pascal BERNARD - support@projeqtor.org Contributors : -
// * 
// * This file is part of ProjeQtOr.
// * 
// * ProjeQtOr is free software: you can redistribute it and/or modify it under
// * the terms of the GNU Affero General Public License as published by the Free Software
// * Foundation, either version 3 of the License, or (at your option) any later
// * version.
// * 
// * ProjeQtOr is distributed in the hope that it will be useful, but WITHOUT ANY
// * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
// * A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
// * 
// * You should have received a copy of the GNU Affero General Public License along with
// * ProjeQtOr. If not, see <http://www.gnu.org/licenses/>.
// * 
// * You can get complete code of ProjeQtOr, other resource, help and information
// * about contributors at http://www.projeqtor.org
// * 
// * DO NOT REMOVE THIS NOTICE **
// ******************************************************************************/
//
//// ============================================================================
//// All specific ProjeQtOr functions and variables for Dialog Purpose
//// This file is included in the main.php page, to be reachable in every context
//// ============================================================================

// ===========================================================================================
// ADMIN functionalities
// ===========================================================================================
//
var cronCheckIteration=50; // Number of cronCheckTimeout to wait max
function adminLaunchScript(scriptName, needRefresh) {
  if (typeof needRefresh == 'undefined')
    needRefresh=true;
  var url="../tool/" + scriptName + ".php";
  dojo.xhrGet({
    url : url+"?csrfToken="+csrfToken,
    handleAs : "text",
    load : function(data, args) {

    },
    error : function() {
    }
  });
  if (scriptName == 'cronRun') {
    if (needRefresh)
      setTimeout('loadContent("admin.php","centerDiv");', 3000);
  } else if (scriptName == 'cronStop' && needRefresh) {
    i=120;
    cronCheckIteration=5 * cronSleepTime;
    setTimeout('adminCronCheckStop();', 1000);
  }
}

function adminCronCheckStop() {
  dojo.xhrGet({
    url : "../tool/cronCheck.php?csrfToken="+csrfToken,
    handleAs : "text",
    load : function(data, args) {
      if (data != 'running') {
        loadContent("admin.php", "centerDiv");
      } else {
        cronCheckIteration--;
        if (cronCheckIteration > 0) {
          setTimeout('adminCronCheckStop();', 1000);
        } else {
          loadContent("admin.php", "centerDiv");
        }
      }
    },
    error : function() {
      loadContent("admin.php", "centerDiv");
    }
  });
}

function adminCronRelaunch() {
  var url="../tool/cronRelaunch.php";
  dojo.xhrGet({
    url : url+"?csrfToken="+csrfToken,
    handleAs : "text",
    load : function(data, args) {
    },
    error : function() {
    }
  });
}

function adminCronRestart() {
  if (cronCheckCount > 0)
    return;
  cronCheckCount=1;
  dojo.xhrGet({
    url : "../tool/cronStop.php?csrfToken="+csrfToken,
    handleAs : "text",
    load : function(data, args) {
      setTimeout("adminCronRestartCheck();", 1000);
    }
  });
}
var cronCheckCount=0;
function adminCronRestartCheck() {
  dojo.xhrGet({
    url : "../tool/cronCheck.php?csrfToken="+csrfToken,
    handleAs : "text",
    load : function(data, args) {
      if (data == 'running') {
        cronCheckCount++;
        if (cronCheckCount < 60)
          setTimeout("adminCronRestartCheck();", 1000);
      } else {
        adminCronRestartRun();
      }
    }
  });
}
function adminCronRestartRun() {
  dojo.xhrGet({
    url : "../tool/cronRun.php?csrfToken="+csrfToken,
    handleAs : "text",
    load : function(data, args) {
    }
  });
  cronCheckCount=0;
}
function adminSendAlert() {
  formVar=dijit.byId("adminForm");
  if (formVar.validate()) {
    loadContent(
        "../tool/adminFunctionalities.php?adminFunctionality=sendAlert",
        "resultDivMain", "adminForm", true, 'admin');
  }
}

function adminDisconnectAll(toConfirm) {
  actionOK=function() {
    loadContent(
        "../tool/adminFunctionalities.php?adminFunctionality=disconnectAll&element=Audit",
        "resultDivMain", "adminForm", true, 'admin');
  };
  if (toConfirm) {
    msg=i18n('confirmDisconnectAll');
    showConfirm(msg, actionOK);
  }
}

function maintenance(operation, item) {
  if (operation == "updateReference") {
    loadContent("../tool/adminFunctionalities.php?adminFunctionality="
        + operation + "&element=" + item, "resultDivMain", "adminForm", true,
        'admin');
  } else {
    var nb=0;
    if (operation != 'read') {
      if(dijit.byId(operation + item + "Days"))nb=dijit.byId(operation + item + "Days").get('value');
      else if(dijit.byId("maintenance"+(operation.charAt(0).toUpperCase() + operation.slice(1)) + item )){
        nb=dijit.byId("maintenance"+(operation.charAt(0).toUpperCase() + operation.slice(1)) + item ).get('value');
      }
    }
    loadContent(
        "../tool/adminFunctionalities.php?adminFunctionality=maintenance&operation="
            + operation + "&item=" + item + "&nbDays=" + nb, "resultDivMain",
        "adminForm", true, 'admin');
  }
}
function adminSetApplicationTo(newStatus) {
  var url="../tool/adminFunctionalities.php?adminFunctionality=setApplicationStatusTo&newStatus="
      + newStatus;
  showWait();
  dojo.xhrPost({
    url : url+"&csrfToken="+csrfToken,
    form : "adminForm",
    handleAs : "text",
    load : function(data, args) {
      loadContent("../view/admin.php", "centerDiv");
    },
    error : function() {
    }
  });
}

// ************************************************************
// Code to select columns to be exported
// ************************************************************
var ExportType='';
// open the dialog with checkboxes
function openExportDialog(Type) {
  ExportType=Type;
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  var objectClass=(dojo.byId('objectClassList')) ? dojo.byId('objectClassList').value
      : dojo.byId('objectClass').value;
  var params="&objectClass=" + objectClass;
  if (objectClass == 'Work') {
    params+="&dateWeek=" + dojo.byId("dateWeek").value;
    params+="&dateMonth=" + dojo.byId("dateMonth").value;
    params+="&userId=" + dojo.byId("userId").value;
  }
  var listId='';
  if (objectClass=='Activity') {
    grid = dijit.byId("objectGrid");
    if (grid) {
      grid.store.fetch({
        onComplete : function(items) {
          dojo.forEach(items, function(item, index) {
            if (listId!='') listId+=',';
            listId+=item.id;
          });
        }
      });
    }
    dojo.byId('activityIdList').value = listId;
  }
  loadDialog("dialogExport", null, true, params, true, true, 'dialogExport', null, 'activityIdListForm');
}

// close the dialog with checkboxes
function closeExportDialog() {
  dijit.byId("dialogExport").hide();
}

// save current state of checkboxes
function saveCheckboxExport(obj, idUser) {
  var val=dojo.byId('column0').value;
  var toStore="";
  val=eval(val);
  for (i=1; i <= val; i++) {
    var checkbox=dijit.byId('column' + i);
    if (checkbox) {
      if (!checkbox.get('checked')) {
        var field=checkbox.value;
        toStore+=field + ";";
      }
    }
  }
  dojo.xhrPost({
    url : "../tool/saveCheckboxes.php?&objectClass=" + obj + "&toStore="
        + toStore+"&csrfToken="+csrfToken,
    handleAs : "text",
    load : function() {
    }
  });
}

// Executes the report (shows the print/pdf/csv)
function executeExport(obj, idUser) {
  var verif=0;
  var val=dojo.byId('column0').value;
  var exportReferencesAs=dijit.byId('exportReferencesAs').get('value');
  var exportHtml=(dijit.byId('exportHtml').get('checked')) ? '1' : '0';
  var separatorCSV=dijit.byId('separatorCSV').get('value');
  if (obj == 'Work') {
    var exportDateAs=dijit.byId('exportDateAs').get('value');
    var exportRessourceAs=dijit.byId('exportRessourceAs').get('value');
  }
  val=eval(val);
  var toExport="";
  for (var i=1; i <= val; i++) {
    var checkbox=dijit.byId('column' + i);
    if (checkbox) {
      if (checkbox.get('checked')) {
        verif=1;
      } else {
        var field=checkbox.value;
        toExport+=field + ";";
      }
    }
  }
  if(!dojo.byId('isUOexportable').value)toExport+='CatalogUo;';
  if (dijit.byId('documentVersionLastOnly')
      && dijit.byId('documentVersionLastOnly').get('checked')) {
    toExport+='documentVersionAll';
  }
  if (verif == 1) {
    if (ExportType == 'csv') {
      if (obj != 'Work') {
        showPrint("../tool/jsonQuery.php?exportHtml=" + exportHtml
            + "&exportReferencesAs=" + exportReferencesAs + "&hiddenFields="
            + toExport + "&separatorCSV=" + separatorCSV, 'list', null, 'csv');
      } else {
        showPrint("../tool/jsonQuery.php?exportHtml=" + exportHtml
            + "&exportReferencesAs=" + exportReferencesAs + "&hiddenFields="
            + toExport + "&exportDateAs=" + exportDateAs
            + "&exportRessourceAs=" + exportRessourceAs + "&separatorCSV="
            + separatorCSV, 'list', null, 'csv');
      }
    }
    saveCheckboxExport(obj, idUser);
    closeExportDialog(obj, idUser);
  } else {
    showAlert(i18n('alertChooseOneAtLeast'));
  }
}

// Check or uncheck all boxes
function checkExportColumns(scope) {
  if (scope == 'aslist') {
    showWait();
    dojo
        .xhrGet({
          url : "../tool/getColumnsList.php?objectClass="
              + ((dojo.byId('objectClassList')) ? dojo.byId('objectClassList').value
                  : dojo.byId('objectClass').value)+"&csrfToken="+csrfToken,
          load : function(data) {
            var list=";" + data;
            var val=dojo.byId('column0').value;
            val=eval(val);
            var allChecked=true;
            for (i=1; i <= val; i++) {
              var checkbox=dijit.byId('column' + i);
              if (checkbox) {
                var search=";" + checkbox.value + ";";
                if (list.indexOf(search) >= 0) {
                  checkbox.set('checked', true);
                } else {
                  checkbox.set('checked', false);
                  allChecked=false;
                }
              }
            }
            dijit.byId('checkUncheck').set('checked', allChecked);
            hideWait();
          },
          error : function() {
            hideWait();
          }
        });
  } else {
    var check=dijit.byId('checkUncheck').get('checked');
    var val=dojo.byId('column0').value;
    val=eval(val);
    for (i=1; i <= val; i++) {
      var checkbox=dijit.byId('column' + i);
      if (checkbox) {
        checkbox.set('checked', check);
      }
    }
  }
}

// ********************************************************************************************
// WORKFLOW PARAMETERS (selection of status)
// ********************************************************************************************
var workflowParameterAllChecked=true;
function showWorkflowParameter(id) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  callBack=function() {
  };
  workflowParameterAllChecked=true;
  var params='&idWorkflow=' + id;
  loadDialog('dialogWorkflowParameter', callBack, true, params);
}

function saveWorkflowParameter() {
  loadContent("../tool/saveWorkflowParameter.php", "resultDivMain",
      "dialogWorkflowParameterForm", true);
  dijit.byId('dialogWorkflowParameter').hide();
}

function dialogWorkflowParameterUncheckAll() {
  dojo.query(".workflowParameterCheckbox").forEach(
      function(node, index, nodelist) {
        var id=node.getAttribute('widgetid');
        if (dijit.byId(id)) {
          dijit.byId(id).set('checked', !workflowParameterAllChecked);
        }
      });
  workflowParameterAllChecked=!workflowParameterAllChecked;
}

var workflowProfileParameterAllChecked=true;
function showWorkflowProfileParameter(id) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  callBack=function() {
  };
  workflowProfileParameterAllChecked=true;
  var params='&idWorkflow=' + id;
  loadDialog('dialogWorkflowProfileParameter', callBack, true, params);
}

function saveWorkflowProfileParameter() {
  loadContent("../tool/saveWorkflowProfileParameter.php", "resultDivMain",
      "dialogWorkflowProfileParameterForm", true);
  dijit.byId('dialogWorkflowProfileParameter').hide();
}

function dialogWorkflowProfileParameterUncheckAll() {
  dojo.query(".workflowProfileParameterCheckbox").forEach(
      function(node, index, nodelist) {
        var id=node.getAttribute('widgetid');
        if (dijit.byId(id)) {
          dijit.byId(id).set('checked', !workflowProfileParameterAllChecked);
        }
      });
  workflowProfileParameterAllChecked=!workflowProfileParameterAllChecked;
}

// =============================================================================
// = KpiThreshold
// =============================================================================

function addKpiThreshold(idKpiDefinition) {
  var params="&mode=add&idKpiDefinition=" + idKpiDefinition;
  loadDialog('dialogKpiThreshold', null, true, params);
}

function editKpiThreshold(idKpiThreshold) {
  var params="&mode=edit&idKpiThreshold=" + idKpiThreshold;
  loadDialog('dialogKpiThreshold', null, true, params);
}

function saveKpiThreshold() {
  if (!dijit.byId("kpiThresholdName").get('value')) {
    showAlert(i18n('messageMandatory', new Array(i18n('colName'))));
    return false;
  }
  if (!dijit.byId("kpiThresholdValue").get('value')
      && dijit.byId("kpiThresholdValue").get('value') != '0') {
    showAlert(i18n('messageMandatory', new Array(i18n('colValue'))));
    return false;
  }
  loadContent("../tool/saveKpiThreshold.php", "resultDivMain",
      "dialogKpiThresholdForm", true, 'kpiThreshold');
  dijit.byId('dialogKpiThreshold').hide();
}

function removeKpiThreshold(idKpiThreshold) {
  var params="?kpiThresholdId=" + idKpiThreshold;
  actionOK=function() {
    loadContent("../tool/removeKpiThreshold.php" + params, "resultDivMain",
        null, true, 'kpiThreshold');
  };
  msg=i18n('confirmDelete', new Array(i18n('KpiThreshold'), idKpiThreshold));
  showConfirm(msg, actionOK);
}

//====================================
//CRON FEATURES
//====================================

function cronActivation(scope,adminView){
  showWait();
  dojo.xhrGet({
        url : "../tool/cronExecutionStandard.php?operation=activate&cronExecutionScope="
            + scope+"&csrfToken="+csrfToken,
        load : function(data) {
          if (adminView) {
            loadContent("../view/admin.php", "centerDiv");
          } else {
            loadContent("../view/parameter.php?type=globalParameter",
                "centerDiv");
          }
          adminCronRestart();
        },
        error : function(data) {
          hideWait();
        }
      });
}

function cronExecutionDefinitionSave(adminView){
  var finish=false;
  showWait();
  dojo.xhrPost({
    url : "../tool/cronExecutionStandard.php?operation=saveDefinition&csrfToken="+csrfToken,
    form : "cronDefiniton",
    handleAs : "text",
    load : function(data, args) {
      dijit.byId('dialogCronDefinition').hide();
      if(adminView){
    	  loadContent("../view/admin.php", "centerDiv"); 
      }else{
    	  loadContent("../view/parameter.php?type=globalParameter", "centerDiv");
      }
      hideWait();
    },
    error : function() {
      hideWait();
    }
  });
}


function addDataCloning(){
  loadDialog('dialogAddDataCloning',null,true,null,true);
}

function copyDataCloning(idDataCloning){
  var param = '&idDataCloningParent='+idDataCloning;
  loadDialog('dialogAddDataCloning',null,true,param,true);
}

function refreshCronIconStatus(status){
  if (dojo.byId('actualCronStatusInDiv') && dojo.byId('actualCronStatusInDiv').value.toLowerCase()==status.toLowerCase()) return;
  var url='../view/refreshCronIconStatus.php';
      url+='?cronStatus=' + status;
    loadDiv(url, 'menuBarCronStatus', null, null);
}

function checkCronStatus(status){
  dojo.byId('cronStatus').style.filter = 'grayscale(100%)';
  if (status=='Stopped') {
    adminLaunchScript("cronRun", false);
    refreshCronIconStatus("running");
  } else {
    adminLaunchScript("cronStop", false);
    dojo.byId('cronStatusButton').title = i18n('cronStopping');
    //refreshCronIconStatus("stopped");
  }
}
