<?php
/**
 *
 * The template part for displaying the dashboard.
 *
 * @package   Workreap
 * @author    Amentotech
 * @link      http://amentotech.com/
 * @since 1.0
 */
get_header();
global $current_user;
?>
<div class="wt-haslayout wt-moredetailsholder">
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
		<div class="row">
			<?php 
				get_template_part('directory/front-end/templates/dashboard', 'statistics-messages'); 
				if( apply_filters('workreap_system_access','job_base') === true ){
					get_template_part('directory/front-end/templates/dashboard', 'statistics-proposals');
				}
				get_template_part('directory/front-end/templates/dashboard', 'statistics-package-expiry');
				get_template_part('directory/front-end/templates/dashboard', 'statistics-saved-items');
			 	get_template_part('directory/front-end/templates/dashboard', 'available-balance');
				if( apply_filters('workreap_system_access','job_base') === true ){
					get_template_part('directory/front-end/templates/employer/dashboard', 'insights-jobs-totals');
				}
				if( apply_filters('workreap_system_access','service_base') === true ){
					get_template_part('directory/front-end/templates/employer/dashboard', 'insights-services');
				}
			?>
		</div>
	</div>
	<?php if( apply_filters('workreap_system_access','job_base') === true ){ ?>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 float-left">
					<?php get_template_part('directory/front-end/templates/dashboard', 'insghts-ongoing-jobs');?>
				</div>
			</div>
		</div>
	<?php } ?>
</div>
<?php get_template_part('directory/front-end/templates/dashboard', 'package-detail');?>