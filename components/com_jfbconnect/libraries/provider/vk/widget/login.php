<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2015 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.4.1
 * @build-date      2015/07/04
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class JFBConnectProviderVkWidgetLogin extends JFBConnectProviderWidgetLogin
{
    function __construct($provider, $fields)
    {
        parent::__construct($provider, $fields, 'scVkLoginTag');

        $this->className = 'scVkLoginTag';
        $this->tagName = 'SCVkLogin';

    }
}
