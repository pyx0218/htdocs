<h2><?php echo $user_name;
	if($admin){
		echo' (Administrator)';
	}
?></h2>
<div>
	<?php echo validation_errors(); ?>

	<?php echo form_open('users/setting') ?>
	<h3>Change password:</h3>
	<p>
	<label for="password">Password:</label>
	<input type="password" id="password" name="password" value="<?php echo set_value('password'); ?>" />
	</p>
	<p>
	<label for="con_password">Confirm Password:</label>
	<input type="password" id="con_password" name="con_password" value="<?php echo set_value('con_password'); ?>" />
	</p>
	<p>
	<input type="submit" class="btn" value="Submit" />
	</p>
	</form>
</div>