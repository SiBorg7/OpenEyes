<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

/**
 * A class that all clinical elements should extend from.
 */
class BaseEventTypeElement extends BaseElement {

	public $firm;
	public $userId;
	public $patientId;

	// Used to display the view number set in site_element_type for any particular
	// instance of this element
	public $viewNumber;

	// Used during creation and updating of elements
	public $required = false;

	function getElementType() {
		return ElementType::model()->find('class_name=?', array(get_class($this)));
	}

	/**
	 * Can this element be copied (cloned/duplicated)
	 * Override to return true if you want an element to be copyable
	 * @return boolean
	 */
	public function canCopy() {
		return false;
	}

	/**
	 * Return this elements children
	 * @return array
	 */
	public function getChildren() {
		$child_element_types = ElementType::model()->findAll('parent_element_type_id = :element_type_id', array(':element_type_id' => $this->getElementType()->id));
		$child_elements = array();
		foreach($child_element_types as $child_element_type) {
			if($element = self::model($child_element_type->class_name)->find('event_id = ?', array($this->event_id))) {
				$child_elements[] = $element;
			}
		}
		return $child_elements;
	}
	
	/**
	 * Fields which are copied by the loadFromExisting() method
	 * By default these are taken from the "safe" scenario of the model rules, but
	 * should be overridden for more complex requirements  
	 * @return array:
	 */
	protected function copiedFields() {
		$rules = $this->rules();
		$fields = null;
		foreach($rules as $rule) {
			if($rule[1] == 'safe') {
				$fields = $rule[0];
				break;
			}
		}
		$fields = explode(',', $fields);
		$no_copy = array('event_id','id');
		foreach($fields as $index => $field) {
			if(in_array($field,$no_copy)) {
				unset($fields[$index]);
			} else {
				$fields[$index] = trim($field);
			}
		}
		return $fields;
	}
	
	/**
	 * Load an existing element's data into this one
	 * The base implementation simply uses copiedFields(), but it may be
	 * overridden to allow for more complex relationships
	 * @param BaseEventTypeElement $element
	 */
	public function loadFromExisting($element) {
		foreach($this->copiedFields() as $attribute) {
			$this->$attribute = $element->$attribute;
		}
	}
	
	function render($action) {
		$this->Controller->renderPartial();
	}

	function getSetting($key) {
		$element_type = ElementType::model()->find('class_name=?',array(get_class($this)));

		if (!$metadata = SettingMetadata::model()->find('element_type_id=? and `key`=?',array($element_type->id,$key))) {
			return false;
		}

		if ($setting = SettingUser::model()->find('user_id=? and element_type_id=? and `key`=?',array(Yii::app()->session['user']->id,$element_type->id,$key))) {
			return $this->parseSetting($setting, $metadata);
		}

		$firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);

		if ($setting = SettingFirm::model()->find('firm_id=? and element_type_id=? and `key`=?',array($firm->id,$element_type->id,$key))) {
			return $this->parseSetting($setting, $metadata);
		}

		if ($setting = SettingSubspecialty::model()->find('subspecialty_id=? and element_type_id=? and `key`=?',array($firm->serviceSubspecialtyAssignment->subspecialty_id,$element_type->id,$key))) {
			return $this->parseSetting($setting, $metadata);
		}

		if ($setting = SettingSpecialty::model()->find('specialty_id=? and element_type_id=? and `key`=?',array($firm->serviceSubspecialtyAssignment->subspecialty->specialty_id,$element_type->id,$key))) {
			return $this->parseSetting($setting, $metadata);
		}

		$site = Site::model()->findByPk(Yii::app()->session['selected_site_id']);

		if ($setting = SettingSite::model()->find('site_id=? and element_type_id=? and `key`=?',array($site->id,$element_type->id,$key))) {
			return $this->parseSetting($setting, $metadata);
		}

		if ($setting = SettingInstitution::model()->find('institution_id=? and element_type_id=? and `key`=?',array($site->institution_id,$element_type->id,$key))) {
			return $this->parseSetting($setting, $metadata);
		}

		if ($setting = SettingInstallation::model()->find('element_type_id=? and `key`=?',array($element_type->id,$key))) {
			return $this->parseSetting($setting, $metadata);
		}

		return $metadata->default_value;
	}

	function parseSetting($setting, $metadata) {
		if (@$data = unserialize($metadata->data)) {
			if (isset($data['model'])) {
				$model = $data['model'];
				return $model::model()->findByPk($setting->value);
			}
		}

		return $setting->value;
	}

	/**
	 * Here we need to provide default options for when the element is instantiated
	 * by findByPk in ClinicalService->getElements().
	 *
	 * @param object $firm
	 * @param int $patientId
	 * @param int $userId
	 * @param int $viewNumber
	 * @param boolean $required
	 */
	public function setBaseOptions($firm = null, $patientId = null, $userId = null, $viewNumber = null, $required = false) {
		$this->firm = $firm;
		$this->patientId = $patientId;
		$this->userId = $userId;
		$this->viewNumber = $viewNumber;
		$this->required = $required;
	}

	/**
	 * Returns a list of Exam Phrases to be used by the element form.
	 *
	 * @return array
	 */
	public function getPhraseBySubspecialtyOptions($section) {
		$section = Section::Model()->getByType('Exam', $section);
		return array_merge(array('-' => '-'), CHtml::listData(PhraseBySubspecialty::Model()->findAll('subspecialty_id = ? AND section_id = ?', array($this->firm->serviceSubspecialtyAssignment->subspecialty_id, $section->id)), 'id', 'phrase'));
	}

	/**
	 * Stubbed method to set default options
	 * Used by child objects to set defaults for forms on create
	 */
	public function setDefaultOptions() {
	}

	/**
	 * Stubbed method to set update options
	 * Used by child objects to override null values for forms on update
	 */
	public function setUpdateOptions() {
	}

	public function getInfoText() {
	}

	public function getDefaultView() {
		return get_class($this);
	}

	public function getCreate_view() {
		return $this->getDefaultView();
	}

	public function getUpdate_view() {
		return $this->getDefaultView();
	}

	public function getView_view() {
		return $this->getDefaultView();
	}

	public function getPrint_view() {
		return $this->getDefaultView();
	}

	public function isEditable() {
		return true;
	}

	public function requiredIfSide($attribute, $params) {
		if (($params['side'] == 'left' && $this->eye_id != 2) || ($params['side'] == 'right' && $this->eye_id != 1)) {
			if ($this->$attribute == null) {
				$this->addError($attribute, ucfirst($params['side'])." ".$this->getAttributeLabel($attribute)." cannot be blank.");
			}
		}
	}

	public function wrap($relations=array()) {
		$table = $this->tableName();
		$data = Yii::app()->db->createCommand("select $table.* from $table where id = $this->id")->queryRow();

		$data['event_id'] = '{Event:'.$this->event->hash.'}';

		foreach ($relations as $class => $key) {
			$table = $class::model()->tableName();

			if (is_array($key)) {
				$data['_relations'][$class] = Yii::app()->db->createCommand("select $table.*, '{element_id}' as {$key['key']} from $table where {$key['key']} = $this->id")->queryAll();
				foreach ($data['_relations'][$class] as $i => $item) {
					foreach ($key['_relations'] as $class2 => $key2) {
						$table2 = $class2::model()->tableName();
						$data['_relations'][$class][$i]['_relations'][$class2] = Yii::app()->db->createCommand("select $table2.*, '{parent_id}' as {$key2} from $table2 where {$key2} = {$item['id']}")->queryAll();
					}
				}
			} else {
				$data['_relations'][$class] = Yii::app()->db->createCommand("select $table.*, '{element_id}' as $key from $table where $key = $this->id")->queryAll();
			}
		}

		return $data;
	}
}
