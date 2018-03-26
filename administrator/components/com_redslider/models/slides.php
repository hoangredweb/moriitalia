<?php
/**
 * @package     RedSLIDER.Backend
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * RedSLIDER slides Model
 *
 * @package     RedSLIDER.Component
 * @subpackage  Models.slides
 * @since       2.0.0
 *
 */
class RedsliderModelSlides extends RModelList
{
	/**
	 * Name of the filter form to load
	 *
	 * @var  string
	 */
	protected $filterFormName = 'filter_slides';

	/**
	 * Limitstart field used by the pagination
	 *
	 * @var  string
	 */
	protected $limitField = 'slides_limit';

	/**
	 * Limitstart field used by the pagination
	 *
	 * @var  string
	 */
	protected $limitstartField = 'auto';

	/**
	 * Constructor.
	 *
	 * @param   array  $config  [description]
	 *
	 * @see     JController
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				's.title', 'title',
				's.ordering', 'ordering',
				's.published', 'published',
				's.gallery_id', 'gallery_id',
				'g.title', 'gallery_title',
				't.title', 'template_title',
				's.section', 'section',
				's.id',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to cache the last query constructed.
	 *
	 * This method ensures that the query is constructed only once for a given state of the model.
	 *
	 * @return JDatabaseQuery A JDatabaseQuery object
	 */
	public function getListQuery()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select(
			$this->getState(
				'list.select',
				's.*,' . $db->qn('g.title') . ' AS ' . $db->qn('gallery_title') . ', ' . $db->qn('t.title') . ' AS ' . $db->qn('template_title')
			)
		);
		$query->from('#__redslider_slides AS s')
			->leftJoin($db->qn('#__redslider_galleries', 'g') . ' ON ' . $db->qn('s.gallery_id') . ' = ' . $db->qn('g.id'))
			->leftJoin($db->qn('#__redslider_templates', 't') . ' ON ' . $db->qn('s.template_id') . ' = ' . $db->qn('t.id'));

		// Filter: like / search
		$search = $this->getState('filter.search', '');

		if ($search != '')
		{
			$like = $db->quote('%' . $search . '%');
			$query->where('s.title LIKE ' . $like);
		}

		// Filter by published state
		$published = $this->getState('filter.published');

		if (is_numeric($published))
		{
			$query->where('s.published = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(s.published IN (0, 1))');
		}

		// Filter by gallery id
		$galleryId = $this->getState('filter.gallery_id');

		if (is_numeric($galleryId))
		{
			$query->where('s.gallery_id = ' . (int) $galleryId);
		}

		// Get the ordering modifiers
		$orderCol	= $this->state->get('list.ordering', 's.ordering');
		$orderDirn	= $this->state->get('list.direction', 'asc');
		$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

		return $query;
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return	string  A store id.
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':' . $this->getState('filter.search');
		$id	.= ':' . $this->getState('filter.published');

		return parent::getStoreId($id);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * @param   string  $ordering   [description]
	 * @param   string  $direction  [description]
	 *
	 * @return  void
	 */
	protected function populateState($ordering = 's.ordering', $direction = 'ASC')
	{
		$app = JFactory::getApplication();

		$filterSearch = $this->getUserStateFromRequest($this->context . '.filter_search', 'filter_search');
		$this->setState('filter.search', $filterSearch);

		$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$gallery_id = $this->getUserStateFromRequest($this->context . '.filter.gallery_id', 'filter_gallery_id', '');
		$this->setState('filter.gallery_id', $gallery_id);

		$value = $app->getUserStateFromRequest('global.list.limit', $this->paginationPrefix . 'limit', $app->getCfg('list_limit'), 'uint');
		$limit = $value;
		$this->setState('list.limit', $limit);

		$value = $app->getUserStateFromRequest($this->context . '.limitstart', $this->paginationPrefix . 'limitstart', 0);
		$limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
		$this->setState('list.start', $limitstart);

		parent::populateState($ordering, $direction);
	}
}
