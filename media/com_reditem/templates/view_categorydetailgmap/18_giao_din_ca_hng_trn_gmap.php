<div class="row map">
	<div class="box-filter">
      <div class="container">
         <h3>
            Hệ thống cửa hàng
          </h3>
          <div class="search">
            <div class="inner-search row">
              <div class="col-sm-4 col-xs-12">{filter_title|0|Tìm kiếm}</div>
              <div class="col-sm-3 col-xs-12">{filter_category|2|select}</div>
              </div>
            </div>
          </div>
      </div>  
	</div>
	<div class="col-sm-12 col-xs-12 content-map">
    {include_sub_category_items}
    
   	{items_loop_start}
    	<div class="item-map row">
          <div class="col-sm-12">
             <label>
              {item_title}
            </label>
            <p>
              {item_location_address}
            </p>
          </div>
        </div>
    {items_loop_end}
		<div id="reditemCategoryDetailGmapCanvas" style="width: 100%; height: 610px;"></div>	  		
	</div>
</div>