<?php
if (!defined('FW'))
	die('Forbidden');
/**
 * @var $atts
 */

$image       = !empty($atts['image']['url']) ? $atts['image']['url'] : '';
$title       = !empty($atts['title']) ? $atts['title'] : '';
$sub_title   = !empty($atts['sub_title']) ? $atts['sub_title'] : '';
$desc  	     = !empty($atts['description']) ? $atts['description'] : '';
$text_align  = !empty($atts['text_align'] && $atts['text_align'] == 'left' ) ? esc_html__('float-left', 'workreap') : esc_html__('float-right', 'workreap');
$image_align 	= !empty($atts['image_align'] && $atts['image_align'] == 'left' ) ? esc_html__('float-left', 'workreap') : esc_html__('float-right', 'workreap');
$faqs        	= !empty($atts['faqs']) ? $atts['faqs'] : array();
$open        	= !empty($atts['open']) ? $atts['open'] : 'no';
$count_faq		= 0;
?>
<div class="wt-sc-how-it-works wt-haslayout">
	<?php 
		if( !empty( $image ) ||
			!empty( $title ) ||
			!empty( $sub_title ) ||
			!empty( $desc ) ||
			!empty( $faqs ) ) {
		?>
		<div class="wt-howitwork-hold wt-bgwhite wt-haslayout">
			<div class="wt-contentarticle">
				<div class="row">
					<div class="wt-starthiringhold wt-innerspace wt-haslayout">
					<?php 
						if( !empty( $title ) ||
							!empty( $sub_title ) ||
							!empty( $desc ) ||
							!empty( $faqs ) ) {
						?>
						<div class="col-12 col-sm-12 col-md-8 col-lg-7 <?php echo esc_attr($text_align); ?>">
							<div class="wt-starthiringcontent">
								<?php 
									if( !empty( $title ) || 
										!empty( $sub_title ) ||
										!empty( $desc ) ) { 
									?>
									<div class="wt-sectionhead">
										<?php if( !empty( $title ) || !empty( $sub_title ) ) { ?>
											<div class="wt-sectiontitle">
												<?php if( !empty( $title ) ) { ?>
													<h2><?php echo esc_html( $title ); ?></h2>
												<?php } ?>
												<?php if( !empty( $sub_title ) ) { ?>
													<span><?php echo esc_html( $sub_title ); ?></span>
												<?php } ?>
											</div>
										<?php } ?>
										<?php if( !empty( $desc ) ) { ?>
											<div class="wt-description">
												<?php echo wp_kses_post( wpautop( do_shortcode( $desc ) ) ); ?>
											</div>
										<?php } ?>
									</div>
								<?php } ?>
								<?php if( !empty( $faqs ) ) { 
									$counter	= 0;
									?>
									<ul class="wt-accordionhold accordion">
										<?php foreach( $faqs as $key => $faq ) { 
											$count_faq = rand(0,999);
											$title    = !empty( $faq['title'] ) ? $faq['title'] : '';
											$question = !empty( $faq['question'] ) ? $faq['question'] : '';
											$answer   = !empty( $faq['answer'] ) ? $faq['answer'] : '';
											$counter++; 
											$show = '';
										
											if( $open === 'yes' && $counter === 1 ) {
												$show = 'show';
											}
											?>
											<li>
												<?php if( !empty( $title ) ) { ?>
													<div class="wt-accordiontitle" id="heading-<?php echo intval($count_faq); ?>" data-toggle="collapse" data-target="#collapse-<?php echo intval($count_faq); ?>">
														<span><?php echo esc_html( $title ); ?></span>
													</div>
												<?php } ?>
												<?php if( !empty( $question ) || !empty( $answer ) ) { ?>
													<div class="wt-accordiondetails collapse <?php echo esc_attr( $show ); ?>" id="collapse-<?php echo intval( $count_faq ); ?>" aria-labelledby="heading-<?php echo intval( $count_faq ); ?>">
														<?php if( !empty( $question ) ) { ?>
															<div class="wt-title">
																<h3><?php echo esc_html( $question ); ?></h3>
															</div>
														<?php } ?>
														<?php if( !empty( $answer ) ) { ?>
															<div class="wt-description">
																<?php echo wp_kses_post( wpautop( do_shortcode( $answer ) ) ); ?>
															</div>
														<?php } ?>
													</div>
												<?php } ?>
											</li>
										<?php } ?>
									</ul>
								<?php } ?>
							</div>
						</div>
					<?php } ?>
					<?php if( !empty( $image ) ) { ?>
						<div class="col-12 col-sm-12 col-md-4 col-lg-5 <?php echo esc_attr($image_align); ?>">
							<div class="wt-howtoworkimg">
								<figure>
									<img src="<?php echo esc_url( $image ); ?>" alt="<?php esc_attr_e( 'Working', 'workreap' ); ?>">
								</figure>
							</div>
						</div>
					<?php } ?>
					</div>
				</div>
			</div>
		</div>
	<?php } ?>
</div>