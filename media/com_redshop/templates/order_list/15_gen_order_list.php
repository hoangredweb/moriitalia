<div class="account_title">
  <h1>Tài khoản</h1>
</div>

<div class='col-md-3'>
        {loadposition personalmenu}
</div>
<div class='account-wrapper'>
  
      <div class='col-md-9'>
        <div class='table-responsive account-box account-wrapper ac-orderlist'>
        <h4>Đơn hàng</h4>
          <table border="0" cellspacing="5" cellpadding="5" width="100%" class="improvedTable account orderlist">
              <tbody>
              <tr class="header">
                  <th class="order_id orderid" width='97px'>{order_id_lbl}</th>
                  <th width='350px'>{product_name_lbl}</th>
                  <th width='200px'>{total_price_lbl}</th>
                  <th width='180px'>{order_date_lbl}</th>
                  <th class="ord-status" width='134px'>{order_status_lbl}</th>
                  <th width='100'>{order_detail_lbl}</th>
              </tr>
              <!--  {product_loop_start} -->
              <tr>
                  <td class="orderid">{order_id}</td>
                  <td >{order_products}</td>
                  <td >{order_total}</td>
                  <td >{order_date}</td>
                  <td class="ord-status">{order_status}</td>
                  <td class="ord-detail">{order_detail_link}{reorder_link}</td>
              </tr>
              <!--  {product_loop_end} -->
              </tbody>
          </table>
          <div class="category_pagination">{pagination}</div>
        </div>
  </div>
</div>