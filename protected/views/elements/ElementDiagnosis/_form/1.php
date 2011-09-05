<div class="heading">
<span class="emphasize">Book Operation:</span> Select diagnosis
</div>

<?php

// @todo - populate diagnosis eye with appropriate value
$disorderId = '';
$value = '';
$eye = '';

if (empty($model->event_id)) {
	// It's a new event so fetch the most recent element_diagnosis
	$diagnosis = $model->getNewestDiagnosis();

	if (empty($diagnosis->disorder)) {
		// There is no diagnosis for this episode, or no episode, or the diagnosis has no disorder (?)
		$diagnosis = $model;
	} else {
		// There is a diagnosis for this episode
		$value = $diagnosis->disorder->term . ' - ' . $diagnosis->disorder->fully_specified_name;
		$eye = $diagnosis->eye;
		$disorderId = $diagnosis->disorder->id;
	}
} else {
	if (isset($model->disorder)) {
		$value = $model->disorder->term . ' - ' . $model->disorder->fully_specified_name;
		$eye = $model->eye;
		$disorderId = $model->disorder->id;
	}

	$diagnosis = $model;
}

?>
<div class="box_grey_big_gradient_top"></div>
<div class="box_grey_big_gradient_bottom">
	<div class="label">Select eye(s):</div>
	<div class="data"><?php echo CHtml::activeRadioButtonList($diagnosis, 'eye', $model->getEyeOptions(),
		array('separator' => ' &nbsp; ')); ?>
	</div>
	<div class="cleartall"></div>
	<div class="label">Enter diagnosis:</div>
	<div class="data"><span></span>
<?php
$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
    'name'=>'ElementDiagnosis[disorder_id]',
    'id'=>'ElementDiagnosis_disorder_id_0',
    'value'=>$value,
    'sourceUrl'=>array('disorder/autocomplete'),
    'htmlOptions'=>array(
        'style'=>'height:20px;width:400px;font:10pt Arial;'
    ),
));
?></div><span class="tooltip"><a href="#"><img src="/images/icon_info.png" /><span>Type the first few characters of a disorder into the <strong>enter diagnosis</strong> text box. When you see the required disorder displayed - <strong>click</strong> to select.</span></a></span>
</div>
<script type="text/javascript">
	$('input[name="ElementDiagnosis[eye]"]').click(function() {
		var disorder = $('input[name="ElementDiagnosis[disorder_id]"]').val();
		if (disorder.length == 0) {
			$('input[name="ElementDiagnosis[disorder_id]"]').focus();
		}
	});
</script>