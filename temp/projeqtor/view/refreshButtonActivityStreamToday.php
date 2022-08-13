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
 * Presents the list of objects of a given class.
 *
 */
require_once "../tool/projeqtor.php";
require_once "../tool/formatter.php";
scriptLog('   ->/view/refreshButtonActivityStreamToday.php'); 


$showActStream= (RequestHandler::isCodeSet('showActStream'))?pq_trim(RequestHandler::getValue('showActStream')):'false';
$iconClass=($showActStream=='true')?'iconActivityStream22 iconActivityStream iconSize22':'iconActivityStreamClose22 iconActivityStreamClose iconSize22';
?>
 <button id="todayShowActivityStream" dojoType="dijit.form.Button"
            showlabel="false" title="<?php echo ($showActStream=='true')?i18n('showActivityStream'):i18n('hideActivityStream');?>"  class="detailButton"
            iconClass="<?php echo $iconClass;?> imageColorNewGui"  >
    <script type="dojo/connect" event="onClick" args="evt">
            showHideActivityStreamToday(<?php echo '\''.$showActStream.'\'';?>);
    </script>
</button>   

