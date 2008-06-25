<?php
// v0.1

// for config page (use same in other boo-box wp-plugins)
if ( !function_exists('boo_config_page') ) {
	function boo_config_page() {
		if ( function_exists('add_submenu_page') )
			add_submenu_page('plugins.php', __('boo-box configuration', 'boobox'), __('boo-box configuration', 'boobox'), 'manage_options', 'boobox-config', 'boo_config_submenu');

	}
	// insert options for boo-box
	add_action('admin_menu', 'boo_config_page');
}

// the config page
if ( !function_exists('boo_config_submenu') ) {
	function boo_config_submenu(){
	     // refresh user options
	     if (isset($_POST['update_boobox'])) {
			update_option('boo_shopid', $_POST['boo_shopid']);
			update_option('boo_affid', $_POST['boo_affid']);
			?> 
			<div class="updated"><p>Options saved.</p></div>
			<?php
	     }

		// config html
		?>	
		<div class="wrap">
			<h2>boo-box options</h2>
			<form method="post">
				<p>Choose a shop and enter your affiliated code</p>
				<p>If you have questions, contact us: <strong>contact@boo-box.com</strong></p>
				<fieldset class="options">
					<table>
						<tr>
							<td>
								<p>
									<label for="boo_shopid"><strong>Affiliated Program:</strong></label>
								
								</p>
							</td>
							<td>
								<select name="boo_shopid" id="boo_shopid">
									<option value="">Loading</option>
								</select>
								<script type="text/javascript">
									// receive JSONP request with affiliate data from boo-box master
									function pushAffiliates(data) {
										select = document.getElementById("boo_shopid");
										select.innerHTML = "";
										for (var i=0; i < data.shops.length; i++) {
											shop = data.shops[i];
											
											var option = document.createElement('option');
											option.value = shop.id;
											option.innerHTML = shop.name;
											
											// selected?
											("<?php echo get_option('boo_shopid'); ?>" == shop.id) ? option.selected = "selected" : '';
											
											// just attach to #boo_shopid
											select.appendChild(option);
										}
									}
									
									var script = document.createElement('script');
									script.src = 'http://stable.boo-box.com/config.php?format=json&mime=application/json&callback=pushAffiliates';
									script.type = 'text/javascript';
									script.defer = true;
									
									var head = document.getElementsByTagName('head').item(0);
									head.appendChild(script);
									
								</script>
							</td>
						</tr>
						<tr>
							<td>
								<p>
									<label for="boo_affid"><strong>Affiliated Code</strong>:</label>
								</p>
							</td>
							<td>
								<p>
								<input name="boo_affid" type="text" id="boo_affid" value="<?php echo get_option('boo_affid'); ?>" size="25" />
								</p>
						</tr>
					</table>
				</fieldset>
				<p><div class="submit"><input type="submit" name="update_boobox" value="Save Options &raquo;" style="font-weight:bold;" /></div></p>
			</form>
		</div>
	<?php
	} // end of config page
}

if (( !get_option('boo_shopid') || !get_option('boo_affid')) && !isset($_POST['submit']) ) {
	if ( !function_exists('boo_config_warning') ) {
		function boo_config_warning() {
			echo "
			<div id='boo-warning' class='updated fade'><p><strong>".__('boo-box is almost ready.')."</strong> ".sprintf(__('You must select a affiliated program for it work.'), "plugins.php?page=boobox-config")."</p></div>
			";
		}
		add_action('admin_notices', 'boo_config_warning');
	}
}

// let wordpress (and other plugins) know if the boo-box are in <head>
$boobox_head_added = false;

// start atomic bomb with the insert on head
if ( !function_exists('boo_head_script') ) {
	function boo_head_script() {
		global $boobox_head_added;
	
		// for all boo-plugins use this variable, and do that check :)
		if (!$boobox_head_added) {
			echo "\n <!-- boo-box for WordPress -->";
			echo "\n<script type=\"text/javascript\" src=\"http://stable.boo-box.com/\"></script>\n";
			$boobox_head_added = true;
		}	
	}
	// insert <script> on <head>
	add_action('wp_head', 'boo_head_script');
}

?>
