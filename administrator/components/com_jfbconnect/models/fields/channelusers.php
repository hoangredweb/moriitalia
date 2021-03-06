<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Field to select a user ID from a modal list.
 *
 * @package     Joomla.Libraries
 * @subpackage  Form
 * @since       1.6
 */
class JFormFieldChannelUserList extends JFormField
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  1.6
     */
    public $type = 'ChannelUserList';
    public $provider = '';

    /**
     * Method to get the user field input markup.
     *
     * @return  string  The field input markup.
     *
     * @since   1.6
     */
    protected function getInput()
    {
        if (defined('SC16')):
        $this->class = (string) $this->element['class'];
        $this->size = (int) $this->element['size'];
        // Initialize JavaScript field attributes.
        $this->onchange = (string) $this->element['onchange'];
        endif; //SC16

        $this->provider = $this->class;

        $html = array();
        $groups = $this->getGroups();
        $excluded = $this->getExcluded();
        $link = 'index.php?option=com_jfbconnect&amp;view=channel&amp;layout=user&amp;provider='.$this->provider.'&amp;tmpl=component&amp;field=' . $this->id
            . (isset($groups) ? ('&amp;groups=' . base64_encode(json_encode($groups))) : '')
            . (isset($excluded) ? ('&amp;excluded=' . base64_encode(json_encode($excluded))) : '');

        // Initialize some field attributes.
        $attr = !empty($this->class) ? ' class="' . $this->class . '"' : '';
        $attr .= !empty($this->size) ? ' size="' . $this->size . '"' : '';
        $attr .= $this->required ? ' required' : '';

        // Load the modal behavior script.
        JHtml::_('behavior.modal', 'a.modal_' . $this->id);

        // Build the script.
        $script = array();
        $script[] = '	function jSelectUser_' . $this->id . '(id, title) {';
        $script[] = '		var old_id = document.getElementById("' . $this->id . '_id").value;';
        $script[] = '		if (old_id != id) {';
        $script[] = '			document.getElementById("' . $this->id . '_id").value = id;';
        if(defined('SC30')):
        $script[] = '			document.getElementById("' . $this->id . '").value = title;';
        $script[] = '			document.getElementById("' . $this->id . '").className = document.getElementById("' . $this->id . '").className.replace(" invalid" , "");';
        endif; //SC30
        if(defined('SC16')):
        $script[] = '			document.getElementById("' . $this->id . '_name").value = title;';
        endif; //SC16
        $script[] = '			' . $this->onchange;
        $script[] = '		}';
        $script[] = '		SqueezeBox.close();';
        $script[] = '	}';

        // Add the script to the document head.
        JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

        // Load the current username if available.
        $table = JTable::getInstance('user');

        if (is_numeric($this->value))
        {
            $table->load($this->value);
        }
        // Handle the special case for "current".
        elseif (strtoupper($this->value) == 'CURRENT')
        {
            // 'CURRENT' is not a reasonable value to be placed in the html
            $this->value = JFactory::getUser()->id;
            $table->load($this->value);
        }
        else
        {
            $table->name = JText::_('JLIB_FORM_SELECT_USER');
        }

        if(defined('SC30')):
        // Create a dummy text field with the user name.
        $html[] = '<div class="input-append">';
        $html[] = '	<input type="text" id="' . $this->id . '" value="' . htmlspecialchars($table->name, ENT_COMPAT, 'UTF-8') . '"'
            . ' readonly' . $attr . ' />';

        // Create the user select button.
        if ($this->readonly === false)
        {
            $html[] = '		<a class="btn btn-primary modal_' . $this->id . '" title="' . JText::_('JLIB_FORM_CHANGE_USER') . '" href="' . $link . '"'
                . ' rel="{handler: \'iframe\', size: {x: 800, y: 500}}">';
            $html[] = '<i class="icon-user"></i></a>';
        }

        $html[] = '</div>';
        endif; //SC30

        if(defined('SC16')):
        // Create a dummy text field with the user name.
        $html[] = '<div class="fltlft">';
        $html[] = '	<input type="text" id="' . $this->id . '_name"' . ' value="' . htmlspecialchars($table->name, ENT_COMPAT, 'UTF-8') . '"'
            . ' disabled="disabled"' . $attr . ' />';
        $html[] = '</div>';

        // Create the user select button.
        $html[] = '<div class="button2-left">';
        $html[] = '  <div class="blank">';
        if ($this->element['readonly'] != 'true')
        {
            $html[] = '		<a class="modal_' . $this->id . '" title="' . JText::_('JLIB_FORM_CHANGE_USER') . '"' . ' href="' . $link . '"'
                . ' rel="{handler: \'iframe\', size: {x: 800, y: 500}}">';
            $html[] = '			' . JText::_('JLIB_FORM_CHANGE_USER') . '</a>';
        }
        $html[] = '  </div>';
        $html[] = '</div>';
        endif; //SC16

        // Create the real field, hidden, that stored the user id.
        $html[] = '<input type="hidden" id="' . $this->id . '_id" name="' . $this->name . '" value="' . $this->value . '" />';

        return implode("\n", $html);
    }

    /**
     * Method to get the filtering groups (null means no filtering)
     *
     * @return  mixed  array of filtering groups or null.
     *
     * @since   1.6
     */
    protected function getGroups()
    {
        return null;
    }

    /**
     * Method to get the users to exclude from the list of users
     *
     * @return  mixed  Array of users to exclude or null to to not exclude them
     *
     * @since   1.6
     */
    protected function getExcluded()
    {
        return null;
    }
}
