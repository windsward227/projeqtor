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

//=============================================================================
//= Situation
//=============================================================================

function addSituation() {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  if (dijit.byId("situationToolTip")) {
    dijit.byId("situationToolTip").destroy();
    dijit.byId("situationComment").set("class", "");
  }
  pauseBodyFocus();
  var callBack=function() {
    var editorType=dojo.byId("situationEditorType").value;
    if (editorType == "CK" || editorType == "CKInline") { // CKeditor type
      ckEditorReplaceEditor("situationComment", 995);
    } else if (editorType == "text") {
      dijit.byId("situationComment").focus();
      dojo.byId("situationComment").style.height=(screen.height * 0.6) + 'px';
      dojo.byId("situationComment").style.width=(screen.width * 0.6) + 'px';
    } else if (dijit.byId("situationEditor")) { // Dojo type editor
      dijit.byId("situationEditor").set("class", "input");
      dijit.byId("situationEditor").focus();
      dijit.byId("situationEditor").set("height", (screen.height * 0.6) + 'px'); // Works
      // on
      // first
      // time
      dojo.byId("situationEditor_iframe").style.height=(screen.height * 0.6)
          + 'px'; // Works after first time
    }
  };
  var params="&objectClass=" + dojo.byId('objectClass').value;
  params+="&objectId=" + dojo.byId("objectId").value;
  loadDialog('dialogSituation', callBack, true, params, true);
}

function editSituation(situationId) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  if (dijit.byId("situationToolTip")) {
    dijit.byId("situationToolTip").destroy();
    dijit.byId("situationComment").set("class", "");
  }
  pauseBodyFocus();
  var callBack=function() {
    var editorType=dojo.byId("situationEditorType").value;
    if (editorType == "CK" || editorType == "CKInline") { // CKeditor type
      ckEditorReplaceEditor("situationComment", 995);
    } else if (editorType == "text") {
      dijit.byId("situationComment").focus();
      dojo.byId("situationComment").style.height=(screen.height * 0.6) + 'px';
      dojo.byId("situationComment").style.width=(screen.width * 0.6) + 'px';
    } else if (dijit.byId("situationEditor")) { // Dojo type editor
      dijit.byId("situationEditor").set("class", "input");
      dijit.byId("situationEditor").focus();
      dijit.byId("situationEditor").set("height", (screen.height * 0.6) + 'px'); // Works
      // on
      // first
      // time
      dojo.byId("situationEditor_iframe").style.height=(screen.height * 0.6)
          + 'px'; // Works after first time
    }
  };
  var params="&objectClass=" + dojo.byId('objectClass').value;
  params+="&objectId=" + dojo.byId("objectId").value;
  params+="&situationId=" + situationId;
  loadDialog('dialogSituation', callBack, true, params, true);
}

function saveSituation() {
  var formVar=dijit.byId('situationForm');
  if (formVar.validate()) {
    var editorType=dojo.byId("situationEditorType").value;
    if (editorType == "CK" || editorType == "CKInline") {
      situationEditor=CKEDITOR.instances['situationComment'];
      situationEditor.updateElement();
      var tmpCkEditor=situationEditor.document.getBody().getText();
      var tmpCkEditorData=situationEditor.getData();
    }
    loadContent("../tool/saveSituation.php", "resultDivMain", "situationForm",
        true, 'situation');
    dijit.byId('dialogSituation').hide();
  } else {
    showAlert(i18n("alertInvalidForm"));
    return;
  }
}

function removeSituation(situationId) {
  var param="?situationId=" + situationId;
  param+="&situationRefType=" + dojo.byId('objectClass').value;
  param+="&situationRefId=" + dojo.byId("objectId").value;
  param+="&action=remove";
  actionOK=function() {
    loadContent("../tool/saveSituation.php" + param, "resultDivMain",
        "situationForm", true, 'situation');
  };
  msg=i18n('confirmDelete', new Array(i18n('Situation'), situationId));
  showConfirm(msg, actionOK);
}

function situationSelectPredefinedText(idPrefefinedText) {
  dojo.xhrPost({
    url : '../tool/getPredefinedSituation.php?id=' + idPrefefinedText+'&csrfToken='+csrfToken,
    handleAs : "text",
    load : function(data, args) {
      if (data) {
        var ps=JSON.parse(data);
        dijit.byId('situationSituation').set('value', ps.situation);
        var editorType=dojo.byId("situationEditorType").value;
        if (editorType == "CK" || editorType == "CKInline") { // CKeditor type
          CKEDITOR.instances['situationComment'].setData(ps.comment);
        } else if (editorType == "text") {
          dijit.byId('situationComment').set('value', ps.comment);
          dijit.byId('situationComment').focus();
        } else if (dijit.byId('situationCommentEditor')) {
          dijit.byId('situationComment').set('value', ps.comment);
          dijit.byId('situationCommentEditor').set('value', ps.comment);
          dijit.byId("situationCommentEditor").focus();
        }
      }
    }
  });
}

function billLineChangeCatalog() {
  if (!dijit.byId("billLineIdCatalog")
      || !dijit.byId("billLineIdCatalog").get("value"))
    return;
  var idCatalog=dijit.byId("billLineIdCatalog").get("value");
  dojo.xhrGet({
    url : '../tool/getSingleData.php?dataType=catalogBillLine&idCatalog='
        + idCatalog+'&csrfToken='+csrfToken,
    handleAs : "text",
    load : function(data) {
      arrayData=data.split('#!#!#!#!#!#');
      dijit.byId('billLineDescription').set('value', arrayData[0]);
      dijit.byId('billLineDetail').set('value', arrayData[1]);
      dijit.byId('billLinePrice').set('value', parseFloat(arrayData[3]));
      dijit.byId('billLineUnit').set('value', arrayData[4]);
      if (arrayData[6]) {
        dijit.byId('billLineQuantity').set('value', parseFloat(arrayData[6]));
      }
    }
  });
}

// =============================================================================
// = ExpenseDetail
// =============================================================================

function addExpenseDetail(expenseType) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  dojo.byId("expenseDetailId").value="";
  dojo.byId("idExpense").value=dojo.byId("objectId").value;
  dijit.byId("expenseDetailName").reset();
  dijit.byId("expenseDetailReference").reset();
  dijit.byId("expenseDetailDate").set('value', null);
  dijit.byId("expenseDetailType").reset();
  dojo.byId("expenseDetailDiv").innerHTML="";
  dijit.byId("expenseDetailAmount").reset();
  refreshList('idExpenseDetailType', expenseType, '1', null,
      'expenseDetailType', false);
  dijit.byId("dialogExpenseDetail").show();
}

var expenseDetailLoad=false;
function editExpenseDetail(expenseType, id, idExpense, type, expenseDate,
    amount) {
  expenseDetailLoad=true;
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  refreshList('idExpenseDetailType', expenseType, '1', null,
      'expenseDetailType', false);
  dojo.byId("expenseDetailId").value=id;
  dojo.byId("idExpense").value=idExpense;
  dijit.byId("expenseDetailName").set("value",
      dojo.byId('expenseDetail_' + id).value);
  dijit.byId("expenseDetailReference").set("value",
      dojo.byId('expenseDetailRef_' + id).value);
  dijit.byId("expenseDetailDate").set("value", getDate(expenseDate));
  dijit.byId("expenseDetailAmount").set("value", dojo.number.parse(amount));
  dijit.byId("dialogExpenseDetail").set('title',
      i18n("dialogExpenseDetail") + " #" + id);
  dijit.byId("expenseDetailType").set("value", type);
  expenseDetailLoad=false;
  expenseDetailTypeChange(id);
  expenseDetailLoad=true;
  setTimeout('expenseDetailLoad=false;', 500);
  dijit.byId("dialogExpenseDetail").show();
}

function saveExpenseDetail() {
  expenseDetailRecalculate();
  if (!dijit.byId('expenseDetailName').get('value')) {
    showAlert(i18n('messageMandatory', new Array(i18n('colName'))));
    return;
  }
  if (!dijit.byId('expenseDetailAmount').get('value')) {
    showAlert(i18n('messageMandatory', new Array(i18n('colAmount'))));
    return;
  }
  var formVar=dijit.byId('expenseDetailForm');
  if (formVar.validate()) {
    dijit.byId("expenseDetailName").focus();
    dijit.byId("expenseDetailAmount").focus();
    loadContent("../tool/saveExpenseDetail.php", "resultDivMain",
        "expenseDetailForm", true, 'expenseDetail');
    dijit.byId('dialogExpenseDetail').hide();
  } else {
    showAlert(i18n("alertInvalidForm"));
  }
}

function removeExpenseDetail(expenseDetailId) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  dojo.byId("expenseDetailId").value=expenseDetailId;
  actionOK=function() {
    loadContent("../tool/removeExpenseDetail.php", "resultDivMain",
        "expenseDetailForm", true, 'expenseDetail');
  };
  msg=i18n('confirmDeleteExpenseDetail', new Array(dojo.byId('expenseDetail_'
      + expenseDetailId).value));
  showConfirm(msg, actionOK);
}

function expenseDetailTypeChange(expenseDetailId) {
  if (expenseDetailLoad)
    return;
  var idType=dijit.byId("expenseDetailType").get("value");
  var url='../tool/expenseDetailDiv.php?idType=' + idType;
  if (expenseDetailId) {
    url+='&expenseDetailId=' + expenseDetailId;
  }
  loadContent(url, 'expenseDetailDiv', null, false);
}

function expenseDetailRecalculate() {
  val=false;
  if (!dojo.byId('expenseDetailValue01'))
    return;
  if (dijit.byId('expenseDetailValue01')) {
    val01=dijit.byId('expenseDetailValue01').get("value");
  } else {
    val01=dojo.byId('expenseDetailValue01').value;
  }
  if (dijit.byId('expenseDetailValue02')) {
    val02=dijit.byId('expenseDetailValue02').get("value");
  } else {
    val02=dojo.byId('expenseDetailValue02').value;
  }
  if (dijit.byId('expenseDetailValue03')) {
    val03=dijit.byId('expenseDetailValue03').get("value");
  } else {
    val03=dojo.byId('expenseDetailValue03').value;
  }
  total=1;
  if (dojo.byId('expenseDetailUnit01').value) {
    total=total * val01;
    val=true;
  }
  if (dojo.byId('expenseDetailUnit02').value) {
    total=total * val02;
    val=true;
  }
  if (dojo.byId('expenseDetailUnit03').value) {
    total=total * val03;
    val=true;
  }
  if (val) {
    dijit.byId("expenseDetailAmount").set('value', total);
    lockWidget("expenseDetailAmount");
  } else {
    unlockWidget("expenseDetailAmount");
  }
}

// =============================================================================
// = BillLines
// =============================================================================

function addBillLine(billingType) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  var postLoad=function() {
    var prj=dijit.byId('idProject').get('value');
    refreshListSpecific('listTermProject', 'billLineIdTerm', 'idProject', prj);
    refreshListSpecific('listResourceProject', 'billLineIdResource',
        'idProject', prj);
    refreshList('idActivityPrice', 'idProject', prj, null,
        'billLineIdActivityPrice');
    dijit.byId("dialogBillLine").set('title', i18n("dialogBillLine"));
  };
  var params="&id=";
  params+="&refType=" + dojo.byId('objectClass').value;
  params+="&refId=" + dojo.byId("objectId").value;
  if (billingType)
    params+="&billingType=" + billingType;
  loadDialog('dialogBillLine', postLoad, true, params, true);
}

function editBillLine(id, billingType) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  var params="&id=" + id;
  params+="&refType=" + dojo.byId('objectClass').value;
  params+="&refId=" + dojo.byId("objectId").value;
  if (billingType)
    params+="&billingType=" + billingType;
  loadDialog('dialogBillLine', null, true, params, true);
}

function saveBillLine() {
  if (isNaN(dijit.byId("billLineLine").getValue())) {
    dijit.byId("billLineLine").set("class", "dijitError");
    var msg=i18n('messageMandatory', new Array(i18n('BillLine')));
    new dijit.Tooltip({
      id : "billLineToolTip",
      connectId : [ "billLineLine" ],
      label : msg,
      showDelay : 0
    });
    dijit.byId("billLineLine").focus();
  } else {
    loadContent("../tool/saveBillLine.php", "resultDivMain", "billLineForm",
        true, 'billLine');
    dijit.byId('dialogBillLine').hide();
  }
}

function removeBillLine(lineId) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  actionOK=function() {
    loadContent("../tool/removeBillLine.php?billLineId=" + lineId,
        "resultDivMain", null, true, 'billLine');
  };
  msg=i18n('confirmDelete', new Array(i18n('BillLine'), lineId));
  showConfirm(msg, actionOK);
}

function billLineUpdateAmount() {
  var price=dijit.byId('billLinePrice').get('value');
  var quantity=dijit.byId('billLineQuantity').get('value');
  var amount=price * quantity;
  dijit.byId('billLineAmount').set('value', amount);
}
function billLineUpdateNumberDays() {
  if (dijit.byId('billLineUnit')
      && dijit.byId('billLineUnit').get("value") == '3') { // If unit = day
    if (dijit.byId('billLineNumberDays') && dijit.byId('billLineQuantity')
        && dijit.byId('billLineQuantity').get("value") > 0) {
      dijit.byId('billLineNumberDays').set("value",
          dijit.byId('billLineQuantity').get("value"));
    }
  }
}

// =============================================================================
// = Resource Cost
// =============================================================================

function addResourceCost(idResource, idRole, funcList) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  var callBack=function() {
    affectationLoad=true;
    dojo.byId("resourceCostId").value="";
    dojo.byId("resourceCostIdResource").value=idResource;
    dojo.byId("resourceCostFunctionList").value=funcList;
    dijit.byId("resourceCostIdRole").set('readOnly', false);
    if (idRole) {
      dijit.byId("resourceCostIdRole").set('value', idRole);
    } else {
      dijit.byId("resourceCostIdRole").reset();
    }
    dijit.byId("resourceCostValue").reset('value');
    dijit.byId("resourceCostStartDate").set('value', null);
    resourceCostUpdateRole();
    dijit.byId("dialogResourceCost").show();
    setTimeout("affectationLoad=false", 500);
  };
  var params="&idResource=" + idResource;
  params+="&funcList=" + funcList;
  params+="&idRole=" + idRole;
  params+="&mode=add";
  loadDialog('dialogResourceCost', callBack, true, params);
}

function removeResourceCost(id, idRole, nameRole, startDate) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  var params="&idResource=" + dijit.byId('id').get("value");
  params+="&funcList=";
  params+="&idRole=" + idRole;
  params+="&mode=delete";
  var callBack=function() {
    dojo.byId("resourceCostId").value=id;
  }
  loadDialog('dialogResourceCost', callBack, false, params, false);
  actionOK=function() {

    loadContent("../tool/removeResourceCost.php", "resultDivMain",
        "resourceCostForm", true, 'resourceCost');
  };
  msg=i18n('confirmDeleteResourceCost', new Array(nameRole, startDate));
  showConfirm(msg, actionOK);
}

reourceCostLoad=false;
function editResourceCost(id, idResource, idRole, cost, startDate, endDate) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  var callBack=function() {
    dojo.byId("resourceCostId").value=id;
    dojo.byId("resourceCostIdResource").value=idResource;
    dijit.byId("resourceCostIdRole").set('readOnly', true);
    dijit.byId("resourceCostValue")
        .set('value', dojo.number.format(cost / 100));
    var dateStartDate=getDate(startDate);
    dijit.byId("resourceCostStartDate").set('value', dateStartDate);
    dijit.byId("resourceCostStartDate").set('disabled', true);
    dijit.byId("resourceCostStartDate").set('required', 'false');
    reourceCostLoad=true;
    dijit.byId("resourceCostIdRole").set('value', idRole);
    setTimeout('reourceCostLoad=false;', 300);
    dijit.byId("dialogResourceCost").show();
  };
  loadDialog('dialogResourceCost', callBack, true, null);
}

function saveResourceCost() {
  var formVar=dijit.byId('resourceCostForm');
  if (formVar.validate()) {
    loadContent("../tool/saveResourceCost.php", "resultDivMain",
        "resourceCostForm", true, 'resourceCost');
    dijit.byId('dialogResourceCost').hide();
  } else {
    showAlert(i18n("alertInvalidForm"));
  }
}

function resourceCostUpdateRole() {
  if (reourceCostLoad) {
    return;
  }
  if (dijit.byId("resourceCostIdRole").get('value')) {
    dojo.xhrGet({
      url : '../tool/getSingleData.php?dataType=resourceCostDefault&idRole='
          + dijit.byId("resourceCostIdRole").get('value')+'&csrfToken='+csrfToken,
      handleAs : "text",
      load : function(data) {
        dijit.byId('resourceCostValue').set('value', dojo.number.format(data));
      }
    });
  }
  var funcList=dojo.byId('resourceCostFunctionList').value;
  $key='#' + dijit.byId("resourceCostIdRole").get('value') + '#';
  if (funcList.indexOf($key) >= 0) {
    dijit.byId("resourceCostStartDate").set('disabled', false);
    dijit.byId("resourceCostStartDate").set('required', 'true');
  } else {
    dijit.byId("resourceCostStartDate").set('disabled', true);
    dijit.byId("resourceCostStartDate").set('value', null);
    dijit.byId("resourceCostStartDate").set('required', 'false');
  }
}

function saveComplexity(id, idZone) {
  var value=dijit.byId("complexity" + idZone).get("value");
  var url='../tool/saveComplexity.php?idCatalog=' + id + '&name=' + value
      + '&idZone=' + idZone+'&csrfToken='+csrfToken;
  dojo.xhrPut({
    url : url,
    form : 'objectForm',
    handleAs : "text",
    load : function(data) {
      if (data) {
        dijit.byId("complexity" + idZone).set("value", data);
        showAlert(i18n("cantDeleteUsingUOComplexity"));
      } else {
        loadContent("objectDetail.php?refreshComplexitiesValues=true",
            "CatalogUO_unitOfWork", 'listForm');
      }
    }
  });
}

// =============================================================================
// = Add-Edit-Remove an organization's Budget Element
// =============================================================================

function addBudgetElement(objectClassName, refId, id, year, scope) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  params='&objectClass=' + objectClassName;
  params+='&action=ADD';
  params+='&refId=' + refId;
  params+='&id=' + id;
  params+='&year=' + year;
  params+='&scope=' + scope;
  loadDialog('dialogAddChangeBudgetElement', null, true, params, true, true,
      'addBudgetElement');
}

function changeBudgetElement(objectClassName, refId, id, year, budgetWork,
    budgetCost, budgetExpenseAmount) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  params='&objectClass=' + objectClassName;
  params+='&action=CHANGE';
  params+='&refId=' + refId;
  params+='&id=' + id;
  params+='&year=' + year;
  params+='&budgetWork=' + budgetWork;
  params+='&budgetCost=' + budgetCost;
  params+='&budgetExpenseAmount=' + budgetExpenseAmount;

  loadDialog('dialogAddChangeBudgetElement', null, true, params, false, true,
      'changeBudgetElement');
}

function saveOrganizationBudgetElement() {
  loadContent("../tool/saveOrganizationBudgetElement.php", "resultDivMain",
      "addChangeBudgetElementForm", true);
  dijit.byId('dialogAddChangeBudgetElement').hide();
  showWait();
}

function closeUncloseBudgetElement(objectClassName, refId, id, idle, year) {
  var param="?objectClassName=" + objectClassName;
  param+="&refId=" + refId;
  param+="&budgetElementId=" + id;
  param+="&idle=" + idle;
  param+="&year=" + year;
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  if (idle == 0) {
    msg=i18n('confirmCloseBudgetElement');
  } else {
    msg=i18n('confirmUncloseBudgetElement');
  }
  actionOK=function() {
    loadContent("../tool/closeUncloseOrganizationBudgetElement.php" + param,
        "detailDiv", "");
  };

  showConfirm(msg, actionOK);
}

function removeBudgetElement(objectClassName, refId, id, year) {
  var param="?objectClassName=" + objectClassName;
  param+="&refId=" + refId;
  param+="&budgetElementId=" + id;
  param+="&year=" + year;

  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }

  actionOK=function() {
    loadContent("../tool/removeOrganizationBudgetElement.php" + param,
        "detailDiv", "");
  };

  msg=i18n('confirmRemoveBudgetElement');
  showConfirm(msg, actionOK);

}

// =============================================================================
// = Financial
// =============================================================================

function editProviderTerm(objectClass, idProviderOrder, isLine, id, name, date,
    tax, discount, untaxed, taxAmount, fullAmount, totalUntaxed) {
  affectationLoad=true;
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  // var percent = Math.round(untaxed*100000/totalUntaxed)/1000;
  var percent=untaxed * 100 / totalUntaxed;
  var callBack=function() {
    if (name) {
      dijit.byId("providerTermName").set('value', name);
    }
    if (date) {
      dijit.byId("providerTermDate").set('value', date);
    }
    if (tax) {
      dijit.byId("providerTermTax").set('value', tax);
    }
    if (discount) {
      dijit.byId("providerTermDiscount").set('value', discount);
    }
    if (isLine == 'false') {
      dijit.byId("providerTermPercent").set('value', percent);

      if (untaxed) {
        dijit.byId("providerTermUntaxedAmount").set('value', untaxed);
      }
      if (taxAmount) {
        dijit.byId("providerTermTaxAmount").set('value', taxAmount);
      }
      if (fullAmount) {
        dijit.byId("providerTermFullAmount").set('value', fullAmount);
      }
    }
    dijit.byId("dialogProviderTerm").show();
    setTimeout("affectationLoad=false", 500);
  };
  var params="&objectClass=" + objectClass;
  params+="&id=" + id;
  params+="&idProviderOrderEdit=" + idProviderOrder;
  params+="&isLineMulti=" + isLine;
  params+="&mode=edit";
  loadDialog('dialogProviderTerm', callBack, false, params);
}

function removeProviderTerm(id, fromBill) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  actionOK=function() {
    var url="../tool/removeProviderTerm.php?providerTermId=" + id;
    if (fromBill)
      url+="&fromBill=true";
    loadContent(url, "resultDivMain", null, true, 'providerTerm');
  };
  msg=i18n('confirmDeleteProviderTerm', new Array(id));
  showConfirm(msg, actionOK);
}

function removeProviderTermFromBill(id, idProviderBill) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  actionOK=function() {
    loadContent("../tool/removeProviderTerm.php?providerTermId=" + id
        + "&isProviderBill=true", "resultDivMain", null, true, 'providerTerm');
  };
  msg=i18n('confirmRemoveProviderTermFromBill', new Array(id));
  showConfirm(msg, actionOK);
}

function addWorkCommand(id) {
  var callBack=function() {
    affectationLoad=true;
    dijit.byId("dialogWorkCommand").show();
    setTimeout("affectationLoad=false", 500);
  };
  var params="&idCommand=" + id;
  params+="&mode=add";
  loadDialog('dialogWorkCommand', callBack, false, params);
}

function editWorkCommand(idCommand, id, idWorkUnit, idComplexity, quantity,
    unitAmount, commandAmount) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  pauseBodyFocus();
  var callBack=function() {
    dijit.byId("dialogWorkCommand").show();
  };
  var params="&id=" + id;
  params+="&idCommand=" + idCommand;
  params+="&idWorkUnit=" + idWorkUnit;
  params+="&idComplexity=" + idComplexity;
  params+="&quantity=" + quantity;
  params+="&unitAmount=" + unitAmount;
  params+="&commandAmount=" + commandAmount;
  params+="&mode=edit";
  loadDialog('dialogWorkCommand', callBack, false, params);
}

function editBilledWorkCommand(idBill, id, idWorkCommand, quantity) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  pauseBodyFocus();
  var callBack=function() {
    dijit.byId("dialogBilledWorkCommand").show();
  };
  var params="&id=" + id;
  params+="&idBill=" + idBill;
  params+="&idWorkCommand=" + idWorkCommand;
  params+="&quantity=" + quantity;
  params+="&mode=edit";
  loadDialog('dialogBilledWorkCommand', callBack, false, params);
}

function addBilledWorkCommand(id) {
  var callBack=function() {
    affectationLoad=true;
    dijit.byId("dialogBilledWorkCommand").show();
    setTimeout("affectationLoad=false", 500);
  };
  var params="&idBill=" + id;
  params+="&mode=add";
  loadDialog('dialogBilledWorkCommand', callBack, false, params);
}

function saveWorkCommand() {
  var formVar=dijit.byId('workCommandForm');
  if (formVar.validate()) {
    loadContent("../tool/saveWorkCommand.php", "resultDivMain",
        "workCommandForm", true, 'workCommand');
    dijit.byId('dialogWorkCommand').hide();
  } else {
    showAlert(i18n("alertInvalidForm"));
  }
}

function saveBilledWorkCommand() {
  var formVar=dijit.byId('billedWorkCommandForm');
  if (dijit.byId('billedWorkCommandBilled').get('value') > dijit.byId(
      'billedWorkCommandCommand').get('value')) {
    showAlert(i18n("billedQuantityCantBeSuperiorThanCommand"));
  } else {
    if (formVar.validate()) {
      loadContent("../tool/saveBilledWorkCommand.php", "resultDivMain",
          "billedWorkCommandForm", true, 'billedWorkCommand');
      dijit.byId('dialogBilledWorkCommand').hide();
    } else {
      showAlert(i18n("alertInvalidForm"));
    }
  }
}

function removeWorkCommand(id) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  actionOK=function() {
    loadContent("../tool/removeWorkCommand.php?idWorkCommand=" + id,
        "resultDivMain", null, true, 'workCommand');
  };
  msg=i18n('confirmRemoveWorkCommand', new Array(id));
  showConfirm(msg, actionOK);
}

function removeBilledWorkCommand(idWorkCommandBilled, idWorkCommand) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  actionOK=function() {
    loadContent("../tool/removeBilledWorkCommand.php?idWorkCommandBilled="
        + idWorkCommandBilled + '&idWorkCommand=' + idWorkCommand,
        "resultDivMain", null, true, 'workCommand');
  };
  msg=i18n('confirmRemoveWorkCommand', new Array(id));
  showConfirm(msg, actionOK);
}

function activityWorkUnitChangeIdWorkUnit() {
  if (dijit.byId('ActivityWorkCommandWorkUnit').get('value') == ''
      || dijit.byId('ActivityWorkCommandWorkUnit').get('value') == ' ') {
    dijit.byId('ActivityWorkCommandComplexity').set('value', '');
    dijit.byId("ActivityWorkCommandComplexity").set('readOnly', true);
    dijit.byId('ActivityBilledWorkCommandWorkCommand').set('value', '');
    dijit.byId("ActivityBilledWorkCommandWorkCommand").set('readOnly', true);
  } else {
    dijit.byId("ActivityWorkCommandComplexity").set('readOnly', false);
    if (dijit.byId('ActivityWorkCommandComplexity').get('value') != '') {
      dijit.byId('ActivityWorkCommandComplexity').set('value', '');
    }
    refreshListSpecific("idWorkUnit", "ActivityWorkCommandComplexity",
        "idWorkUnit", dijit.byId('ActivityWorkCommandWorkUnit').get('value'));
    dijit.byId('ActivityBilledWorkCommandWorkCommand').set('value', '');
    dijit.byId("ActivityBilledWorkCommandWorkCommand").set('readOnly', true);
  }
}

function activityWorkUnitChangeIdComplexity() {
  dijit.byId("ActivityWorkCommandAmount").set('value', '');
  dijit.byId("ActivityWorkCommandQuantity").set('readOnly', false);
  if (dijit.byId("ActivityBilledWorkCommandWorkCommand")) {
    var idComplexity=dijit.byId("ActivityWorkCommandComplexity").get("value");
    var idWorkUnit=dijit.byId("ActivityWorkCommandWorkUnit").get("value");
    dijit.byId("ActivityBilledWorkCommandWorkCommand").set("value", "");
    if (idComplexity != " " && idComplexity != "") {
      refreshListSpecific("idWorkCommand",
          "ActivityBilledWorkCommandWorkCommand", "idWorkCommand", idWorkUnit
              + "separator" + idComplexity + "separator"
              + dojo.byId("id").value);
      dijit.byId("ActivityBilledWorkCommandWorkCommand").set("readOnly", false);
    } else {
      dijit.byId("ActivityBilledWorkCommandWorkCommand").set("readOnly", true);
    }
  }

}

function activityWorkUnitChangeQuantity() {
  unit=dijit.byId('ActivityWorkCommandQuantity').get('value');
  dojo.xhrGet({
    url : '../tool/getSingleData.php?dataType=workCommand' + '&idWorkUnit='
        + dijit.byId('ActivityWorkCommandWorkUnit').get('value')
        + '&idComplexity='
        + dijit.byId('ActivityWorkCommandComplexity').get('value')+'&csrfToken='+csrfToken,
    handleAs : "text",
    load : function(data) {
      total=data * unit;
      dijit.byId('ActivityWorkCommandAmount').set('value', total);
    }
  });
}

function workCommandChangeIdWorkUnit() {
  if (dijit.byId('workCommandWorkUnit').get('value') == ''
      || dijit.byId('workCommandWorkUnit').get('value') == ' ') {
    dijit.byId('workCommandComplexity').set('value', '');
    dijit.byId("workCommandComplexity").set('readOnly', true);
  } else {
    dijit.byId("workCommandComplexity").set('readOnly', false);
    if (dijit.byId('workCommandComplexity').get('value') != '') {
      dijit.byId('workCommandComplexity').set('value', '');
    }
    refreshListSpecific("idWorkUnit", "workCommandComplexity", "idWorkUnit",
        dijit.byId('workCommandWorkUnit').get('value'));
  }
}

function workCommandChangeIdComplexity() {
  dijit.byId("workCommandQuantity").set('value', '');
  dijit.byId("workCommandAmount").set('value', '');
  dijit.byId("workCommandQuantity").set('readOnly', false);
  dojo.xhrGet({
    url : '../tool/getSingleData.php?dataType=workCommand' + '&idWorkUnit='
        + dijit.byId('workCommandWorkUnit').get('value') + '&idComplexity='
        + dijit.byId('workCommandComplexity').get('value')+'&csrfToken='+csrfToken,
    handleAs : "text",
    load : function(data) {
      dijit.byId('workCommandUnitAmount').set('value', data);
    }
  });
}

function changeBilledWorkCommand() {
  dijit.byId("billedWorkCommandQuantityBilled").set('readOnly', false);
  dojo.xhrGet({
    url : '../tool/getSingleData.php?dataType=billedWorkCommand'
        + '&idWorkCommand='
        + dijit.byId('billedWorkCommandWorkCommand').get('value')+'&csrfToken='+csrfToken,
    handleAs : "text",
    load : function(data) {
      arrayData=data.split('#!#!#!#!#!#');
      dijit.byId('billedWorkCommandWorkUnit').set('value', arrayData[0]);
      dijit.byId('billedWorkCommandComplexity').set('value', arrayData[1]);
      dijit.byId('billedWorkCommandUnitAmount').set('value',
          parseFloat(arrayData[2]));
      dijit.byId('billedWorkCommandCommand').set('value',
          parseFloat(arrayData[3]));
      dijit.byId('billedWorkCommandDone')
          .set('value', parseFloat(arrayData[4]));
      dijit.byId('billedWorkCommandBilled').set('value',
          parseFloat(arrayData[5]));
    }
  });
}

function billedWorkCommandChangeQuantity(mode, id) {
  var total=dijit.byId('billedWorkCommandUnitAmount').get('value')
      * dijit.byId('billedWorkCommandQuantityBilled').get('value');
  dijit.byId('billedWorkCommandAmount').set('value', total);

  if (mode == 'add') {
    dojo
        .xhrGet({
          url : '../tool/getSingleData.php?dataType=billedWorkCommandQuantityAdd'
              + '&idWorkCommand='
              + dijit.byId('billedWorkCommandWorkCommand').get('value')+'&csrfToken='+csrfToken,
          handleAs : "text",
          load : function(data) {
            var quantity=dijit.byId('billedWorkCommandQuantityBilled').get(
                'value');
            var totalQuantityBilled=parseInt(data) + quantity;
            dijit.byId('billedWorkCommandBilled').set('value',
                totalQuantityBilled);
          }
        });
  } else {
    dojo
        .xhrGet({
          url : '../tool/getSingleData.php?dataType=billedWorkCommandQuantityEdit'
              + '&idWorkCommandBill='
              + id
              + '&idWorkCommand='
              + dijit.byId('billedWorkCommandWorkCommand').get('value')+'&csrfToken='+csrfToken,
          handleAs : "text",
          load : function(data) {
            var quantity=dijit.byId('billedWorkCommandQuantityBilled').get(
                'value');
            var totalQuantityBilled=parseInt(data) + quantity;
            dijit.byId('billedWorkCommandBilled').set('value',
                totalQuantityBilled);
          }
        });
  }

}

function workCommandChangeQuantity() {
  var quantity=dijit.byId('workCommandQuantity').get('value');
  var amountUnity=dijit.byId('workCommandUnitAmount').get('value');
  var amount=quantity * amountUnity;
  dijit.byId('workCommandAmount').set('value', amount);
}

function addProviderTerm(objectClass, type, idProviderOrder, isLine) {
  var callBack=function() {
    affectationLoad=true;
    dijit.byId("dialogProviderTerm").show();
    setTimeout("affectationLoad=false", 500);
  };
  var params="&idProviderOrder=" + idProviderOrder;
  params+="&type=" + type;
  params+="&isLine=" + isLine;
  params+="&mode=add";
  params+="&objectClass=" + objectClass;
  loadDialog('dialogProviderTerm', callBack, false, params);
}

function saveProviderTerm() {
  var formVar=dijit.byId('providerTermForm');
  if (formVar.validate()) {
    loadContent("../tool/saveProviderTerm.php", "resultDivMain",
        "providerTermForm", true, 'providerTerm');
    dijit.byId('dialogProviderTerm').hide();
  } else {
    showAlert(i18n("alertInvalidForm"));
  }
}

var cancelRecursiveChange_OnGoingChange=false;
function providerTermLine(totalUntaxedAmount) {
  if (cancelRecursiveChange_OnGoingChange)
    return;
  cancelRecursiveChange_OnGoingChange=true;
  var totalUntaxedAmountValue=totalUntaxedAmount;
  var untaxedAmount=dijit.byId("providerTermUntaxedAmount").get("value");
  if (!untaxedAmount)
    untaxedAmount=0;
  var taxPct=dijit.byId("providerTermTax").get("value");
  if (!taxPct)
    taxPct=0;
  var taxAmount=Math.round(untaxedAmount * taxPct) / 100;
  var fullAmount=taxAmount + untaxedAmount;
  var percent=untaxedAmount * 100 / totalUntaxedAmountValue;
  dijit.byId("providerTermPercent").set('value', percent);
  dijit.byId("providerTermTaxAmount").set('value', taxAmount);
  dijit.byId("providerTermFullAmount").set('value', fullAmount);
  setTimeout("cancelRecursiveChange_OnGoingChange = false;", 50);
}
function providerTermLinePercent(totalUntaxedAmount) {
  if (cancelRecursiveChange_OnGoingChange)
    return;
  cancelRecursiveChange_OnGoingChange=true;
  var totalUntaxedAmountValue=totalUntaxedAmount;
  var percent=dijit.byId("providerTermPercent").get("value");
  var taxPct=dijit.byId("providerTermTax").get("value");
  if (!taxPct)
    taxPct=0;
  var untaxedAmount=percent * totalUntaxedAmountValue / 100;
  var taxAmount=Math.round(untaxedAmount * taxPct) / 100;
  var fullAmount=taxAmount + untaxedAmount;
  dijit.byId("providerTermUntaxedAmount").set('value', untaxedAmount);
  dijit.byId("providerTermTaxAmount").set('value', taxAmount);
  dijit.byId("providerTermFullAmount").set('value', fullAmount);
  setTimeout("cancelRecursiveChange_OnGoingChange = false;", 50);
}

function providerTermLineBillLine(id) {
  if (cancelRecursiveChange_OnGoingChange)
    return;
  cancelRecursiveChange_OnGoingChange=true;
  var totalUntaxedAmountValue=dijit.byId("providerTermBillLineUntaxed" + id)
      .get("value");
  var untaxedAmount=dijit.byId("providerTermUntaxedAmount" + id).get("value");
  var discount=dijit.byId("providerTermDiscount").get("value");
  if (!untaxedAmount)
    untaxedAmount=0;
  var taxPct=dijit.byId("providerTermTax").get("value");
  if (!taxPct)
    taxPct=0;
  var discountBill=(untaxedAmount * discount / 100);
  var taxAmount=Math.round((untaxedAmount - discountBill) * taxPct) / 100;
  var fullAmount=untaxedAmount - discountBill + taxAmount;
  var percent=untaxedAmount * 100 / totalUntaxedAmountValue;
  dijit.byId("providerTermDiscountAmount" + id).set('value', discountBill);
  dijit.byId("providerTermPercent" + id).set('value', percent);
  dijit.byId("providerTermTaxAmount" + id).set('value', taxAmount);
  dijit.byId("providerTermFullAmount" + id).set('value', fullAmount);
  setTimeout("cancelRecursiveChange_OnGoingChange = false;", 50);
}

function providerTermLinePercentBilleLine(id) {
  if (cancelRecursiveChange_OnGoingChange)
    return;
  cancelRecursiveChange_OnGoingChange=true;
  var totalUntaxedAmountValue=dijit.byId("providerTermBillLineUntaxed" + id)
      .get("value");
  var percent=dijit.byId("providerTermPercent" + id).get("value");
  var taxPct=dijit.byId("providerTermTax").get("value");
  var discount=dijit.byId("providerTermDiscount").get("value");
  if (!taxPct)
    taxPct=0;
  var untaxedAmount=percent * totalUntaxedAmountValue / 100;
  var discountBill=(untaxedAmount * discount / 100);
  var taxAmount=Math.round((untaxedAmount - discountBill) * taxPct) / 100;
  var fullAmount=untaxedAmount - discountBill + taxAmount;
  dijit.byId("providerTermUntaxedAmount" + id).set('value', untaxedAmount);
  dijit.byId("providerTermDiscountAmount" + id).set('value', discountBill);
  dijit.byId("providerTermTaxAmount" + id).set('value', taxAmount);
  dijit.byId("providerTermFullAmount" + id).set('value', fullAmount);
  setTimeout("cancelRecursiveChange_OnGoingChange = false;", 50);
}

function addProviderTermFromProviderBill() {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  var params="&providerBillId=" + dijit.byId('id').get('value');
  loadDialog('dialogProviderTermFromProviderBill', null, true, params);
}

function saveProviderTermFromProviderBill() {
  var formVar=dijit.byId('providerTermFromProviderBillForm');
  if (formVar.validate() && dojo.byId('linkProviderTerm')
      && dojo.byId('linkProviderTerm').value) {
    loadContent("../tool/saveProviderTermFromProviderBill.php",
        "resultDivMain", "providerTermFromProviderBillForm", true,
        'ProviderTerm');
    dijit.byId('dialogProviderTermFromProviderBill').hide();
  } else {
    showAlert(i18n("alertInvalidForm"));
  }
}

function providerTermLineChangeNumber() {
  if (!dijit.byId('providerTermNumberOfTerms'))
    return;
  var number=dijit.byId('providerTermNumberOfTerms').get("value");
  if (!number || number <= 0)
    return;
  if (number > 1) {
    cancelRecursiveChange_OnGoingChange=true;
    dijit.byId('providerTermUntaxedAmount').set('value',
        dijit.byId('providerTermOrderUntaxedAmount').get('value') / number);
    dijit.byId('providerTermPercent').set('value', 100 / number);
    dijit.byId('providerTermFullAmount').set('value',
        dijit.byId('providerTermOrderFullAmount').get('value') / number);
    dijit.byId('providerTermTaxAmount').set(
        'value',
        dijit.byId('providerTermFullAmount').get('value')
            - dijit.byId('providerTermUntaxedAmount').get('value'));
    lockWidget('providerTermPercent');
    lockWidget('providerTermUntaxedAmount');
    var termDate=dijit.byId('providerTermDate').get('value');
    if (!termDate) {
      dojo.byId('labelRegularTerms').innerHTML='<br/>'
          + '<span style="color:red">'
          + i18n('messageMandatory', new Array(i18n('colDate'))) + '</span>';
    } else {
      var termDay=termDate.getDate();
      var lastDayOfMonth=(new Date(termDate.getFullYear(),
          termDate.getMonth() + 1, 0)).getDate();
      if (termDay == lastDayOfMonth) {
        termDay=i18n('colLastDay');
      }
      var startDate=dateFormatter(formatDate(termDate));
      dojo.byId('labelRegularTerms').innerHTML='<br/>'
          + i18n('labelRegularTerms', new Array(number, termDay, startDate));
    }
    setTimeout("cancelRecursiveChange_OnGoingChange=false;", 50);
  } else {
    unlockWidget('providerTermPercent');
    unlockWidget('providerTermUntaxedAmount');
    dojo.byId('labelRegularTerms').innerHTML="";
  }
}
function refreshLinkProviderTerm(selected) {
  var url='../tool/dynamicListLinkProviderTerm.php';
  if (selected) {
    url+='?selected=' + selected;
    if (dojo.byId("ProviderBillId")) {
      url+="&providerBillId=" + dojo.byId("ProviderBillId").value;
    }
    var callback=function() {
      dojo.byId('linkProviderTerm').focus();
    };
    loadDiv(url, 'linkProviderTermDiv', null, callback);
  }
}

function addWorkUnit(idCatalogUO) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  pauseBodyFocus();
  var callBack=function() {
    ckEditorReplaceEditor("WUDescriptions", 992);
    ckEditorReplaceEditor("WUIncomings", 993);
    ckEditorReplaceEditor("WULivrables", 994);
    dijit.byId("dialogWorkUnit").show();
  };
  var params="&idCatalog=" + idCatalogUO;
  params+="&mode=add";
  loadDialog('dialogWorkUnit', callBack, false, params);
}

function removeWorkUnit(idWorkUnit) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  actionOK=function() {
    loadContent("../tool/removeWorkUnit.php?idWorkUnit=" + idWorkUnit,
        "resultDivMain", null, true, 'affectation');
  };
  msg=i18n('confirmDeleteWorkUnit', new Array(id, i18n('WorkUnit'), idWorkUnit));
  showConfirm(msg, actionOK);
}

function addActivityWorkUnit(id,visibility) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  var callBack=function() {
    affectationLoad=true;
    dijit.byId("dialogActivityWorkUnit").show();
    setTimeout("affectationLoad=false", 500);
  };
  var params="&id=" + id;
  params+="&mode=add";
  params+="&visibility="+visibility;
  loadDialog('dialogActivityWorkUnit', callBack, false, params);
}

function removeActivityWorkUnit(idWorkUnit) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  actionOK=function() {
    loadContent("../tool/removeActivityWorkUnit.php?idWorkUnit=" + idWorkUnit,
        "resultDivMain", null, true, 'affectation');
  };
  msg=i18n('confirmDeleteWorkUnit', new Array(id, i18n('WorkUnit'), idWorkUnit));
  showConfirm(msg, actionOK);
}

function saveActivityWorkUnit() {
  if (trim(dijit.byId('ActivityWorkCommandComplexity').get("value")) == "") {
    showAlert(i18n("ActivityWorkCommandComplexityIsMissing"));
    return;
  }
  var formVar=dijit.byId('activityWorkUnitForm');
  if (formVar.validate()) {
    var idWorkUnit=dijit.byId("ActivityWorkCommandWorkUnit").get("value");
    var today=(new Date()).toISOString().substr(0, 10);
    dojo.xhrGet({
      url : '../tool/getSingleData.php?dataType=validityDate&idWorkUnit='
          + idWorkUnit+'&csrfToken='+csrfToken,
      handleAs : "text",
      load : function(data) {
        if (data) {
          if (data < today) {
            actionOK=function() {
              loadContent("../tool/saveActivityWorkUnit.php", "resultDivMain",
                  "activityWorkUnitForm", true, 'workUnit');
              dijit.byId('dialogActivityWorkUnit').hide();
            };
            msg=i18n('errorValidityDate');
            showConfirm(msg, actionOK);
          } else {
            loadContent("../tool/saveActivityWorkUnit.php", "resultDivMain",
                "activityWorkUnitForm", true, 'workUnit');
            dijit.byId('dialogActivityWorkUnit').hide();
          }
        } else {
          loadContent("../tool/saveActivityWorkUnit.php", "resultDivMain",
              "activityWorkUnitForm", true, 'workUnit');
          dijit.byId('dialogActivityWorkUnit').hide();
        }
      }
    });
  } else {
    showAlert(i18n("alertInvalidForm"));
  }
}

function saveWorkUnit() {
  editorDescriptions=CKEDITOR.instances['WUDescriptions'];
  editorDescriptions.updateElement();
  editorWUIncomings=CKEDITOR.instances['WUIncomings'];
  editorWUIncomings.updateElement();
  editorWULivrables=CKEDITOR.instances['WULivrables'];
  editorWULivrables.updateElement();
  if (trim(dijit.byId('WUReferences').get("value")) == "") {
    showAlert(i18n("referenceIsMissing"));
    return;
  }
  var formVar=dijit.byId('workUnitForm');
  if (formVar.validate()) {
    loadContent("../tool/saveWorkUnit.php", "resultDivMain", "workUnitForm",
        true, 'WorkUnit');
    dijit.byId('dialogWorkUnit').hide();
    loadContent("objectDetail.php?refreshComplexitiesValues=true",
        "CatalogUO_unitOfWork", 'listForm');
  } else {
    showAlert(i18n("alertInvalidForm"));
  }
}

function editActivityWorkUnit(idActivityWorkUnit, id,visibility) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  pauseBodyFocus();
  var callBack=function() {
    dijit.byId("dialogActivityWorkUnit").show();
  };
  var params="&id=" + id;
  params+="&idActivityWorkUnit=" + idActivityWorkUnit;
  params+="&mode=edit";
  params+="&visibility="+visibility;
  loadDialog('dialogActivityWorkUnit', callBack, false, params);
}

function editWorkUnit(id, idCatalogUO, validityDate, idle) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  pauseBodyFocus();
  var callBack=function() {
    ckEditorReplaceEditor("WUDescriptions", 992);
    ckEditorReplaceEditor("WUIncomings", 993);
    ckEditorReplaceEditor("WULivrables", 994);
    if (validityDate) {
      dijit.byId("ValidityDateWU").set('value', validityDate);
    } else {
      dijit.byId("ValidityDateWU").reset();
    }
    if (idle == 1) {
      dijit.byId("idleWU").set('value', idle);
    } else {
      dijit.byId("idleWU").reset();
    }
    dijit.byId("dialogWorkUnit").show();
  };
  var params="&id=" + id;
  params+="&idCatalog=" + idCatalogUO;
  params+="&mode=edit";
  loadDialog('dialogWorkUnit', callBack, false, params);
}

function moveBudgetFromHierarchicalView(idFrom, idTo) {
  var mode='before';
  dndSourceTableBudget.sync();
  var nodeList=dndSourceTableBudget.getAllNodes();
  for (i=0; i < nodeList.length; i++) {
    if (nodeList[i].id == idFrom) {
      mode='before';
      break;
    } else if (nodeList[i].id == idTo) {
      mode='after';
      break;
    }
  }
  var url='../tool/moveBudgetFromHierarchicalView.php?idFrom=' + idFrom
      + '&idTo=' + idTo + '&mode=' + mode+'&csrfToken='+csrfToken;
  dojo.xhrPost({
    url : url,
    handleAs : "text",
    load : function() {
      refreshHierarchicalBudgetList();
    }
  });
}

function addTenderEvaluationCriteria(callForTenderId) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  var params="&mode=add&callForTenderId=" + callForTenderId;
  loadDialog('dialogCallForTenderCriteria', null, true, params, false);
}

function editTenderEvaluationCriteria(criteriaId) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  var params="&mode=edit&criteriaId=" + criteriaId;
  loadDialog('dialogCallForTenderCriteria', null, true, params, false);
}

function saveTenderEvaluationCriteria() {
  var formVar=dijit.byId("dialogTenderCriteriaForm");
  if (!formVar) {
    showError(i18n("errorSubmitForm", new Array("n/a", "n/a",
        "dialogTenderCriteriaForm")));
    return;
  }
  if (formVar.validate()) {
    loadContent("../tool/saveTenderEvaluationCriteria.php", "resultDivMain",
        "dialogTenderCriteriaForm", true, 'tenderEvaluationCriteria');
    dijit.byId('dialogCallForTenderCriteria').hide();
  }
}

function removeTenderEvaluationCriteria(criteriaId) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  actionOK=function() {
    loadContent("../tool/removeTenderEvaluationCriteria.php?criteriaId="
        + criteriaId, "resultDivMain", null, true, 'tenderEvaluationCriteria');
  };
  msg=i18n('confirmDelete', new Array(i18n('TenderEvaluationCriteria'),
      criteriaId));
  showConfirm(msg, actionOK);
}

function addTenderSubmission(callForTenderId) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  var params="&mode=add&callForTenderId=" + callForTenderId;
  loadDialog('dialogCallForTenderSubmission', null, true, params, false);
}

function editTenderSubmission(tenderId) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  var params="&mode=edit&tenderId=" + tenderId;
  loadDialog('dialogCallForTenderSubmission', null, true, params, false);
}

function saveTenderSubmission() {
  var formVar=dijit.byId("dialogTenderSubmissionForm");
  if (dijit.byId('dialogCallForTenderSubmissionProvider')
      && !trim(dijit.byId('dialogCallForTenderSubmissionProvider').get("value"))) {
    showAlert(i18n('messageMandatory', new Array(i18n('colIdProvider'))));
    return;
  }
  if (!formVar) {
    showAlert(i18n("errorSubmitForm", new Array("n/a", "n/a",
        "dialogTenderSubmissionForm")));
    return;
  }
  if (formVar.validate()) {
    loadContent("../tool/saveTenderSubmission.php", "resultDivMain",
        "dialogTenderSubmissionForm", true, 'tenderSubmission');
    dijit.byId('dialogCallForTenderSubmission').hide();
  }
}

function removeTenderSubmission(tenderId) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  actionOK=function() {
    loadContent("../tool/removeTenderSubmission.php?tenderId=" + tenderId,
        "resultDivMain", null, true, 'tenderSubmission');
  };
  msg=i18n('confirmDelete', new Array(i18n('Tender'), tenderId)) + '<br/><b>'
      + i18n('messageAlerteDeleteTender') + '</b>';
  showConfirm(msg, actionOK);
}

function changeTenderEvaluationValue(index) {
  var value=dijit.byId("tenderEvaluation_" + index).get("value");
  var coef=dojo.byId("tenderCoef_" + index).value;
  var total=value * coef;
  dijit.byId("tenderTotal_" + index).set("value", total);
  var list=dojo.byId('idTenderCriteriaList').value.split(';');
  var sum=0;
  for (var i=0; i < list.length; i++) {
    sum+=dijit.byId('tenderTotal_' + list[i]).get('value');
  }
  dijit.byId("tenderTotal").set("value", sum);
  var newValue=Math.round(sum * dojo.byId('evaluationMaxCriteriaValue').value
      / dojo.byId('evaluationSumCriteriaValue').value * 100) / 100;
  dijit.byId("evaluationValue").set("value", newValue);
}

function addWorkTokenMarkup(idToken) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  pauseBodyFocus();
  var callBack=function() {
    dijit.byId("dialogWorkTokenMarkup").show();
  };
  var params="&idToken=" + idToken;
  params+="&mode=add";
  loadDialog('dialogWorkTokenMarkup', callBack, false, params);
}

function addTokenClientContract(idClientContract,idProject) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  pauseBodyFocus();
  var callBack=function() {
    dijit.byId("dialogWorkTokenClientContract").show();
  };
  var params="&idClientContract=" + idClientContract+"&idProject="+idProject;
  params+="&mode=add";
  loadDialog('dialogWorkTokenClientContract', callBack, false, params);
}

function saveWorkTokenMarkup() {
  if (trim(dijit.byId('LabelMarkup').get("value")) == "" || isNaN(dijit.byId('coefValue').get("value"))) {
    if(trim(dijit.byId('LabelMarkup').get("value")) == "" )var msg=i18n("messageMandatory",new Array('colLabelMarkup'));
    else var msg=i18n("messageMandatory",new Array(i18n("colCoefValue")));
    showAlert(msg);
    return;
  }
  var formVar=dijit.byId('workTokenMarkupForm');
  if (formVar.validate()) {
    loadContent("../tool/saveWorkTokenMarkup.php", "resultDivMain", "workTokenMarkupForm",true,'workTokenMarkup');
    dijit.byId('dialogWorkTokenMarkup').hide();
    loadContent("objectDetail.php?refreshWorkTokenMarkup=true","TokenDefinition_treatment", 'listForm');
  } else {
    showAlert(i18n("alertInvalidForm"));
  }
}

function saveWorkTokenClientContract () {
  if ( isNaN(dijit.byId('quantity').get("value")) || trim(dijit.byId('tokentType').get("value"))== "") {
    if (trim(dijit.byId('tokentType').get("value")) == "") msg=i18n("messageMandatory", new Array('tokentType'));
    else var msg=i18n("messageMandatory",new Array('quantity'));
    showAlert(msg);
    return;
  }
  var formVar=dijit.byId('workTokenClientContractForm');
  if (formVar.validate()) {
    loadContent("../tool/saveWorkTokenClientContract.php", "resultDivMain", "workTokenClientContractForm",true,'workTokenClientContract');
    dijit.byId('dialogWorkTokenClientContract').hide();
    loadContent("objectDetail.php?refreshWorkClientContract=true","ClientContract_WorkTokenClientContract", 'listForm');
  } else {
    showAlert(i18n("alertInvalidForm"));
  }
}

function removeTokenMarkUp(tokenMarkupId) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  actionOK=function() {
    loadContent("../tool/saveWorkTokenMarkup.php?mode=delete&idWorkTokenMarkup=" + tokenMarkupId,
        "resultDivMain", null, true, 'workTokenMarkup');
  };
  msg=i18n('confirmDelete', new Array(i18n('TokenMarkup'), tokenMarkupId)) + '<br/><b>';
  showConfirm(msg, actionOK);
}

function removeTokenClientContract(id) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  actionOK=function() {
    loadContent("../tool/saveWorkTokenClientContract.php?mode=delete&idWorkTokenClientContract=" + id,
        "resultDivMain", null, true, 'workTokenClientContract');
  };
  msg=i18n('confirmDelete', new Array(i18n('WorkTokenClientContract'), id)) + '<br/><b>';
  showConfirm(msg, actionOK);
}

function editTokenMarkUp(tokenMarkupId,used) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  var params="&mode=edit&idWorkTokenMarkup=" + tokenMarkupId+"&used="+used;
  loadDialog('dialogWorkTokenMarkup', null, true, params, false);
}

function editTokenClientContract(id,idProject,used) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  var params="&mode=edit&idWorkTokenClientContract=" +id+"&idProject="+idProject+"&used="+used;
  loadDialog('dialogWorkTokenClientContract', null, true, params, false);
}

function changeValueWorkTokenElement(cp){
  var val=dijit.byId('dispatchWorkValue_'+cp).get('value');

  if(val==0){
    dijit.byId("tokenQuantityValue_"+cp).set('value','');
    dijit.byId("tokenQuantityValue_"+cp).set('readOnly',true);
    dijit.byId("tokenQuantityValue_"+cp).set('style','background-color:#F0F0F0');
    dijit.byId("tokenQuantityMarkupValue_"+cp).set('value','');
    dijit.byId("tokenMarkupType_"+cp).set('value','');
    dijit.byId("tokenMarkupType_"+cp).set('readOnly',true);
    dijit.byId("tokenMarkupType_"+cp).set('style','background-color:#F0F0F0');
    dijit.byId("tokenType_"+cp).set('value','');
    dijit.byId("tokenType_"+cp).set('readOnly',true);
    dijit.byId("tokenType_"+cp).set('style','background-color:#F0F0F0');
    if(dijit.byId("billableToken_"+cp).get("checked")==true){
      dijit.byId("billableToken_"+cp).set("checked",false);
    }
    if(dijit.byId("billableTokenInput_"+cp).get("value")==1){
      dijit.byId("billableTokenInput_"+cp).set("value",0);
    }
    if(dijit.byId("billableSiwtched_"+cp).get("value")=="on"){
      dijit.byId("billableSiwtched_"+cp).set("value","off");
    }
 }else{
   
    if(dijit.byId("tokenQuantityValue_"+cp).get('readOnly')==true){
      dijit.byId("tokenQuantityValue_"+cp).set('readOnly',false);
      dijit.byId("tokenQuantityValue_"+cp).set('style','background-color:unset');
    }
    if(dijit.byId("tokenMarkupType_"+cp).get('readOnly')==true){
      dijit.byId("tokenMarkupType_"+cp).set('readOnly',false);
      dijit.byId("tokenMarkupType_"+cp).set('style','background-color:unset');
    }
    if(dijit.byId("tokenType_"+cp).get('readOnly')==true){
      dijit.byId("tokenType_"+cp).set('readOnly',false);
      dijit.byId("tokenType_"+cp).set('style','background-color:unset');
    }
    
 }
}

function dispatchSetTokenTypeEvents(index){
  var val =(dijit.byId('tokenType_'+index).get('value')!="")?dijit.byId('tokenType_'+index).get('value'):0;
  refreshListSpecific('idWorkTokenMarkup','tokenMarkupType_'+index,'idWorkTokenCC',val);
  if(val==""){
    dijit.byId("tokenQuantityValue_"+index).set('value','');
    dijit.byId("tokenQuantityValue_"+index).set('readOnly',true);
    dijit.byId("tokenQuantityValue_"+index).set('style','background-color:#F0F0F0');
    dijit.byId("tokenQuantityMarkupValue_"+index).set('value','');
    dijit.byId("tokenMarkupType_"+index).set('value','');
  }else{
    if(dijit.byId("tokenQuantityValue_"+index).get('readOnly')==true){
      dijit.byId("tokenQuantityValue_"+index).set('readOnly',false);
     dijit.byId("tokenQuantityValue_"+index).set('style','background-color:unset');
    }
     dijit.byId("tokenQuantityMarkupValue_"+index).set('value',dijit.byId("tokenQuantityValue_"+index).get('value'));
     dijit.byId("tokenMarkupType_"+index).set('value','');
  }
  isplitStr=dojo.byId('lstTokenDefSplittable').value;
  isplit=isplitStr.split(',');
  if(isplit.indexOf(val)!=-1){
    quantity=dijit.byId('tokenQuantityValue_'+index).get('value');
    dijit.byId('tokenQuantityValue_'+index).set("constraints",{min:0,places:'0,2'});
    dijit.byId('tokenQuantityValue_'+index).set('value',quantity);
  }else{
    dijit.byId('tokenQuantityValue_'+index).set("constraints",{min:0,places:'0'});
  }
  
}

function dispatchTokenMarkupTypeEvents(index){
  if(dijit.byId('tokenMarkupType_'+index)==undefined)return;
  var val=dijit.byId('tokenMarkupType_'+index).get('value');
  if(val==""){
    var val=dijit.byId("tokenQuantityValue_"+index).get('value');
    dijit.byId("tokenQuantityMarkupValue_"+index).set('value',val);
  }else{
    var coef=val.substr(val.indexOf('_')+1);
    if(isNaN(coef))coef=1;
    var value=dijit.byId("tokenQuantityValue_"+index).get('value')*coef;
    dijit.byId("tokenQuantityMarkupValue_"+index).set('value',dojo.number.format(value));
  }
  updateDispatchWorkTotal("tokenQuantityMarkupValue_","quantityMarkupTotal");
}

function dispatchTokenQuantityValueEvents(index){
  var tokenMakupType=dijit.byId("tokenMarkupType_"+index).get('value');
  if(tokenMakupType=="" || tokenMakupType==0){
    dijit.byId("tokenQuantityMarkupValue_"+index).set('value',dijit.byId('tokenQuantityValue_'+index).get('value')); 
    dijit.byId("tokenMarkupType_"+index).set('value',''); 
  }else{
    var coef=(tokenMakupType!='')? tokenMakupType.substr(tokenMakupType.indexOf('_')+1):1;
    if(isNaN(coef))coef=1;
    var newVal=dijit.byId('tokenQuantityValue_'+index).get('value')*coef;
    dijit.byId("tokenQuantityMarkupValue_"+index).set('value',newVal); 
  }
  updateDispatchWorkTotal("tokenQuantityValue_","quantityTotal");
  updateDispatchWorkTotal("tokenQuantityMarkupValue_","quantityMarkupTotal");
}