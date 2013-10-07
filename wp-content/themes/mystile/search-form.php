<?php
// File Security Check
if ( ! empty( $_SERVER['SCRIPT_FILENAME'] ) && basename( __FILE__ ) == basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
    die ( 'You do not have sufficient permissions to access this page!' );
}
?>
<?php
/**
 * Search Form Template
 *
 * This template is a customised search form.
 *
 * @package WooFramework
 * @subpackage Template
 */
?>
<div class="search_main fix">
    <form method="get" class="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>" >
    	<label class="screen-reader-text" for="s"><?php _e('Buscar:', 'woothemes'); ?></label>
        <input type="text" class="field s" name="s" placeholder="<?php esc_attr_e( 'Digite aqui...', 'woothemes' ); ?>" />
        <input type="submit" id="searchsubmit" value="<?php esc_attr_e( 'Buscar', 'woothemes' ); ?>">
    </form>    
</div><!--/.search_main-->