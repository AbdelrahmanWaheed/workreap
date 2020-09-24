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
global $current_user,$woocommerce;
?>
<div class="wt-moredetailsholder">
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
		<div class="row">
			<?php get_template_part('directory/front-end/templates/dashboard', 'statistics-messages');?>
			<?php 
				if( apply_filters('workreap_system_access','job_base') === true ){
					get_template_part('directory/front-end/templates/dashboard', 'statistics-proposals');
				}
			?>
			<?php get_template_part('directory/front-end/templates/dashboard', 'statistics-package-expiry');?>
			<?php get_template_part('directory/front-end/templates/dashboard', 'statistics-saved-items');?>
			<?php get_template_part('directory/front-end/templates/dashboard', 'pending-balance');?>
			<?php get_template_part('directory/front-end/templates/dashboard', 'available-balance');?>
			<?php 
				if( apply_filters('workreap_system_access','job_base') === true ){
					get_template_part('directory/front-end/templates/freelancer/dashboard', 'insights-jobs-totals');
				}
				if( apply_filters('workreap_system_access','service_base') === true ){
					get_template_part('directory/front-end/templates/freelancer/dashboard', 'insights-services');
				}
			?>
		</div>
	</div>
	
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
		<div class="row">
			<?php if( apply_filters('workreap_system_access','job_base') === true ){ ?>
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 float-left">
					<?php get_template_part('directory/front-end/templates/dashboard', 'insghts-ongoing-jobs');?>
				</div>
			<?php } ?>
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 float-left">
				<?php get_template_part('directory/front-end/templates/freelancer/dashboard', 'earning');?>
			</div>
		</div>
	</div>
</div>
<?php get_template_part('directory/front-end/templates/dashboard', 'package-detail');