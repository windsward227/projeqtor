<?php 
require_once "../tool/projeqtor.php";
require_once "../tool/formatter.php";
$categ=null;
if (isset($_REQUEST['idCategory'])) {
  $categ=$_REQUEST['idCategory'];
}
$lstIdOfSubCateg=null;
if(RequestHandler::isCodeSet('lstRepId')){
  $lstIdOfSubCateg=RequestHandler::getValue('lstRepId');
  $referTo=(RequestHandler::isCodeSet('referTo')?RequestHandler::getValue('referTo'):'');
  if(pq_strpos($lstIdOfSubCateg,',')==false){
    $rep= new Report();
    $repCat= new ReportCategory($lstIdOfSubCateg);
    $whereCat="idReportCategory=".Sql::fmtId($lstIdOfSubCateg);
    $lstIdOfSubCateg=$rep->getSqlElementsFromCriteria(null,false,$whereCat);
    $namSubCateg=$repCat->name;
  }else{
    $namSubCateg=(RequestHandler::isCodeSet('nameSubCat'))?RequestHandler::getValue('nameSubCat'):$referTo;
  }
}
$hr=new HabilitationReport();
$user=getSessionUser();
$allowedReport=array();
$allowedCategory=array();
$lst=$hr->getSqlElementsFromCriteria(array('idProfile'=>$user->idProfile, 'allowAccess'=>'1'), false);
foreach ($lst as $h) {
  $report=$h->idReport;
  $nameReport=SqlList::getNameFromId('Report', $report, false);
  if (! Module::isReportActive($nameReport)) continue;
  $allowedReport[$report]=$report;
  $category=SqlList::getFieldFromId('Report', $report, 'idReportCategory',false);
  $allowedCategory[$category]=$category;
}

if (!$categ) {
  echo "<div class='messageData headerReport' style= 'white-space:nowrap;margin-top:5px'>";
  echo pq_ucfirst(i18n('colCategory'));
  echo "</div>";
  $listCateg=SqlList::getList('ReportCategory');
  echo "<ul class='bmenu'>";
  foreach ($listCateg as $id=>$name) {
    if (isset($allowedCategory[$id])) {
      echo "<li class='sectionCategorie' onClick='loadDiv(\"../view/reportListMenu.php?idCategory=$id\",\"reportMenuList\");'><div class='bmenuCategText'>$name</div></li>";
    }
  }
  echo "</ul>";
} else {
  $catObj=new ReportCategory($categ);
  $title=i18n($catObj->name);
  if($lstIdOfSubCateg){
    $title=i18n(pq_ucfirst($namSubCateg));
  }
  if($title=="[../tool/jsonPlanning]")$title=i18n('GanttPlan');
  echo "<div class='messageData headerReport' style= 'white-space:nowrap;margin-top:5px'>";
  echo $title;
  echo "</div>";
  echo "<div class='arrowBack' style='position:absolute;top:5px;left:25px;'>";
  if($lstIdOfSubCateg)$undo="loadDiv(\"../view/reportListMenu.php?idCategory=$categ\",\"reportMenuList\")";
  else $undo="loadDiv(\"../view/reportListMenu.php\",\"reportMenuList\")";
  echo "<span class='dijitInline dijitButtonNode backButton noRotate'  onClick='$undo' style='border:unset;'>";
  if(isNewGui()){
    echo formatNewGuiButton('Back', 22);
  }else{
    echo formatBigButton('Back'); 
  }
  echo "</div>";
  echo '</span>';
  
  $report=new Report();
  $res=array();
  
  
  //================================Sort reports and category ==============================================//
  $lstReportName=array();
  $lstNewListReport=array();
  
  if(!$lstIdOfSubCateg){
    $crit=array('idReportCategory'=>$categ);
    $listReport=$report->getSqlElementsFromCriteria($crit, false, null, 'sortOrder asc');
    $nameOfFiles=SqlList::getListWithCrit("Report","idReportCategory = $categ and (referTo IS NULL or referTo='') and id not in (21,22)","file");
    $referToList=SqlList::getListWithCrit("Report","idReportCategory = $categ and referTo IS NOT NULL and referTo<>''","referTo");
    
    foreach ($nameOfFiles as $idN=>$nameFile){
      $lstReportName[]=pq_substr($nameFile, 0,pq_strpos($nameFile, '.php'));
    }
    $countNameFil=array_count_values($lstReportName);
    foreach ($countNameFil as $name=>$val){
      if($val==1)unset($countNameFil[$name]);
      else $lstNewListReport[]=$name;
    }
    
    if(!empty($referToList))$lstNewListReport=array_unique(array_merge($lstNewListReport,array_unique($referToList)));
    storReportsView($listReport, $res, $lstNewListReport);
  }else{
    $where="idReportCategory=$categ and id in ($lstIdOfSubCateg) ";
    $listReport=$report->getSqlElementsFromCriteria(null, false, $where, 'sortOrder asc');
  }

  ksort($res);
 //==================================================================================//
 
  echo "<ul class='bmenu report' style=''>";
  if(!empty($res)){
    foreach ($res as $rpt) {
      $id=$rpt['object']['id'];
      $name=$rpt['object']['name'];
      if($rpt['objectType']=='reportSubMenu'){
        $lstRepId=$rpt['object']['lstRepId'];
        $referToVal=$rpt['object']['referTo'];
        if($name=='../tool/jsonPlanning')$name='GanttPlan';
        echo "<li class='sectionCategorie' onClick='loadDiv(\"../view/reportListMenu.php?idCategory=$categ&lstRepId=$lstRepId&referTo=$referToVal&nameSubCat=$name\",\"reportMenuList\");'><div class='bmenuCategText'>".i18n($name)."</div></li>";
      }else{
        if (isset($allowedReport[$id])) {
          echo "<li class='section' id='report$id' onClick='reportSelectReport($id);'><div class='bmenuText'>".pq_ucfirst(i18n($name))."</div></li>";
        }
      }
    }
  }else{
    foreach ($listReport as $rpt) {
      if (isset($allowedReport[$rpt->id])) {
        echo "<li class='section' id='report$rpt->id' onClick='reportSelectReport($rpt->id);'><div class='bmenuText'>".pq_ucfirst(i18n($rpt->name))."</div></li>";
      }
    }
  }
  echo "</ul>";
}  



function storReportsView($listReport, &$res, $lstNewListReport ) { //store report
  $count=array();
  $isNewPId=array();
  foreach ($listReport as $id=>$report){
    if($report->id=="108" and !Module::isModuleActive('moduleTechnicalProgress'))continue;
    $referTo=false;
    
    if($report->referTo!=''){
      $file=$report->referTo;
      $referTo=true;
    }else{
      $file=pq_substr($report->file, 0,pq_strpos($report->file, '.php'));
    }
    
    if(in_array($file, $lstNewListReport)){
      if(!isset($count[$file])){
        $sortOrder=$report->sortOrder;
        $count[$file]=1;
        $keyParent=numericFixLengthFormatter($sortOrder,10);
        $isNewPId[$file]=$sortOrder;
        $obj= array('id'=>$isNewPId[$file],'name'=>pq_ucfirst($file),'lstRepId'=>$report->id,'referTo'=>(($referTo)?$file:''));
        $res[$keyParent]=array('objectType'=>'reportSubMenu','object'=>$obj);
      }else{
        $key=numericFixLengthFormatter($isNewPId[$file],10);
        $res[$key]['object']['lstRepId'].=','.$report->id;
      }
    }else{
      $keyParent=numericFixLengthFormatter(('100'.$report->sortOrder),10);
      $obj= array('id'=>$report->id,'name'=>$report->name);
      $res[$keyParent]=array('objectType'=>'reportDirect','object'=>$obj);
    }
  }
}
?>