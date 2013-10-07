<?php
/**
 * WooCommerce CSV Log class 
 */
class WC_CSV_Log {
	
	var $log;
	
	public function __construct() { 
		$this->log = array();
	}
	
	function add( $message, $echo = false ) {
		$this->log[] = $message;
		if ( $echo ) {
			echo $message . '<br/>';
			@ob_flush();
			@flush();
		}
	}
	
	function show_log() {
		
		?>
		<div class="postbox" style="margin:1em 0 0 0;">
			<div class="inside">
				<textarea id="installation_log" rows="10" cols="30" style="width: 100%; height: 200px;" readonly="readonly"><?php
					foreach ($this->log as $log) {
						echo $log . "\n";
					}
				?></textarea>
			</div>
		</div>
		<?php
		
		$this->log = array();
	}
	
}