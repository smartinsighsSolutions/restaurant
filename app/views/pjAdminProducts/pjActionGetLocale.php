<?php
$category = '<select name="category_id" id="category_id" class="pj-form-field required w300">';
foreach ($tpl['category_arr'] as $v)
{
	$category .= sprintf('<option value="%u">%s</option>', $v['id'], (!empty($v['parent_id']) ? '------' : null) . stripslashes($v['name']));
}
$category .= '</select>';

pjAppController::jsonResponse(compact('category'));
?>