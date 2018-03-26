<div class="row">
	<div class="col-sm-6">
		{private_billing_template:private_billing_template}

		{company_billing_template:company_billing_template}
	</div>

	{account_creation_start}
	<div class="col-sm-6">
		<div class="form-group">
			<label>{username_lbl}:<span class="required">*</span></label>
			{username}

		</div>

		<div class="form-group">
			<label>{password_lbl}:<span class="required">*</span></label>
			{password}

		</div>

		<div class="form-group">
			<label>{confirm_password_lbl}:<span class="required">*</span></label>
			{confirm_password}

		</div>

		<div class="form-group">
			<label>{newsletter_signup_chk}</label>
			{newsletter_signup_lbl}
		</div>
	</div>
	{account_creation_end}
</div>

<div class="checkbox">
	<label>
		{shipping_same_as_billing}
		{shipping_same_as_billing_lbl}
	</label>
</div>



