<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2017
 * @package     sh404SEF
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     4.9.2.3552
 * @date		2017-06-01
 */

// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC')) die('Direct Access to this location is not allowed.');

?>
<form method="post" name="adminForm" id="adminForm">

<?php

  echo $this->loadTemplate( $this->joomlaVersionPrefix . '_filters');

?>

<div id="editcell">
    <table class="adminlist">
      <thead>
        <tr>
          <th class="title" width="3%">
            <?php echo JText::_( 'NUM' ); ?>
          </th>
          <th width="2%">
            <input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
          </th>
          <th class="title" width="5%" >
            <?php echo JHTML::_('grid.sort', JText::_( 'COM_SH404SEF_HITS'), 'cpt', $this->options->filter_order_Dir, $this->options->filter_order); ?>
          </th>
	        <th class="title" width="5%"><?php echo JText::_('COM_SH404SEF_HIT_TYPE_INTERNAL'); ?>
	        </th>
          <th class="title"  style="text-align: left;" >
            <?php echo JHTML::_('grid.sort', JText::_( 'COM_SH404SEF_SEF_URL'), 'oldurl', $this->options->filter_order_Dir, $this->options->filter_order); ?>
          </th>
          <th class="title" width="15%">
            &nbsp;
          </th>
          <th class="title" width="15%">
            &nbsp;
          </th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="6">
            <?php echo $this->pagination->getListFooter(); ?>
          </td>
        </tr>
      </tfoot>
      <tbody>
        <?php
        $k = 0;
        if( $this->itemCount > 0 ) {
          for ($i=0; $i < $this->itemCount; $i++) {

            $url = &$this->items[$i];
            $checked = JHtml::_( 'grid.id', $i, $url->id);
            $custom = '&nbsp;'; ?>

        <tr class="<?php echo "row$k"; ?>">
          <td align="center">
            <?php echo $this->pagination->getRowOffset( $i ); ?>
          </td>
          <td align="center">
            <?php echo $checked; ?>
          </td>
          <td align="center">
            <?php echo empty($url->cpt) ? '&nbsp;' : $this->escape( $url->cpt); ?>
          </td>
	        <td align="center">
		        <?php echo $url->referrer_type == Sh404sefHelperUrl::IS_INTERNAL ? '<span title="'.JText::_('COM_SH404SEF_HIT_TYPE_INTERNAL_404_TITLE').'">&cross;</span>' : '&nbsp;'; ?>
	        </td>
          <td>
            <?php
              $linkData = array( 'c' => 'editurl', 'task' => 'edit', 'cid[]' => $url->id, 'tmpl' => 'component');
              $urlData = array( 'title' => $url->oldurl, 'class' => 'modalediturl');
              $modalOptions = array( 'size' => array('x' =>800, 'y' => 600));
              echo Sh404sefHelperHtml::makeLink( $this, $linkData, $urlData, $modal = true, $modalOptions, $hasTip = false, $extra = '');
              ?>
          </td>
          <td align="center">
            <?php
              $linkData = array( 'c' => 'notfound', 'notfound_url_id' => $url->id, 'tmpl' => 'component');
              $urlData = array( 'title' => JText::_('COM_SH404SEF_NOT_FOUND_SHOW_URLS_TITLE'), 'class' => 'modalediturl', 'anchor' => JText::_('COM_SH404SEF_NOT_FOUND_SHOW_URLS'));
              $modalOptions = array( 'size' => array('x' => '\\window.getScrollSize().x*.9', 'y' => '\\window.getSize().y*.9'));
              echo Sh404sefHelperHtml::makeLink( $this, $linkData, $urlData, $modal = true, $modalOptions, $hasTip = false, $extra = '');
            ?>
          </td>
          <td align="center">
            <?php
              $linkData = array( 'c' => 'editnotfound', 'notfound_url_id' => $url->id, 'task' => 'newredirect', 'tmpl' => 'component');
              $urlData = array( 'title' => JText::_('COM_SH404SEF_NOT_FOUND_ENTER_REDIRECT_TITLE'), 'class' => 'modalediturl', 'anchor' => JText::_('COM_SH404SEF_NOT_FOUND_ENTER_REDIRECT'));
              $modalOptions = array( 'size' => array('x' => '\\window.getScrollSize().x*.7', 'y' => '\\window.getSize().y*.5'));
              echo Sh404sefHelperHtml::makeLink( $this, $linkData, $urlData, $modal = true, $modalOptions, $hasTip = false, $extra = '');
            ?>
          </td>
        </tr>
        <?php
        $k = 1 - $k;
      }
    } else {
      ?>
        <tr>
          <td align="center" colspan="6">
            <?php echo JText::_( 'COM_SH404SEF_NO_URL' ); ?>
          </td>
        </tr>
        <?php
      }
      ?>
      </tbody>
    </table>
    <input type="hidden" name="c" value="urls" />
    <input type="hidden" name="view" value="urls" />
    <input type="hidden" name="layout" value="view404" />
    <input type="hidden" name="option" value="com_sh404sef" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $this->options->filter_order; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $this->options->filter_order_Dir; ?>" />
    <?php echo JHTML::_( 'form.token' ); ?>
  </div>
</form>

<div class="sh404sef-footer-container">
	<?php echo $this->footerText; ?>
</div>
