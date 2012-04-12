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

<div class="<?php echo $element->elementType->class_name?> ondemand<?php if (@$ondemand){?> hidden<?php }?>">
	<h4 class="elementTypeName"><?php echo $element->elementType->name ?></h4>

	<div class="splitElement clearfix" style="background-color: #DAE6F1;">
		<div class="left" style="width:65%;">
			<?php
			$this->widget('application.modules.eyeDraw.OEEyeDrawWidgetCataract', array(
				'side'=>$element->getSelectedEye()->getShortName(),
				'mode'=>'edit',
				'size'=>300,
				'model'=>$element,
				'attribute'=>'eyedraw',
			));
			?>
			<div class="halfHeight">
				<?php echo $form->dropDownList($element, 'incision_site_id', CHtml::listData(IncisionSite::model()->findAll(), 'id', 'name'),array('empty'=>'- Please select -'))?>
				<?php echo $form->textField($element, 'length', array('size' => '10'))?>
				<?php echo $form->textField($element, 'meridian', array('size' => '10'))?>
				<?php echo $form->dropDownList($element, 'incision_type_id', CHtml::listData(IncisionType::model()->findAll(), 'id', 'name'),array('empty'=>'- Please select -'))?>
				<?php echo $form->checkBox($element, 'vision_blue')?>
				<?php echo $form->textArea($element, 'report', array('rows'=>4,'cols'=>25))?>
			</div>
		</div>
		<div class="right" style="width:35%;">
			<div class="halfHeight">
				<?php echo $form->dropDownList($element, 'iol_position_id', CHtml::listData(IOLPosition::model()->findAll(), 'id', 'name'),array('empty'=>'- Please select -'))?>
				<?php echo $form->multiSelectList($element, 'CataractComplications', 'complications', 'complication_id', CHtml::listData(CataractComplications::model()->findAll(), 'id', 'name'), array(), array('empty' => '- Complications -', 'label' => 'Complications'))?>
				<?php echo $form->textArea($element, 'complication_notes', array('rows'=>4,'cols'=>25))?>
				<?php
				$this->widget('application.modules.eyeDraw.OEEyeDrawWidgetSurgeonPosition', array(
					'side'=>$element->getSelectedEye()->getShortName(),
					'mode'=>'edit',
					'size'=>200,
					'model'=>$element,
					'attribute'=>'eyedraw2',
				));
				?>
			</div>
		</div>
	</div>
</div>