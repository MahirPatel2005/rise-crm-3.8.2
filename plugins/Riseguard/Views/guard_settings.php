<div class="card clearfix rounded-0 m20">
	<div class="m20 mb0 mt0">
		<ul id="guard-setting-tabs" data-bs-toggle="ajax-tab" class="nav nav-tabs bg-white rounded-0 title" role="tablist">
			<li><a id="monthly-attendance-button"  role="presentation" data-bs-toggle="tab"  href="javascript:;" data-bs-target="#brute_force_settings"><?php echo app_lang("brute_force_settings"); ?></a></li>
			<li><a role="presentation" data-bs-toggle="tab" href="" data-bs-target="#blacklist_ip"><?php echo app_lang('blacklist_ip_email'); ?></a></li>    
			<li><a role="presentation" data-bs-toggle="tab" href="" data-bs-target="#login_expiry_for_staff"><?php echo app_lang('login_expiry_single_session'); ?></a></li>
		</ul>
	</div>
	<div class="m-4">
		<div class="tab-content clearfix rounded-0">
			<div role="tabpanel" class="tab-pane fade" id="brute_force_settings">
				<div class="row">
					<div class="col-md-12">
						<div class="alert alert-info" font-medium="">
							<span><?php echo app_lang('settings_for_only_staff_login') ?></span>
						</div>
					</div>
				</div>
				<?php echo form_open(get_uri('riseguard/settings'), ['class' => 'form-inline', 'id' => 'bruteForceSettingsForm'], []); ?>
				<!-- max retries -->
				<div class="form-group">
					<div class="row m10 mb-4">
						<div class="col-md-4 col-sm-6">
							<span class="text-danger">*</span>
							<strong for="max_retries">
								<span data-bs-toggle="tooltip" data-bs-html="true" title="<?php echo app_lang('max_retries_tooltip') ?>"><i data-feather='help-circle' class='icon-16'></i></span>
								<?php echo app_lang('max_retries') ?>
							</strong> <br>
							<small class="text-muted"><?php echo app_lang('max_failed_attempts_allowed_before_lockout') ?></small>
						</div>
						<div class="col-md-4 col-sm-6">
							<input type="number" class="form-control mright5" id="max_retries" name="max_retries" min="1" max="10" value="<?php echo get_setting('max_retries') ?>" data-rule-required="true" data-msg-required="<?php echo app_lang('field_required'); ?>">
						</div>
						<div class="col-md-4"></div>
					</div>
				</div>
				<!-- over -->
				<!-- lockout time -->
				<div class="form-group">
					<div class="row m10 mb-4">
						<div class="col-md-4 col-sm-6">
							<span class="text-danger">*</span>
							<strong for="lockout_time">
								<span data-bs-toggle="tooltip" data-bs-html="true" title="<?php echo app_lang('lockout_time_tooltip') ?>"><i data-feather='help-circle' class='icon-16'></i></span>
								<?php echo app_lang('lockout_time') ?>
							</strong>
							<small class="text-muted">(<?php echo app_lang('in_minutes') ?>)</small>
						</div>
						<div class="col-md-4 col-sm-6">
							<input type="number" class="form-control mright5" id="lockout_time" name="lockout_time" min="5" max="60" value="<?php echo get_setting('lockout_time') ?>" data-rule-required="true" data-msg-required="<?php echo app_lang('field_required'); ?>">
						</div>
						<div class="col-md-4"></div>
					</div>
				</div>
				<!-- over -->
				<!-- Max lockouts -->
				<div class="form-group">
					<div class="row m10 mb-4">
						<div class="col-md-4 col-sm-6">
							<span class="text-danger">*</span>
							<strong for="max_lockouts">
								<span data-bs-toggle="tooltip" data-bs-html="true" title="<?php echo app_lang('max_lockouts_tooltip') ?>"><i data-feather='help-circle' class='icon-16'></i></span>
								<?php echo app_lang('max_lockouts') ?>
							</strong>
						</div>
						<div class="col-md-4 col-sm-6">
							<input type="number" class="form-control mright5" id="max_lockouts" name="max_lockouts" min="1" max="10" value="<?php echo get_setting('max_lockouts') ?>" data-rule-required="true" data-msg-required="<?php echo app_lang('field_required'); ?>">
						</div>
						<div class="col-md-4"></div>
					</div>
				</div>
				<!-- over -->
				<!-- Extend Lockout -->
				<div class="form-group">
					<div class="row m10 mb-4">
						<div class="col-md-4 col-sm-6">
							<span class="text-danger">*</span>
							<strong for="extend_lockout">
								<span data-bs-toggle="tooltip" data-bs-html="true" title="<?php echo app_lang('extend_lockout_tooltip') ?>"><i data-feather='help-circle' class='icon-16'></i></span>
								<?php echo app_lang('extend_lockout') ?>
								<small class="text-muted">(<?php echo app_lang('in_hours') ?>)</small>
							</strong> <br>
							<small class="text-muted"><?php echo app_lang('extend_lockout_time_after_max_lockouts') ?></small>
						</div>
						<div class="col-md-4 col-sm-6">
							<input type="number" class="form-control mright5" id="extend_lockout" name="extend_lockout" min="5" max="240" value="<?php echo get_setting('extend_lockout') ?>" data-rule-required="true" data-msg-required="<?php echo app_lang('field_required'); ?>">
						</div>
						<div class="col-md-4"></div>
					</div>
				</div>
				<!-- over -->
				<!-- Reset Retries -->
				<div class="form-group">
					<div class="row m10 mb-4">
						<div class="col-md-4 col-sm-6">
							<span class="text-danger">*</span>
							<strong for="reset_retries">
								<span data-bs-toggle="tooltip" data-bs-html="true" title="<?php echo app_lang('reset_retries_tooltip') ?>"><i data-feather='help-circle' class='icon-16'></i></span>
								<?php echo app_lang('reset_retries') ?>
								<small class="text-muted">(<?php echo app_lang('in_hours') ?>)</small>
							</strong>
						</div>
						<div class="col-md-4 col-sm-6">
							<input type="number" class="form-control mright5" id="reset_retries" name="reset_retries" min="1" max="48" value="<?php echo get_setting('reset_retries') ?>" data-rule-required="true" data-msg-required="<?php echo app_lang('field_required'); ?>">
						</div>
						<div class="col-md-4"></div>
					</div>
				</div>
				<!-- over -->
				<!-- Email Notification -->
				<div class="form-group">
					<div class="row m10 mb-4">
						<div class="col-md-4 col-sm-6">
							<strong for="email_notification_after_no_of_lockouts">
								<span data-bs-toggle="tooltip" data-bs-html="true" title="<?php echo app_lang('email_notification_tooltip') ?>"><i data-feather='help-circle' class='icon-16'></i></span>
								<?php echo app_lang('email_notification') ?>
								<small class="text-muted"><?php echo app_lang('after') ?> (<?php echo app_lang('no_of_lockouts') ?>) <?php echo app_lang('lockouts') ?></small>
							</strong> <br>
							<small class="text-muted"><?php echo app_lang('0_to_disable_email_notifications') ?></small>
						</div>
						<div class="col-md-4 col-sm-6">
							<input type="number" class="form-control mright5" id="email_notification_after_no_of_lockouts" name="email_notification_after_no_of_lockouts" min="0" max="10" value="<?php echo get_setting('email_notification_after_no_of_lockouts') ?>" data-rule-required="true" data-msg-required="<?php echo app_lang('field_required'); ?>">
						</div>
						<div class="col-md-4"></div>
					</div>
				</div>
				<!-- over -->
				<!-- User inactivity timeout -->
				<div class="form-group">
					<div class="row m10 mb-4">
						<div class="col-md-4 col-sm-6">
							<span class="text-danger">*</span>
							<strong for="user_inactivity">
								<span data-bs-toggle="tooltip" data-bs-html="true" title="<?php echo app_lang('user_inactivity_tooltip') ?>"><i data-feather='help-circle' class='icon-16'></i></span>
								<?php echo app_lang('user_inactivity') ?>
								<small class="text-muted">(<?php echo app_lang('in_minutes') ?>)</small>
							</strong> <br>
							<small class="text-muted"><?php echo app_lang('user_inactivity_description') ?></small>
						</div>
						<div class="col-md-4 col-sm-6">
							<input type="number" class="form-control mright5" id="user_inactivity" name="user_inactivity"min="0" max="240" value="<?php echo get_setting('user_inactivity') ?>" data-rule-required="true" data-msg-required="<?php echo app_lang('field_required'); ?>">
						</div>
						<div class="col-md-4"></div>
					</div>
				</div>
				<!-- Over -->
				<div class="row m10 mb-4">
					<div class="col-md-4 col-sm-6">
						<span class="text-danger">*</span>
						<strong for="send_mail_if_ip_is_different">
							<span data-bs-toggle="tooltip" data-bs-html="true" title="<?php echo app_lang('max_retries_tooltip') ?>"><i data-feather='help-circle' class='icon-16'></i></span>
							<?php echo app_lang('send_mail_if_ip_is_different') ?>
						</strong> <br>
					</div>
					<div class="col-md-4 col-sm-6">
						<?php
						echo form_radio(array(
							"id" => "yes",
							"name" => "send_mail_if_ip_is_different",
							"class" => "form-check-input send_mail_if_ip_is_different",
							"data-msg-required" => app_lang("field_required"),
						), "1", (get_setting('send_mail_if_ip_is_different') == "1") ? true : false);
						?>         
						<strong class="radio-inline"><?php echo app_lang('yes') ?></strong>
						<?php
						echo form_radio(array(
							"id" => "yes",
							"name" => "send_mail_if_ip_is_different",
							"class" => "form-check-input send_mail_if_ip_is_different",
							"data-msg-required" => app_lang("field_required"),
						), "0", (get_setting('send_mail_if_ip_is_different') === "0") ? true : ((get_setting('send_mail_if_ip_is_different') !== "1") ? true : false));
						?>     
						<strong class="radio-inline"><?php echo app_lang('no') ?></strong>
					</div>
					<div class="col-md-4"></div>
				</div>
				<div class="clearfix"></div>
				<hr/>
				<button class="btn btn-primary"><?php echo app_lang('save_settings') ?></button>
				<?php echo form_close(); ?>
			</div>
		</div>
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane fade" id="blacklist_ip">
				<div class="row">
					<div class="col-md-12">
						<div class="alert alert-info" role="alert">
							<span><?php echo app_lang('settings_for_only_staff_login') ?></span>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="card shadow p-3 mb-4 bg-body rounded">
							<?php echo form_open(get_uri('riseguard/add_ip_email'), ['id' => 'blacklist_ip_form', 'class' => 'general-form', 'role' => 'form'],['type' => 'ip']); ?>
							<div class="blacklist_ip_section">
								<div class="blacklist_ip_row mbot15" id="blacklist_ip_row_0">
									<div class="row m10">
										<div class="col-md-11">
											<span class="text-danger">*</span>
											<strong class="blacklist_ip"><?php echo app_lang('blacklist_ip') ?></strong>
											<small class="text-muted"><?php echo app_lang('one_ip_or_ip_range_per_line') ?></small>
											<?php
											echo form_input(array(
												"id" => "blacklist_ip",
												"name" => "blacklist_ip[0]",
												"value" => '',
												"class" => "form-control",
												"data-rule-required" => true,
												"data-msg-required" => app_lang("field_required"),
											));
											?>
										</div>
										<div class="col-md-1 mt20">
											<button class="btn btn-success add_blacklist_ip_row" type="button"><i data-feather='plus-circle' class='icon-16'></i></button>
											<button class="btn btn-danger remove_blacklist_ip_row hide" data-count="0"><i data-feather='x' class='icon-16'></i></button>
										</div>
									</div>
								</div>
							</div>
							<div class="row mt20">
								<div class="d-grid gap-2 d-md-flex justify-content-md-end">
									<button class="btn btn-sm btn-primary" id="blacklist_ip_form_submit_btn"><?php echo app_lang('add_ip_to_blacklist') ?></button>
								</div>
							</div>
							<?php echo form_close(); ?>
							<hr/>
							<h4 class="mbot30"><?php echo app_lang('list_of_blacklist_ip') ?></h4>
							<div class="table-responsive">
								<table id="blacklistIp-table" class="display" cellspacing="0" width="100%">
								</table>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="card shadow p-3 mb-4 bg-body rounded">
							<?php echo form_open(get_uri('riseguard/add_ip_email'), ['id' => 'blacklist_email_form', 'class' => 'general-form', 'role' => 'form'],['type' => 'email']); ?>
							<div class="blacklist_email_section">
								<div class="blacklist_email_row mbot15" id="blacklist_email_row_0">
									<div class="row m10">
										<div class="col-md-11">
											<span class="text-danger">*</span>
											<strong class="blacklist_email"><?php echo app_lang('blacklist_email') ?></strong>
											<?php
											echo form_input(array(
												"id" => "blacklist_email",
												"name" => "blacklist_email[0]",
												"value" => '',
												"class" => "form-control",
												"data-rule-required" => true,
												"data-msg-required" => app_lang("field_required"),
											));
											?>
										</div>
										<div class="col-md-1 mt20">
											<button class="btn btn-success add_blacklist_email_row" type="button"><i data-feather='plus-circle' class='icon-16'></i></button>
											<button class="btn btn-danger remove_blacklist_email_row hide" data-count="0"><i data-feather='x' class='icon-16'></i></button>
										</div>
									</div>
								</div>
							</div>
							<div class="row mt20">
								<div class="d-grid gap-2 d-md-flex justify-content-md-end">
									<button class="btn btn-sm btn-primary" id="blacklist_email_form_submit_btn"><?php echo app_lang('add_email_to_blacklist') ?></button>
								</div>
							</div>
							<?php echo form_close(); ?>
							<hr/>
							<h4 class="mbot30"><?php echo app_lang('list_of_blacklist_emails') ?></h4>
							<div class="table-responsive">
								<table id="blacklistEmail-table" class="display" cellspacing="0" width="100%">
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane fade" id="login_expiry_for_staff">
				<div class="row">
					<div class="col-md-6">
						<div class="card shadow p-3 mb-4 bg-body rounded">
							<div class="row">
								<div class="col-md-12">
									<div class="alert alert-info" font-medium="">
										<span> &#9900; <?php echo app_lang('settings_for_only_staff_login') ?></span> <br>
										<span> &#9900; <?php echo app_lang('cron_job_setup_required') ?></span>
									</div>
								</div>
							</div>
							<?php echo form_open(get_uri('riseguard/addLoginExpiry'), ['id' => 'addStaffExpiryForm', 'class' => 'general-form', 'role' => 'form']); ?>
							<div class="row mb15">
								<input type="hidden" name="update" id="update" value="">
								<div class="form-group">
									<span class="text-danger">*</span>
									<strong for="staffid" class="control-label"><?php echo app_lang('select_staff'); ?></strong>
									<select name="staffid" id="staffid" class="select2 validate-hidden form-control" data-rule-required="true" data-msg-required=" <?= app_lang('field_required') ?>">
										<option></option>
										<?php foreach ($allStaff as $staff) { ?>
											<option value="<?= $staff['id'] ?>"><?= $staff['first_name'].$staff['last_name'] ?></option>
										<?php } ?>
									</select>
								</div>
								<div class="form-group">
									<span class="text-danger">*</span>
									<strong for="expiry_date" class="control-label"><?php echo app_lang('expiry_date'); ?></strong>
									<?php
									echo form_input(array(
										"id" => "expiry_date",
										"name" => "expiry_date",
										"value" => get_setting('expiry_date') ?? '',
										"class" => "form-control",
										"data-rule-required" => true,
										"data-msg-required" => app_lang("field_required"),
									));
									?>
								</div>
							</div>
							<div class="col-md-12 mt20 d-grid justify-content-md-end">
								<button class="btn btn-primary" type="submit">
									<span class="add"><?php echo app_lang('set_expiry_date') ?></span>
									<span class="update hide"><?php echo app_lang('update_expiry_date') ?></span>
								</button>
							</div>
							<?php echo form_close(); ?>
							<div class="clearfix"></div>
							<hr class=""/>
							<h4 class=""><?php echo app_lang('user_expiry') ?></h4>
							<div class="table-responsive">
								<table id="user_expiry-table" class="display" cellspacing="0" width="100%">
								</table>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="card shadow p-3 mb-4 bg-body rounded">
							<div class="row">
								<div class="col-md-12">
									<div class="alert alert-info">
										<span><?php echo app_lang('settings_for_only_staff_login') ?></span>
									</div>
								</div>
							</div>
							<?php echo form_open(get_uri('riseguard/settings'), ['class' => 'form-inline', 'id' => 'single-session-form'], []); ?>
							<div class="row mt15 mb15">
								<div class="col-md-9 col-sm-12">
									<span class="text-danger">*</span>
									<strong for="prevent_user_from_login_more_than_once">
										<span data-bs-toggle="tooltip" data-bs-html="true" title="<?php echo app_lang('prevent_user_tooltip') ?>"><i data-feather='help-circle' class='icon-16'></i></span>
										<?php echo app_lang('prevent_user_from_login_more_than_once') ?> ?
									</strong> <br>
								</div>
								<div class="col-md-3 col-sm-12">
									<?php
									echo form_radio(array(
										"id" => "yes",
										"name" => "prevent_user_from_login_more_than_once",
										"class" => "form-check-input prevent_user_from_login_more_than_once",
										"data-msg-required" => app_lang("field_required"),
									), "1", (get_setting('prevent_user_from_login_more_than_once') == "1") ? true : false);
									?>
									<strong class="radio-inline"><?php echo app_lang('yes') ?></strong>
									<?php
									echo form_radio(array(
										"id" => "yes",
										"name" => "prevent_user_from_login_more_than_once",
										"class" => "form-check-input prevent_user_from_login_more_than_once",
										"data-msg-required" => app_lang("field_required"),
									), "0", (get_setting('prevent_user_from_login_more_than_once') == "0") ? true : false);
									?>
									<strong class="radio-inline"><?php echo app_lang('no') ?></strong>
								</div>
								<div class="col-md-2 col-sm-12"></div>
							</div>
							<div class="clearfix"></div>
							<hr/>
							<div class="d-grid justify-content-md-end">
								<button class="btn btn-primary"><?php echo app_lang('save_settings') ?></button>
							</div>
							<?php echo form_close(); ?>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>
<script>
	$("#staffid").select2();
	setDatePicker('#expiry_date');
	$('#bruteForceSettingsForm').appForm({
		isModal:false,
		onSuccess : function(response) {
			appAlert.success(response.message, {duration:3000});
		}
	});

	$('#single-session-form').appForm({
		isModal:false,
		onSuccess : function(response) {
			appAlert.success(response.message, {duration:3000});
		}
	});

	$('#addStaffExpiryForm').appForm({
		isModal:false,
		onSuccess : function(response) {
			$('#staffid').val('');
			$('#expiry_date').val('');
			$('#staffid').trigger('change');
			appAlert.success(response.message, {duration:3000});
			$('#user_expiry-table').appTable({reload:true});
		}
	});

	$('#blacklist_ip_form').appForm({
		isModal:false,
		onSuccess : function(response) {
			const ip_email = response.type;
			$(`input[name="blacklist_${ip_email}[0]"]`).val('')
			$(`.blacklist_${ip_email}_row`)
			.not(`#blacklist_${ip_email}_row_0`)
			.remove()
			appAlert.success(response.message, {duration:3000});
			$('#blacklistIp-table').appTable({reload:true});
		},
		onError: function (response) {
			const ip_email = response.type;
			$(`input[name="blacklist_${ip_email}[0]"]`).val('')
			$(`.blacklist_${ip_email}_row`)
			.not(`#blacklist_${ip_email}_row_0`)
			.remove()
			appLoader.hide();
			appAlert.error(response.message, {duration:3000});
		}
	});

	$('#blacklist_email_form').appForm({
		isModal:false,
		onSuccess : function(response) {
			const ip_email = response.type;
			$(`input[name="blacklist_${ip_email}[0]"]`).val('')
			$(`.blacklist_${ip_email}_row`)
			.not(`#blacklist_${ip_email}_row_0`)
			.remove()
			appAlert.success(response.message, {duration:3000});
			$('#blacklistEmail-table').appTable({reload:true});
		},
		onError: function (response) {
			const ip_email = response.type;
			$(`input[name="blacklist_${ip_email}[0]"]`).val('')
			$(`.blacklist_${ip_email}_row`)
			.not(`#blacklist_${ip_email}_row_0`)
			.remove()
			appLoader.hide();
			appAlert.error(response.message, {duration:3000});
		}
	});

	$(".add_blacklist_ip_row").on("click", function(event) {
		event.preventDefault();
		var total_element = $(".blacklist_ip_row").length;
		var last_id = $(".blacklist_ip_row:last").attr('id').split("_");
		var next_id = Number(last_id[3]) + 1;
		$("#blacklist_ip_row_0").clone()
		.attr('id', `blacklist_ip_row_${next_id}`)
		.html((i, OldHtml) => {
			OldHtml = OldHtml.replaceAll("blacklist_ip[0]",`blacklist_ip[${next_id}]`);
			return OldHtml;
		})
		.appendTo($(".blacklist_ip_row:last").parent());
		$(`#blacklist_ip_row_${next_id} .add_blacklist_ip_row`).remove();
		$(`#blacklist_email_row_${next_id} .form-group`).removeClass("has-error");
		$(`#blacklist_email_row_${next_id} .form-group p`).remove();
		$(`#blacklist_ip_row_${next_id} :input`).val("");
		$(`#blacklist_ip_row_${next_id} .remove_blacklist_ip_row`).removeClass('hide').data('count', next_id);

		add_ip_validation();
	});

	$(document).on('click', '.remove_blacklist_ip_row', function(event) {
		event.preventDefault();
		$(`#blacklist_ip_row_${$(this).data('count')}`).remove();

		add_ip_validation();

	});

	$(".add_blacklist_email_row").on("click", function(event) {
		event.preventDefault();
		var total_element = $(".blacklist_email_row").length;
		var last_id = $(".blacklist_email_row:last").attr('id').split("_");
		var next_id = Number(last_id[3]) + 1;
		$("#blacklist_email_row_0").clone()
		.attr('id', `blacklist_email_row_${next_id}`)
		.html((i, OldHtml) => {
			OldHtml = OldHtml.replaceAll("blacklist_email[0]",`blacklist_email[${next_id}]`);
			return OldHtml;
		})
		.appendTo($(".blacklist_email_row:last").parent());
		$(`#blacklist_email_row_${next_id} .add_blacklist_email_row`).remove();
		console.log($(`#blacklist_email_row_${next_id} .add_blacklist_email_row .form-group`));
		$(`#blacklist_email_row_${next_id} .form-group`).removeClass("has-error");
		$(`#blacklist_email_row_${next_id} .form-group p`).remove();
		$(`#blacklist_email_row_${next_id} :input`).val("");
		$(`#blacklist_email_row_${next_id} .remove_blacklist_email_row`).removeClass('hide').data('count',next_id);

		add_email_validation();
	});

	$(document).on('click', '.remove_blacklist_email_row', function(event) {
		event.preventDefault();
		$(`#blacklist_email_row_${$(this).data('count')}`).remove();

		add_email_validation();
	});

	$(document).ready(function () {
		$("#blacklistIp-table").appTable({
			source: '<?php echo_uri('riseguard/blacklistip_table/ip'); ?>',
			columns: [
			          {title: '<?php echo app_lang('#'); ?>'},
			          {title: '<?php echo app_lang('ip_address'); ?>'},
			          {title: '<?php echo app_lang('actions'); ?>'}
			          ],
			printColumns: [0,1],
			xlsColumns: [0,1]
		});
	});

	$(document).ready(function () {
		$("#blacklistEmail-table").appTable({
			source: '<?php echo_uri('riseguard/blacklistip_table/email'); ?>',
			columns: [
			          {title: '<?php echo app_lang('#'); ?>'},
			          {title: '<?php echo app_lang('email_address'); ?>'},
			          {title: '<?php echo app_lang('actions'); ?>'}
			          ],
			printColumns: [0,1],
			xlsColumns: [0,1]
		});
	});

	$(document).ready(function () {
		$("#user_expiry-table").appTable({
			source: '<?php echo_uri('riseguard/user_expiry_table'); ?>',
			columns: [
			          {title: '<?php echo app_lang('name'); ?>'},
			          {title: '<?php echo app_lang('expiry_date'); ?>'},
			          {title: '<?php echo app_lang('actions'); ?>'}
			          ],
			printColumns: [0,1],
			xlsColumns: [0,1]
		});
	});
</script>