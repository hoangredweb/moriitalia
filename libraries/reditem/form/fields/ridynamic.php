<?php
/**
 * @package     RedITEM.Backend
 * @subpackage  Field
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

JLoader::import('joomla.form.formfield');


/**
 * RedITEM dynamic field
 *
 * @package     RedITEM.Backend
 * @subpackage  Field.RIDynamic
 *
 * @since       2.1.16
 */
class JFormFieldRIDynamic extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 */
	protected $type = 'RIDynamic';

	/**
	 * Method for render input
	 *
	 * @return  string   HTML render
	 */
	public function getInput()
	{
		$data       = array();
		$attributes = array('required' => false);

		if ($this->required)
		{
			$attributes['required'] = false;
		}

		if (!empty($this->value))
		{
			$tmpValues = explode("\n", $this->value);

			foreach ($tmpValues as $tmpValue)
			{
				if (empty($tmpValue))
				{
					continue;
				}

				$tmpValue = explode("|", $tmpValue);
				$text     = $tmpValue[0];
				$value    = $tmpValue[0];

				if (isset($tmpValue[1]))
				{
					$text = $tmpValue[1];
				}

				$data[] = array('text' => $text, 'value' => $value);
			}
		}

		$layoutData    = array('options' => $data, 'field' => $this, 'attributes' => $attributes);
		$layoutOptions = array('component' => 'com_reditem', 'debug' => false);

		return RLayoutHelper::render('fields.dynamic.render', $layoutData, JPATH_REDITEM_LIBRARY . '/layouts', $layoutOptions);
	}
}
