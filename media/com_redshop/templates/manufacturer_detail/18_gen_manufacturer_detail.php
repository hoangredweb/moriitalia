<div class="manufacturer_name">{manufacturer_name}</div>
<div class="manufacturer_image">{manufacturer_image}</div>
<div class="manufacturer_description">{manufacturer_description}</div>
<div class="manufacturer_product_link"><a href="{manufacturer_allproductslink}">{manufacturer_allproductslink_lbl}</a>
</div>
<div class="manu_categories">
  <div class="row">
    {category_loop_start}
    <div class="manufacture col-sm-4 col-xs-12">
      <h3>
        {category_name_with_link}
      </h3>
      <div class="intro">
        {category_desc}
      </div>
    </div>
    {category_loop_end}
  </div>
</div>