<div class="blog cate-style tin-tuc">
  {include_sub_category_items}
  <div class="items-row row">
  	  {items_loop_start}
    <div class="itemwrapper span4 col-sm-12 col-md-12">
      <div class="item column-1" itemtype="http://schema.org/BlogPosting" itemscope="" itemprop="blogPost">
        <div class="pull-left item-image">
          <a href="{item_link}">{hinh_dai_dien_value}</a>
        </div>
        <div class="blog_content">
          <div class="blogtitle">
            	<h3>
                    <a href="{item_link}">{item_title}</a>
                </h3>
          </div>
          <div class="blogdesc">
            {gioi_thieu_value|250}
          </div>
          <a class="btndiscover" href="{item_link}"><?php echo JText::_('COM_REDITEM_CATEGORYDETAIL_FEED_ITEM_READ_MORE');?></a>
        </div>
      </div>
    </div>
	{items_loop_end}
  </div>
  {items_pagination|5}
</div>