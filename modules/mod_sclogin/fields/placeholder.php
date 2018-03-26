<?php
/**
 * @package         SCLogin
 * @copyright (c)   2009-2017 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v7.2.0
 * @build-date      2017/03/29
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.form.helper');

class JFormFieldPlaceholder extends JFormField
{
    public function getInput()
    {
        return "";
    }

    public function getLabel()
    {
        return "";
    }
}
