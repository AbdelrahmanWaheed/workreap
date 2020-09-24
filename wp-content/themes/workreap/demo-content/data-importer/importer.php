<?php
/**
 * @Booking Dummy data
 * All the functions will be in this file
 */



/**
 * @Data Importer
 * @return 
 */
if (!function_exists('workreap_update_users')) {

    function workreap_update_users() {
        $query_args = array(
			'role__in' => array('freelancers'),
		);
		$user_query = new WP_User_Query($query_args);

		$offset 			= get_option('gmt_offset') * intval(60) * intval(60);
		foreach ($user_query->results as $user) {
			$linked_profile			= workreap_get_linked_profile_id($user->ID);
			if( $linked_profile === 337 ) {
				$countries		 		= get_post_meta($linked_profile, '_country', true);
				if( !empty($countries) ) {
					$locations				= get_term_by('slug', $countries, 'locations');
					if(!empty($locations)) {
						wp_set_post_terms($linked_profile,$locations);
					}

				}
			}
        }
    }
}

/**
 * @Data Importer
 * @return 
 */
if (!function_exists('workreap_addon_services')) {

    function workreap_addon_services() {
        $query_args = array(
			'posts_per_page' 	  => -1,
			'post_type' 	 	  => array( 'micro-services' ),
			'post_status' 	 	  => array( 'publish' ),
			'ignore_sticky_posts' => 1
		);
		
		$services 		= get_posts($query_args);
		$employers 	= array();
		
		$data	=  array(
			0 => array( 'title'	=> 'I can design PSD and Logo as per your requirement',
					'description'	=> 'Additional 2 working days',
					'price'			=> rand(1,100)
				  ),
			1 => array( 'title'	=> 'I can design complete cooperate branding',
					'description'	=> 'I can deliver all work in 1 working day',
					'price'			=> rand(1,100)
				  ),
		);
		
		
		$exclude_data	= array();
		$i	= 0;
		
		
		foreach( $services as $key => $item ){
			$post_author_id = get_post_field( 'post_author', $item->ID );
			$i++;
			$post_id_array	= array();
			if( empty( $exclude_data[$post_author_id] ) ){
				for( $i=0; $i<3; $i++){
					if( !empty( $data[$i] ) ){
						$user_post = array(
							'post_title'    => wp_strip_all_tags( $data[$i]['title'] ),
							'post_excerpt'  => $data[$i]['description'],
							'post_author'   => $post_author_id,
							'post_type'     => 'addons-services',
							'post_status'	=> 'publish'
						);

						$post_id    		= wp_insert_post( $user_post );
						update_post_meta($post_id,'_price',$data[$i]['price']);
						$post_id_array[] 	= $post_id;
						
						$fw_options = array();
						$fw_options['price']         	= $data[$i]['price'];

						//Update User Profile
						fw_set_db_post_option($post_id, null, $fw_options);
					}
				}
				
				//update unyson meta
				$exclude_data[$post_author_id] = $post_id_array;
				update_post_meta($item->ID,'_addons',$post_id_array);
			} else{
				update_post_meta($item->ID,'_addons',$exclude_data[$post_author_id]);
			}
			
			
		}
    }
}


/**
 * @Assign Addresses
 * @return 
 */
if (!function_exists('workreap_update_adress')) {

    function workreap_update_adress() {
		$address	= array(
			0 => array(
				'address' => 'Larapinta Dr Alice Springs NT 0870 Australia',
				'latitude' => '-24.004758',
				'longitude' => '133.368101'
			),
			1 => array(
				'address' => '23 Multa Rd Haasts Bluff NT 0872 Australia',
				'latitude' => '-23.44757491',
				'longitude' => '131.8817496'
			),
			2 => array(
				'address' => 'Lot 39 Anmatjere NT 0872 Australia',
				'latitude' => '-21.24753228',
				'longitude' => '132.6098514'
			),
			3 => array(
				'address' => 'AB-58 John D\'Or Prairie, AB Canada',
				'latitude' => '58.49257244',
				'longitude' => '-115.1511812'
			),
			4 => array(
				'address' => '10009 99 St High Level, AB T0H 1Z0 Canada',
				'latitude' => '58.51683705',
				'longitude' => '-117.1313033'
			),
			5 => array(
				'address' => '9809 100 St High Level, AB T0H 1Z0 Canada',
				'latitude' => '58.51918433',
				'longitude' => '-117.130516'
			),
			6 => array(
				'address' => 'Unit 3, Paynes Lane Ind. Est 11 Paynes Ln Rugby CV21 2UH UK',
				'latitude' => '52.3749008',
				'longitude' => '-1.286473274'
			),
			7 => array(
				'address' => '12 Paynes Ln Rugby CV21 2UH UK',
				'latitude' => '52.37485332',
				'longitude' => '-1.285875142'
			),
			8 => array(
				'address' => '31 Bridget St Rugby CV21 2BH UK',
				'latitude' => '52.37378895',
				'longitude' => '-1.270516813'
			),
			9 => array(
				'address' => 'Gate Chaurai, Madhya Pradesh 480115 India',
				'latitude' => '22.05122291',
				'longitude' => '79.24777508'
			),
			10 => array(
				'address' => 'Barkhudar Bava Badasab Bava. Dargah Mahelaj, Gujarat 387530 India',
				'latitude' => '22.68149723',
				'longitude' => '72.59705007'
			),
			11 => array(
				'address' => 'Mahakali Temple Mahelaj, Gujarat 387530 India',
				'latitude' => '22.69013145',
				'longitude' => '72.6193285'
			),
			12 => array(
				'address' => 'Yenidoğan Mahallesi İstanbul Cd. 71200 Kırıkkale Merkez/Kırıkkale Turkey',
				'latitude' => '39.84194518',
				'longitude' => '33.5064742'
			),
			13 => array(
				'address' => 'Yaylacık Mahallesi 373. Sk. No:2 71100 Kırıkkale Merkez/Kırıkkale Turkey',
				'latitude' => '39.84041706',
				'longitude' => '33.49718705'
			),
			14 => array(
				'address' => 'Yaylacık Mahallesi 332. Sk. No:6 71100 Kırıkkale Merkez/Kırıkkale Turkey',
				'latitude' => '39.84002576',
				'longitude' => '33.49491388'
			),
			15 => array(
				'address' => 'Baqala Al Kamal Abu Dhabi United Arab Emirates',
				'latitude' => '23.6520499',
				'longitude' => '53.69937882'
			),
			16 => array(
				'address' => 'Western Souk & Mall Tarif - Liwa Rd Abu Dhabi United Arab Emirate',
				'latitude' => '23.64994923',
				'longitude' => '53.70384872'
			),
			17 => array(
				'address' => 'Madinat Zayed Adult Education Center for Female Abu Dhabi United Arab Emirates',
				'latitude' => '23.64359286',
				'longitude' => '53.70442271'
			),
			18 => array(
				'address' => 'Thomas Sherriff & Co Ltd Old Bongate Mill Bongate Jedburgh TD8 6DU UK',
				'latitude' => '55.48479672',
				'longitude' => '-2.547197342'
			),
			19 => array(
				'address' => 'Jed Tyre & Exhaust Centre Friars Burn/High St Jedburgh TD8 6AG UK',
				'latitude' => '55.47858594',
				'longitude' => '-2.555555105'
			),
			20 => array(
				'address' => 'Hassendean Station Cottage 2 Hassendean Station Hawick TD9 8PX UK',
				'latitude' => '55.47531162',
				'longitude' => '-2.716192603'
			),
			21 => array(
				'address' => 'Christmas City Gifts 609 S Nance Ave Minden, NE 68959',
				'latitude' => '40.49095681',
				'longitude' => '-98.95441532'
			),
			22 => array(
				'address' => 'Pioneer Aerial Applicators 886 W St Clair St Minden, NE 68959',
				'latitude' => '40.48940039',
				'longitude' => '-98.95992458'
			),
			23 => array(
				'address' => 'King Of Kars 801-899 S Brown Ave Minden, NE 68959',
				'latitude' => '40.48859871',
				'longitude' => '-98.95205498'
			),
			24 => array(
				'address' => 'Bethany Lutheran Church ELCA Minden, NE 68959',
				'latitude' => '40.49834879',
				'longitude' => '-98.94665837'
			),
			25 => array(
				'address' => 'Cooperative Producers, Inc. (CPI) 815 N Brown Ave Minden, NE 68959',
				'latitude' => '40.50277869',
				'longitude' => '-98.95281672'
			),


			26 => array(
				'address' => 'Company Ltd Pextenement Farm Eastwood Todmorden OL14 8RW UK',
				'latitude' => '53.72644924',
				'longitude' => '-2.06485033'
			),
			27 => array(
				'address' => 'Stoodley Ln Todmorden OL14 6HA UK',
				'latitude' => '53.72167556',
				'longitude' => '-2.057640553'
			),
			28 => array(
				'address' => '953R+GG Tennant Creek, Northern Territory, Australia',
				'latitude' => '-19.6469653',
				'longitude' => '134.190717'
			),
			29 => array(
				'address' => '952R+93 Tennant Creek, Northern Territory, Australia',
				'latitude' => '-19.6493549',
				'longitude' => '134.1912816'
			),
			30 => array(
				'address' => 'Tennant Creek NT 0860 Australia',
				'latitude' => '-19.64644741',
				'longitude' => '134.1859657'
			),
			31 => array(
				'address' => '12 Scott St Tennant Creek NT 0860 Australia',
				'latitude' => '-19.6423166',
				'longitude' => '134.1877509'
			),
			32 => array(
				'address' => '42 Noble St Tennant Creek NT 0860 Australia',
				'latitude' => '-19.639561',
				'longitude' => '134.1887663'
			),
			33 => array(
				'address' => '98 Perry Dr Tennant Creek NT 0860 Australia',
				'latitude' => '-19.6369301',
				'longitude' => '134.202274'
			),
			34 => array(
				'address' => '49GW+RW Black Lake, Division No. 18, Unorganized, SK, Canada',
				'latitude' => '59.127494',
				'longitude' => '-105.6047788'
			),
			35 => array(
				'address' => '7536+QQ Stony Rapids, Division No. 18, Unorganized, SK, Canada',
				'latitude' => '59.2563157',
				'longitude' => '-105.8366193'
			),
			36 => array(
				'address' => 'G87P+PG Shustoke, Birmingham, UK',
				'latitude' => '52.5145808',
				'longitude' => '-1.6668839'
			),
			37 => array(
				'address' => 'G88M+2C Shustoke, Birmingham, UK',
				'latitude' => '52.5145808',
				'longitude' => '-1.6668839'
			),
			38 => array(
				'address' => 'G5CQ+69 Birmingham, UK',
				'latitude' => '52.5208604',
				'longitude' => '-1.8121359'
			),
			39 => array(
				'address' => 'Delhi Cantonment New Delhi, Delhi 110010 India',
				'latitude' => '28.594825',
				'longitude' => '77.1222426'
			),
			40 => array(
				'address' => 'New Delhi, Delhi 110064 India',
				'latitude' => '28.6279036',
				'longitude' => '77.1159461'
			),
			41 => array(
				'address' => 'Hari Enclave, Hari Nagar Delhi, 110064',
				'latitude' => '28.6299777',
				'longitude' => '77.110048'
			),
			42 => array(
				'address' => 'MGRW+VH Talas, Kayseri Province, Turkey',
				'latitude' => '38.692134',
				'longitude' => '35.5458668'
			),
			43 => array(
				'address' => 'MGH5+J7 Kayseri, Kayseri Province, Turkey',
				'latitude' => '38.6794451',
				'longitude' => '35.5071671'
			),
			44 => array(
				'address' => 'MGJ4+WV Kayseri, Kayseri Province, Turkey',
				'latitude' => '38.6824958',
				'longitude' => '35.5061291'
			),
			45 => array(
				'address' => '76QH+5P Asab, Abu Dhabi - United Arab Emirates',
				'latitude' => '23.28797',
				'longitude' => '54.228962'
			),
			46 => array(
				'address' => '2P5J+CG Tarif, Abu Dhabi - United Arab Emirates',
				'latitude' => '24.0087851',
				'longitude' => '53.7313825'
			),
			47 => array(
				'address' => '2P5J+R6 Tarif, Abu Dhabi - United Arab Emirates',
				'latitude' => '24.0089713',
				'longitude' => '53.7299663'
			),
			48 => array(
				'address' => 'WW5P+C4 Thurstonfield, Carlisle, UK',
				'latitude' => '54.912065',
				'longitude' => '-3.062308'
			),
			49 => array(
				'address' => '938F+R3 Sanquhar, UK',
				'latitude' => '55.3680072',
				'longitude' => '-3.931581'
			),
			50 => array(
				'address' => '939G+FC Sanquhar, UK',
				'latitude' => '55.3680072',
				'longitude' => '-3.931581'
			),
			51 => array(
				'address' => 'Q774+FJ Dallas, Texas, USA',
				'latitude' => '32.7634291',
				'longitude' => '-96.7432738'
			),
			52 => array(
				'address' => '3526 York St Dallas, TX 75210 USA',
				'latitude' => '32.7637382',
				'longitude' => '-96.7467076'
			),
			53 => array(
				'address' => '3308 Reed Ln Dallas, TX 75215 USA',
				'latitude' => '32.7634489',
				'longitude' => '-96.7512618'
			),
			54 => array(
				'address' => '3308 Reed Ln Dallas, TX 75215 USA',
				'latitude' => '32.7634489',
				'longitude' => '-96.7512618'
			),
			55 => array(
				'address' => 'Q66Q+PH Dallas, Texas, USA',
				'latitude' => '32.7617144',
				'longitude' => '-96.7609955'
			),
			56 => array(
				'address' => 'V5W7+WR Carn, Londonderry, UK',
				'latitude' => '54.8943277',
				'longitude' => '-6.8630259'
			),
			57 => array(
				'address' => 'V5W7+WR Carn, Londonderry, UK',
				'latitude' => '54.8943277',
				'longitude' => '-6.8630259'
			),
			58 => array(
				'address' => '83GG+8C Titjikala, Northern Territory, Australia',
				'latitude' => '-24.6762124',
				'longitude' => '134.0724585'
			),
			59 => array(
				'address' => '83GG+8C Titjikala, Northern Territory, Australia',
				'latitude' => '-24.6762124',
				'longitude' => '134.0724585'
			),
		);
		
		$query_args = array(
			'posts_per_page' 	  => -1,
			'post_type' 	 	  => 'projects',
			'post_status' 	 	  => array( 'publish','hired','cancelled'),
			'ignore_sticky_posts' => 1
		);

		$project_data = get_posts($query_args);
		$counter	= 0;
		foreach( $project_data as $key => $project ){
			$counter++;
			if( $project->ID ){
				$fw_options	= fw_get_db_post_option($project->ID, null);
				$fw_options['address'] 	 = $address[$counter]['address'];
				$fw_options['longitude'] = $address[$counter]['longitude'];
				$fw_options['latitude']  = $address[$counter]['latitude'];
				$fw_options['expiry_date']  = '2020/01/23';
				
				update_post_meta($project->ID, '_longitude', $address[$counter]['longitude']);
				update_post_meta($project->ID, '_latitude', $address[$counter]['latitude']);
				
				fw_set_db_post_option($project->ID, null, $fw_options);
				
				
				/*$start = strtotime("25 December 2019");
				$end = strtotime("13 July 2020");
				$timestamp = mt_rand($start, $end);
				$deadline = date("Y/m/d", $timestamp);
				$types	= array('onsite','partial_onsite','remote');
				$randIndex = array_rand($types);
				$fw_options['deadline']         	 = $deadline;
				$fw_options['job_option']    		 = $types[$randIndex];
				update_post_meta($project->ID, 'deadline', $deadline);
				update_post_meta($project->ID, '_job_option', $types[$randIndex]);*/
			}
		}
		
	}
	
	//workreap_update_adress();
}

/**
 * @Assign authors to projects
 * @return 
 */
if (!function_exists('workreap_update_project_authors')) {

    function workreap_update_project_authors() {
        $query_args = array(
			'posts_per_page' 	  => -1,
			'post_type' 	 	  => 'projects',
			'post_status' 	 	  => array( 'publish','hired','cancelled'),
			'ignore_sticky_posts' => 1
		);

		$project_data = get_posts($query_args);
		
		$query_args = array(
			'posts_per_page' 	  => -1,
			'post_type' 	 	  => array( 'employers' ),
			'post_status' 	 	  => array( 'publish' ),
			'ignore_sticky_posts' => 1
		);
		
		$users 		= get_posts($query_args);
		$employers 	= array();

		foreach( $users as $key => $user ){
			$employers[]	= $user->ID;
		}

		foreach( $project_data as $key => $project ){
			$k 			= array_rand($employers);
			$user_id 	= $employers[$k];
			$author_id 	= workreap_get_linked_profile_id($user_id, 'post');
			
			$arg = array(
				'ID' => $project->ID,
				'post_author' => $author_id,
			);

			wp_update_post( $arg );
			
			if (function_exists('fw_get_db_post_option')) {                               
                $project_new   = fw_get_db_post_option($project->ID);
				if( $project->ID % 2 == 0){ 
					$rate	= rand(1,50);
					$hours	= rand(1,50);
					$project_new['project_type']['gadget']	= 'hourly';
					$project_new['project_type']['hourly']['hourly_rate']		= $rate;
					$project_new['project_type']['hourly']['estimated_hours']	= $hours;
					$project_new['project_type']['fixed']['project_cost']		= 0;
					delete_post_meta($project->ID,'_project_cost');
					update_post_meta($project->ID,'_hourly_rate',$rate);
					update_post_meta($project->ID,'_project_type','hourly');
					fw_set_db_post_option($project->ID, null, $project_new);
				} else{
					delete_post_meta($project->ID,'_hourly_rate');
				}
            }
		}
    }
	
	//workreap_update_project_authors();
}

/**
 * @Assign authors to service
 * @return 
 */
if (!function_exists('workreap_update_service_authors')) {

    function workreap_update_service_authors() {
        $query_args = array(
			'posts_per_page' 	  => -1,
			'post_type' 	 	  => 'micro-services',
			'post_status' 	 	  => array( 'publish','hired','cancelled'),
			'ignore_sticky_posts' => 1
		);

		$project_data = get_posts($query_args);
		
		$query_args = array(
			'posts_per_page' 	  => -1,
			'post_type' 	 	  => array( 'freelancers' ),
			'post_status' 	 	  => array( 'publish' ),
			'ignore_sticky_posts' => 1
		);
		
		$users 		= get_posts($query_args);
		$employers 	= array();

		foreach( $users as $key => $user ){
			$employers[]	= $user->ID;
		}

		foreach( $project_data as $key => $project ){
			$k 			= array_rand($employers);
			$user_id 	= $employers[$k];
			$author_id 	= workreap_get_linked_profile_id($user_id, 'post');
			$arg = array(
				'ID' => $project->ID,
				'post_author' => $author_id,
			);

			wp_update_post( $arg );
		}
    }
}


/**
 * @Assign authors to freelancers and employers
 * @return 
 */
if (!function_exists('workreap_update_authors')) {

    function workreap_update_authors() {
        $query_args = array(
			'posts_per_page' 	  => -1,
			'post_type' 	 	  => array( 'freelancers', 'employers' ),
			'post_status' 	 	  => array( 'publish' ),
			'ignore_sticky_posts' => 1
		);
		
		$users = get_posts($query_args);
		foreach( $users as $key => $user ){
			$linked_profile = get_post_meta($user->ID, '_linked_profile', true);
			if( !empty( $linked_profile ) ){
				$arg = array(
					'ID' => $user->ID,
					'post_author' => $linked_profile,
				);
				
				
				wp_update_post( $arg );
				
				update_post_meta($user->ID, '_rating_filter', 0);
				update_post_meta($user->ID, '_featured_timestamp', 0); 
				update_post_meta($user->ID, '_is_verified', 'yes'); 
				update_post_meta($user->ID, '_profile_blocked', 'off'); 
			}
		}
    }

}

/**
 * @Data Importer languages
 * @return 
 */
if (!function_exists('workreap_import_languages')) {
    function workreap_import_languages() {
        $langs	= workreap_prepare_languages();
		foreach ( $langs as $key => $lang ) {
			$args = array('slug' => $key,'description'=> '','parent'=> 0);
			wp_insert_term( $lang, 'languages', $args );
        }
    }
}

/**
 * Creat dummy projects
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */

if(!function_exists('workreap_dummy_projects') ) {
	function workreap_dummy_projects() {
		$project_title	= array("Internet Developer","Intranet Developer","Web Content Developer","Web Designer","Web Developer","Applications Programmer","Computer Language Coder","Computer Programmer","Junior Software Developer","Mainframe Programmer","Systems Programmer","Application Integration Engineer","Applications Developer","Computer Applications Developer","Computer Applications Engineer","Database Developer","Software Applications Architect","Software Applications Designer","Software Applications Engineer","Catalogue Illustrator","Graphic Artist","Graphic Designer","Visual Designer","C++ Professor","Computer Information Systems Professor","Computer Programming Professor","Information Systems Professor","Information Technology Professor","IT Professor","Java Programming Professor","Ecology Professor","Environmental Conservation Professor","Forest Biometrics Professor","Forest Ecology Professor","Forest Management Professor","Forest Pathology Professor","Forest Resources Professor","Forest Technology Professor","Silviculture Professor","Timber Management Professor","Wildlife Conservation Professor");

		$content 			= get_post_field('post_content', 88);
		$proposed_content	= do_shortcode($content);
		
		$skills			= workreap_get_taxonomy_array('skills');
		$array_skill	= array();
		foreach( $skills as $skill ) {
			$array_skill[]	= $skill->term_id;
		}

		
		$project_cat			= workreap_get_taxonomy_array('project_cat');
		$array_project_cat		= array();
		foreach( $project_cat as $project_cat ) {
			$array_project_cat[]	= $project_cat->term_id;
		}

		
		$languages			= workreap_get_taxonomy_array('languages');
		$array_languages	= array();
		foreach( $languages as $language ) {
			$array_languages[]	= $language->term_id;
		}
		
		$locations			= workreap_get_taxonomy_array('locations');
		$array_locations	= array();
		foreach( $locations as $location ) {
			$array_locations[$location->slug]	= $location->slug;
		}

		$lists               		= worktic_job_duration_list();
		$english_level_list      	= worktic_english_level_list();
		$freelancer_level_list   	= worktic_freelancer_level_list();
		$project_level_list   		= workreap_get_project_level();
		$count = 1;
		
		foreach( $project_title as $key => $title ){	
			$count++;
			$proposal_post = array(
				'post_title'    => wp_strip_all_tags( $title ), //proposal title
				'post_status'   => 'publish',
				'post_content'  => $proposed_content,
				'post_author'   => 1,
				'post_type'     => 'projects',
			);

			$post_id    = wp_insert_post( $proposal_post );
			
			if( !empty($post_id) ){
				if( $count%2 == 0) {
					$project_cost			= rand(10,900);
				} else {
					$project_cost			= rand(100,9000);
				}
				
				if( !empty($array_skill) ){
					wp_set_post_terms( $post_id, worket_get_random_ids($array_skill,2,5), 'skills' );
				}
				
				if( !empty($array_project_cat) ){
					wp_set_post_terms( $post_id, worket_get_random_ids($array_project_cat,2,8), 'project_cat' );
				}

				if( !empty($array_languages) ){
					wp_set_post_terms( $post_id, worket_get_random_ids($array_languages,2,5), 'languages' );
				}
				
				$project_type		= 'fixed';
				$expiry_string		= '0';
				$show_attachments	= 'on';
				
				$project_level		= worket_get_random_key($project_level_list);
				$project_duration	= worket_get_random_key($lists);
				$english_level		= worket_get_random_key($english_level_list);
				$freelancer_level	= worket_get_random_key($freelancer_level_list);
				$country			= worket_get_random_key($array_locations);
				
				$job_files			= array(
											'0'	=> array(
													'attachment_id' => 121, 
													'url' 			=> '//amentotech.com/projects/workreap/wp-content/uploads/2019/03/WordPress-customization.pdf'
												),
											'1'	=> array(
													'attachment_id' => 120, 
													'url' 			=> '//amentotech.com/projects/workreap/wp-content/uploads/2019/03/How-to-run-mysql-command-in-database.docx'
												)
										);
				
				//update
				update_post_meta($post_id, '_expiry_string', $expiry_string);
				update_post_meta($post_id, '_featured_job_string', 0);
				update_post_meta($post_id, '_project_level', $project_level);
				update_post_meta($post_id, '_project_type', $project_type);
				update_post_meta($post_id, '_project_duration', $project_duration);
				update_post_meta($post_id, '_english_level', $english_level);
				update_post_meta($post_id, '_freelancer_level', $freelancer_level);
				update_post_meta($post_id, '_project_cost', $project_cost);


				$project_data							= array(); 
				$project_data['gadget']					= $project_type;
				$project_data['hourly']['hourly_rate']	= '';
				$project_data['fixed']['project_cost']	= $project_cost;


				//update location
				$address    = '';
				$latitude   = '';
				$longitude  = '';

				update_post_meta($post_id, '_address', '');
				update_post_meta($post_id, '_country', $country);
				update_post_meta($post_id, '_latitude', '');
				update_post_meta($post_id, '_longitude', '');


				//Set country for unyson
				$locations = get_term_by( 'slug', $country, 'locations' );
				$location = array();
				if( !empty( $locations ) ){
					$location[0] = $locations->term_id;

					if( !empty( $location ) ){
						wp_set_post_terms( $post_id, $location, 'locations' );
					}

				}

				//update unyson meta
				$fw_options = array();
				$fw_options['project_level']         = $project_level;
				$fw_options['project_type']          = $project_data;
				$fw_options['project_duration']      = $project_duration;
				$fw_options['english_level']         = $english_level;
				$fw_options['freelancer_level']      = $freelancer_level;
				$fw_options['show_attachments']      = $show_attachments;
				$fw_options['project_documents']     = $job_files;
				$fw_options['address']            	 = $address;
				$fw_options['longitude']          	 = $longitude;
				$fw_options['latitude']           	 = $latitude;
				$fw_options['country']            	 = $location;


				//Update User Profile
				fw_set_db_post_option($post_id, null, $fw_options);

			}
		}
	}

}

/**
 * Get Random ID
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if(!function_exists('workreap_get_tagline') ) {
	function worket_get_random_ids($scale,$start,$end){
		shuffle($scale);
		$random_chords = array_slice($scale, 0, rand($start, $end));

		return $random_chords;
	}
}

/**
 * Get Random Key
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if(!function_exists('worket_get_random_key') ) {
	function worket_get_random_key($array){
		$key	= array_rand($array);
	   return $key;
	}
}