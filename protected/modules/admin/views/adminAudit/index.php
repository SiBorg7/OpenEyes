<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>
<div class="fullWidth fullBox clearfix" style="margin-top: 2px;">
	<div id="waitinglist_display">
	<p><?php echo Yii::app()->db->createCommand("select count(distinct data) from audit where action='login-successful' and created_date > '2012-07-02 23:59:59'")->queryScalar(); ?> unique users since 3rd July 2012</p>
	<p><?php echo Yii::app()->db->createCommand("select count(data) from audit where action='login-successful' and created_date > '2012-07-02 23:59:59'")->queryScalar(); ?> total successful logins since 3rd July 2012</p>
		<form method="post" action="/audit/search" id="auditList-filter">
			<input type="hidden" id="page" name="page" value="1" />
			<div id="search-options">
				<div id="main-search" class="grid-view" style="width: 1190px;">
					<h3>Filter by:</h3>
						<table>
							<tbody>
								<tr>
									<th>Site:</th>
									<th>Firm:</th>
									<th>User:</th>
									<th>Action:</th>
									<th>Target type:</th>
								</tr>
								<tr class="even">
									<td>
										<?php echo CHtml::dropDownList('site_id',@$_POST['site_id'],Site::model()->getList(),array('empty'=>'All sites'))?>
									</td>
									<td>
										<?php echo CHtml::dropDownList('firm_id', @$_POST['firm_id'], Firm::model()->getListWithoutDupes(), array('empty'=>'All firms'))?>
									</td>
									<td>
										<?php echo CHtml::dropDownList('user_id', @$_POST['user_id'], User::model()->getList(), array('empty'=>'All users'))?>
									</td>
									<td>
										<?php echo CHtml::dropDownList('action', @$_POST['action'], $actions, array('empty' => 'All actions'))?>
									</td>
									<td>
										<?php echo CHtml::dropDownList('target_type', @$_POST['target_type'], $targets, array('empty' => 'All targets'))?>
									</td>
									<td width="20px;" style="margin-left: 50px; border: none;">
										<img class="loader" src="/img/ajax-loader.gif" alt="loading..." style="float: right; margin-left: 0px; display: none;" />
									</td>
									<td style="padding: 0;" width="70px;">
										<button type="submit" class="classy green tall" style="float: right;"><span class="button-span button-span-green">Filter</span></button>
									</td>
								</tr>
								</tbody>
							</table>
						</div>
						<div id="extra-search" class="eventDetail clearfix">
							<label for="date_from">
								From:
							</label>
							<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
								'name' => 'date_from',
								'id' => 'date_from',
								'options' => array(
									'showAnim'=>'fold',
									'dateFormat'=>Helper::NHS_DATE_FORMAT_JS
								),
								'value' => @$_POST['date_from'],
								'htmlOptions' => array('style' => "width: 95px;"),
							))?>
							<label for="date_to">
								To:
							</label>
							<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
								'name' => 'date_to',
								'id' => 'date_to',
								'options' => array(
									'showAnim'=>'fold',
									'dateFormat'=>Helper::NHS_DATE_FORMAT_JS
								),
								'value' => @$_POST['date_to'],
								'htmlOptions' => array('style' => "width: 95px;"),
							))?>
							&nbsp;&nbsp;
							Hos num:
							<?php echo CHtml::textField('hos_num',@$_POST['hos_num'],array('style'=>'width: 100px;'))?>
							&nbsp;&nbsp;
							<a href="/audit">View all</a>
							<div class="whiteBox pagination" style="display: none; margin-top: 10px;">
							</div>
						</div>
					</div>
					<input type="hidden" id="previous_site_id" value="<?php echo @$_POST['site_id']?>" />
					<input type="hidden" id="previous_firm_id" value="<?php echo @$_POST['firm_id']?>" />
					<input type="hidden" id="previous_user_id" value="<?php echo @$_POST['user_id']?>" />
					<input type="hidden" id="previous_action" value="<?php echo @$_POST['action']?>" />
					<input type="hidden" id="previous_target_type" value="<?php echo @$_POST['target_type']?>" />
					<input type="hidden" id="previous_date_from" value="<?php echo @$_POST['date_from']?>" />
					<input type="hidden" id="previous_date_to" value="<?php echo @$_POST['date_to']?>" />
					<input type="hidden" id="previous_hos_num" value="<?php echo @$_POST['hos_num']?>" />
				</form>
				<div id="searchResults" class="whiteBox">
				</div>
			</div>
			<div style="float: right; margin-right: 18px;">
			</div>
		</div> <!-- .fullWidth -->
<script type="text/javascript">
	$('#auditList-filter button[type="submit"]').click(function() {
		if (!$(this).hasClass('inactive')) {
			disableButtons();
			$('#searchResults').html('<div id="auditList" class="grid-view"><ul id="auditList"><li class="header"><span>Searching...</span></li></ul></div>');

			$.ajax({
				'url': '<?php echo Yii::app()->createUrl('audit/search'); ?>',
				'type': 'POST',
				'data': $('#auditList-filter').serialize(),
				'success': function(data) {
					$('#previous_site_id').val($('#site_id').val());
					$('#previous_firm_id').val($('#firm_id').val());
					$('#previous_user_id').val($('#user_id').val());
					$('#previous_action').val($('#action').val());
					$('#previous_target_type').val($('#target_type').val());
					$('#previous_date_from').val($('#date_from').val());
					$('#previous_date_to').val($('#date_to').val());

					var s = data.split('<!-------------------------->');

					$('#searchResults').html(s[0]);
					$('div.pagination').html(s[1]).show();

					enableButtons();
					return false;
				}
			});
		}
		return false;
	});

	$(document).ready(function() {
		$('#auditList-filter button[type="submit"]').click();
	});
</script>