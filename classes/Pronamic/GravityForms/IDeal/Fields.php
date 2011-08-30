<?php

/**
 * Title: iDEAL fields
 * Description: 
 * Copyright: Copyright (c) 2005 - 2011
 * Company: Pronamic
 * @author Remco Tolsma
 * @version 1.0
 */
class Pronamic_GravityForms_IDeal_Fields {
	public static function bootstrap() {
		add_filter('gform_add_field_buttons', array(__CLASS__, 'addFieldButtons'));
		add_filter('gform_field_input', array(__CLASS__, 'acquirerFieldInput'), 10, 5);
	}
	
	public static function acquirerFieldInput($field_content, $field, $value, $lead_id, $form_id) {
		$type = RGFormsModel::get_input_type($field);

		if($type == Pronamic_GravityForms_IDeal_IssuerDropDown::TYPE) {
			$id = $field['id'];
			$fieldId = IS_ADMIN || $form_id == 0 ? "input_$id" : "input_" . $form_id . "_$id";
	        $class_suffix = RG_CURRENT_VIEW == "entry" ? "_admin" : "";
	        $size = rgar($field, "size");
	        $class = $size . $class_suffix;
			$css_class = trim(esc_attr($class) . " gfield_ideal_acquirer_select");
	        $tabIndex = GFCommon::get_tabindex();
        	$disabledText = (IS_ADMIN && RG_CURRENT_VIEW != "entry") ? "disabled='disabled'" : "";
		
        	$html = '';

        	$iDealFeed = Pronamic_GravityForms_IDeal_FeedsRepository::getFeedByFormId($form_id);

        	/**
        	 * Developing warning:
        	 * Don't use single quotes in the HTML you output, it is buggy in combination with SACK
        	 */
			if(IS_ADMIN) {
				if($iDealFeed === null) {
					$html .= sprintf(
						"<a class='ideal-edit-link' href='%s' target='_blank'>%s</a>" , 
						Pronamic_GravityForms_IDeal_AddOn::getEditFeedLink() , 
						__('Create iDEAL feed', Pronamic_WordPress_IDeal_Plugin::TEXT_DOMAIN)
					);
				} else {
					$html .= sprintf(
						"<a class='ideal-edit-link' href='%s' target='_blank'>%s</a>" , 
						Pronamic_GravityForms_IDeal_AddOn::getEditFeedLink($iDealFeed->getId()) , 
						__('Edit iDEAL feed', Pronamic_WordPress_IDeal_Plugin::TEXT_DOMAIN)
					);
				}
			}

			$htmlInput = '';
			$htmlError = '';

			if($iDealFeed !== null) {
				$configuration = $iDealFeed->getIDealConfiguration();
				
				$lists = Pronamic_WordPress_IDeal_IDeal::getTransientIssuersLists($configuration);
				
				if($lists) {
					$options = Helper::issuersSelectOptions($lists, '', $value);
					$options = str_replace('"', '\'', $options);

					$htmlInput  = '';
					$htmlInput .= sprintf("	<select name='input_%d' id='%s' class='%s' %s %s>", $id, $fieldId, $css_class, $tabIndex, $disabledText);
					$htmlInput .= sprintf("		%s", $options);
					$htmlInput .= sprintf("	</select>");
				} elseif($error = Pronamic_WordPress_IDeal_IDeal::getError()) {
					$htmlError = $error->getConsumerMessage();
				} else {
					$htmlError = __('Paying with iDEAL is not possible. Please try again later or pay another way.', Pronamic_WordPress_IDeal_Plugin::TEXT_DOMAIN);
				}
			}
			
			if($htmlError) {
				$html .= sprintf("<div class='gfield_description validation_message'>");
				$html .= sprintf("	%s", $htmlError);
				$html .= sprintf("</div>");
			} else {
				$html .= sprintf("<div class='ginput_container ginput_ideal'>");			
				$html .= sprintf("	%s", $htmlInput);
				$html .= sprintf("</div>");
			}

			return $html;
		}
	}

	public static function addFieldButtons($groups) {
		$fields = array(
			array(
				'class' => 'button' , 
				'value' => __('Issuer Drop Down', Pronamic_WordPress_IDeal_Plugin::TEXT_DOMAIN) , 
				'onclick' => sprintf("StartAddField('%s');", Pronamic_GravityForms_IDeal_IssuerDropDown::TYPE)
			)
		);

		$group = array(
			'name' => 'ideal_fields',
			'label' => __('iDEAL Fields', Pronamic_WordPress_IDeal_Plugin::TEXT_DOMAIN) , 
			'fields' => $fields
		);

		$groups[] = $group;

		return $groups;
		
	}
}
