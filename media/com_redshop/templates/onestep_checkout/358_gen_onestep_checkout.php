<div class="table_billing row">
  <div class="col-md-7">
    <fieldset class="adminform">
      <div class="address">
        <legend>{billing_address_information_lbl}</legend>

        {billing_template}
        {billing_address}
        {edit_billing_address}

      </div>
    </fieldset>

    <fieldset class="adminform ">
      <legend>{shipping_address_information_lbl}</legend>
      <div class="ship">{shipping_address}</div>
    </fieldset>

    {payment_template:payment_method}

    {shippingbox_template:shipping_box}
    {shipping_template:shipping_method}
  </div>

  <div class="col-md-5">
     {checkout_template:gen_checkout}
  </div>
</div>