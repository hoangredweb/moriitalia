<?php
/**
 * @package     RedSlider
 * @subpackage  Fields
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_BASE') or die;

/**
 * Article field.
 *
 * @package     RedSLIDER
 * @subpackage  Fields
 * @since       2.0
 */
class JFormFieldModal_Article extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Modal_Article';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * 
	 * @since	1.6
	 */
	protected function getInput()
	{
		// Load the modal behavior script.
		JHtml::_('behavior.modal', 'a.modalArticleAjax');

		// Build the script.
		$script = array();
		$script[] = '	function jSelectArticle_' . $this->id . '(id, title, catid, object) {';
		$script[] = '		document.id("' . $this->id . '_id").value = id;';
		$script[] = '		document.id("' . $this->id . '_name").value = title;';
		$script[] = '		SqueezeBox.close();';
		$script[] = '	}';

		// Add the script to the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

		// Setup variables for display.
		$html	= array();
		$link	= 'index.php?option=com_content&amp;view=articles&amp;layout=modal&amp;tmpl=component&amp;function=jSelectArticle_' . $this->id;

		$db	= JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select($db->qn('title'))
			->from($db->qn('#__content'))
			->where($db->qn('id') . ' = ' . (int) $this->value);

		$db->setQuery($query);
		$title = $db->loadResult();
		$error = $db->getErrorMsg();

		if ($error)
		{
			JError::raiseWarning(500, $error);
		}

		if (empty($title))
		{
			$title = JText::_('PLG_REDSLIDER_SECTION_ARTICLE_SELECT_ARTICLE');
		}

		$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

		// The current user display field.

		$html = array();
		$html[] = '<div class="input-prepend input-append">';
		$html[] = '<input type="text" class="input-small" id="' . $this->id . '_name" value="' . $title . '" disabled="disabled"/>';
		$html[] = '<a class="btn modalArticleAjax" title="' . JText::_('PLG_REDSLIDER_SECTION_ARTICLE_SELECT_ARTICLE_BUTTON') . '"
			href="' . $link . '&amp;' . JSession::getFormToken() . '=1" rel="{handler: \'iframe\', size: {x: 800, y: 450}}">'
			. JText::_('PLG_REDSLIDER_SECTION_ARTICLE_SELECT_ARTICLE_BUTTON') . '</a>';
		$html[] = '</div>';

		// The active article id field.
		if (0 == (int) $this->value)
		{
			$value = '';
		}
		else
		{
			$value = (int) $this->value;
		}

		// Client side validation
		$class = '';

		if ($this->required)
		{
			$class = ' class="required modal-value"';
		}

		$html[] = '<input type="hidden" id="' . $this->id . '_id"' . $class . ' name="' . $this->name . '" value="' . $value . '" />';

		return implode("\n", $html);
	}
}
