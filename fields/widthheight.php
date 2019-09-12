<?php
defined('JPATH_BASE') or die;

// jimport('joomla.html.html');
// jimport('joomla.form.formfield');
// jimport('joomla.form.helper');
// JFormHelper::loadFieldClass('list');

class JFormFieldWidthheight extends JFormField {

	protected function getInput() {
		$currentValue = ($this->value ? $this->value : $this->element['default']);
		list ($width, $height) = explode('x', $currentValue);
		list ($widthDefault, $heightDefault) = explode('x', $this->element['default']);
		
		$output = '<span style="float:left;margin:5px 0px 0 0;">Width:</span>';
		$output .= '<input 
						name="width_'.$this->name.'" 
						type="text" 
						value="'.$width.'" 
						id="width_'.$this->id.'" 
						style="text-align:right;float:left;margin:5px 0px 0 0;" 
						size="'.$this->element['size'].'" 
						class="'.($this->required ? 'required' : '').'" '.($this->required ? 'required' : '').' 
					/>';
		$output .= '<span style="float:left;margin:5px 5px 0 0;">px</span>';
		$output .= '<span style="float:left;margin:5px 0px 0 0;">Height:</span>';
		$output .= '<input 
						name="height_'.$this->name.'"  
						type="text" 
						value="'.$height.'" 
						id="height_'.$this->id.'" 
						style="text-align:right;float:left;margin:5px 0px 0 0;" 
						size="'.$this->element['size'].'" 
						class="'.($this->required ? 'required' : '').'" '.($this->required ? 'required' : '').' 
					/>';
		$output .= '<span style="float:left;margin:5px 5px 0 0;">px</span>';
		$output .= '<input 
						name="'.$this->name.'" 
						type="hidden"  
						value="'.$currentValue.'" 
						id="'.$this->id.'" 
						class="'.$this->element['class'].'" '.($this->required ? 'required' : '').'
					/>';
		$script = "
			jQuery(function($) {
				$('#width_".$this->id."').change(function(e) {
				console.log('here');
					width = ($('#width_".$this->id."').val() ? $('#width_".$this->id."').val() : ".$widthDefault.");
					height = ($('#height_".$this->id."').val() ? $('#height_".$this->id."').val() : ".$heightDefault.");
					$('#".$this->id."').val(width +'x'+ height);
				});
				$('#height_".$this->id."').change(function(e) {
					$('#width_".$this->id."').trigger('change');
				});
			});
		";
		$doc = JFactory::getDocument();
		// JHtml::_('behavior.framework');
		$doc->addScriptDeclaration($script);
		return $output;
	}
}