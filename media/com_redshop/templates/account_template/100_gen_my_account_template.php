<div class="account_title">
	<h1>{welcome_introtext}</h1>
</div>
<div class="row">
<div class='col-md-3'>
	<?php
		$document = JFactory::getDocument();
		$renderer = $document->loadRenderer('modules');
		echo $renderer->render('personalmenu', $options, null);
	?>
</div>

<div class='col-md-9'>
	<div class='accounttable'>
		<div class="row">
					<div class="col-sm-6">
							<h4>{person_information}</h4>
							<div class="control-group">
								<div class="controls">
									<label>{firstname_lbl}:</label>
									<span>{firstname}</span>
								</div>
							</div>
							<div class="control-group">
								<div class="controls">
									<label>{lastname_lbl}:</label>
									<span>{lastname}</span>
								</div>
							</div>
							<div class="control-group">
								<div class="controls">
									<label>{zipcode_lbl}:</label>
									<span>{zipcode}</span>
								</div>
							</div>
							<div class="control-group">
										<div class="controls">
											<label>{city_lbl}:</label>
											<span>{city}</span>
										</div>
							</div>
							<div class="control-group">
										<div class="controls">
											<label>{country_lbl}:</label>
											<span>{country}</span>
										</div>
							</div>
							<div class="control-group">
								<div class="controls">
									<label>{phone_lbl}:</label>
									<span>{phone}</span>
								</div>
							</div>
							<div class="control-group">
								<div class="controls">
									<label>{email_lbl}:</label>
									<span>{email}</span>
								</div>
							</div>
					</div>
					<div class="col-sm-6">
						<h4>{account_title}</h4>
						<div class="control-group">
							<div class="controls">
								<label>{username_lbl}:</label>
								<span>{username}</span>
							</div>
						</div>
						<div class="control-group">
							<div class="controls">
								<label>Password:</label>
								<span>***********</span>
							</div>
						</div>
						<div class="control-group">
							<div class="controls">
								<label>{point_lbl}:</label>
								<span>{point}</span>
							</div>
						</div>
					</div>
		</div>
		<div class="edit-account row"><div class="col-sm-6">
			{edit_account_link}
			</div><div class="col-sm-6">{continue_shopping}</div></div>
	</div>
</div>
</div>