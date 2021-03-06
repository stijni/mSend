	<?php
	/**
	 * Footer for the backend. Outputs the default mark up and
	 * information generated on functions.php.
	 *
	 * @package ProjectSend
	 */


		load_js_files();
		if(isset($page) && ($page=="upload-process-dropoff.php")){
?>
			<script src="<?php echo BASE_URI;?>includes/js/chosen/chosen.jquery.min.js"></script>
			<script src="<?php echo BASE_URI;?>includes/js/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>

<?php
	}
	?>
    <script type="text/javascript">
	$(document).ready(function(e) {
		$("#hide-menu").click(function() {
			$('body').toggleClass('hidden-menu');
		});
		$("#select_all, .select_all1").click(function(){
			var status = $(this).prop("checked");
			/** Uncheck all first in case you used pagination */
			$(this).closest('table').find('tr td input[type=checkbox]').prop("checked",false);

			//$(this).closest('table').find("tr:visible td input[type=checkbox]").prop("checked",status);
			$(this).closest('table').find('tr:visible td input[type="checkbox"]').each(function () {
			   if(!$(this).attr('disabled')) {
				   $(this).prop("checked",status);
			   }
			});

		});
		$('.btn_generate_password').click(function(e)
			{

				 e.preventDefault();
				 $.ajax({
					url: "click_to_generate_password.php",
					type: "POST",
					async: true,
					 data: {func: 'generate_password'},
					success: function(data) {
						if($("#add_client_form_pass").length == 0) {
							<?php if( ! isset($nUser)){ ?>
							$('#add_user_form_pass').attr({type:"text"});
							<?php } ?>
							$('#add_user_form_pass').val(data);
							}
							else{
							$('#add_client_form_pass').attr({type:"text"});
							$('#add_client_form_pass').val(data);
							}
					},
				});
			});
		$('.footable').footable().find('> tbody > tr:not(.footable-row-detail):nth-child(even)').addClass('odd');



    });
	</script>
<!-- PAGE FOOTER -->
		<div class="page-footer">
			<div class="row">
				<div class="col-xs-12 col-sm-12">
					<span class="txt-color-white">
					<?php
					default_footer_info();
						if ( DEBUG === true ) {
							echo $dbh->GetCount(); // Print the total count of queries made by PDO
						}?>
                        </span>
				</div>


			</div>
		</div>
		<!-- END PAGE FOOTER -->

		<!-- SHORTCUT AREA : With large tiles (activated via clicking user name tag)
		Note: These tiles are completely responsive,
		you can add as many as you like
		-->


							<?php
								if (CURRENT_USER_LEVEL == 0) {
									$my_account_link = 'clientsedit.php';
								}
								else {
									$my_account_link = 'users-edit.php';
								}
								$my_account_link .= '?id='.CURRENT_USER_ID;
							?>
		<div id="shortcut">
			<ul>
            <i class="close-shortcut fa fa-times" aria-hidden="true"></i>


				<li>
                <a href="<?php echo BASE_URI.$my_account_link; ?>" class="jarvismetro-tile big-cubes selected bg-color-pinkDark">
					<span class="iconbox"> <i class="fa fa-user fa-4x"></i> <span><?php _e('My Account', 'cftp_admin'); ?> </span> </span>
                    </a>
				</li>
                <li>
                <a href="<?php echo BASE_URI; ?>process.php?do=logout" class="jarvismetro-tile big-cubes bg-color-greenLight"><span class="iconbox"> <i class="fa fa-sign-out fa-4x"></i> <span><?php _e('Logout', 'cftp_admin'); ?> </span> </span></a>

				</li>
			</ul>
		</div>
		<!-- END SHORTCUT AREA -->

		<!--================================================== -->
	</body>
</html>
<?php ob_end_flush(); ?>


<!-- Added B) -->
<script type="text/javascript">
$(document).ready(function(e) {
	$("#show-shortcut").click(function(e) {
        $("#shortcut").toggleClass('cc-visible');
    });
	$(".close-shortcut").click(function(e) {
		$("#shortcut").toggleClass('cc-visible');
    });
	$(".cc-dropdown").click(function(e) {
			$(this).find('ul').toggleClass('cc-visible');
    });
	$(".cc-active-subpage").parent().addClass('cc-visible');
});
</script>
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<!-- Ended By B) -->
