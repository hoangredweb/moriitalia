<div class="inner-form dm-cua-hang">
  <div class="map">
    {loadposition map-cate}
  </div>
  {include_sub_category_items}
  <div class="row">
    {items_loop_start}
    <div class="col-xs-12">
      <a class="image-link" href="{item_link}">{hinh_dai_dien_value}</a>
      <h3>
        <a href="{item_link}">{item_title}</a>
      </h3>
    </div>
    {items_loop_end}
  </div>
</div>