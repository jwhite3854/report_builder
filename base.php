<?php

/*
Plugin Name: jWeb Report Builder
Description:  jWeb Report Builder allows access to MySQL tables to join and build reports
Version: 1.0
Author: jWeb
Author URI: http://jwebmedia.com/
*/

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class JWEB_Base 
{

	public function __construct() {}
	
	public static function load($table, $id, $primary_id = 'id')
	{
		global $wpdb;
		$sql = $wpdb->prepare("SELECT * FROM ".$table." WHERE ".$primary_id." = %d LIMIT 1", $id);
		return $wpdb->get_row($sql);
	}
	
	protected static function gen_select($table, $filters = array(), $order_by = array(), $single = false)
	{
		global $wpdb;
		$sql = "SELECT * FROM " . $table;
		$first_col = 1;
		reset ($filters);
		while (list ($key, $data) = each ($filters)) {
			if (preg_match("/^_$table\_/",$key)) {
				$key = substr($key, (2+strlen($table)));
			}
			if (!isset($reserved_words[$key]) && !preg_match("/\b_/",$key)) {
				if ($first_col) {
					$first_col=0;
					$sql = $sql . " WHERE ";
				} else {
					$sql = $sql . " AND ";
				}
				if (strpos("$key","|")) {
					list($key, $data) = special($key, $data);
				}
				$sql = $sql . $key . "=";
				//$sql = $sql . "'" . mysqli_real_escape_string($data) . "'";
				$sql = $sql . "'" . jw_sanitize_data($data) . "'";
			}
		}
		if ($single) {
			$results = $wpdb->get_row($sql);
		} else {
			$results = $wpdb->get_results($sql);
		}
		
		return $results;
	}
	
	private static function clear_reserved($varset, $primary_id = null) {
		unset($varset['submit']);
		unset($varset['action']);
		unset($varset[$primary_id]);
		return $varset;
	}
	
	protected static function gen_insert($table, $varset) {
		global $wpdb;
		
		$varset = self::clear_reserved($varset);
		$varset["created_at"] = current_time('mysql');
		$varset["updated_at"] = current_time('mysql');
		$varset["created_by"] = get_current_user_id();
		$varset["updated_by"] = get_current_user_id();
		
		$wpdb->insert( $table, $varset );
		return $wpdb->insert_id;
	}
		
	protected static function gen_update ($table, $varset, $id, $primary_id = 'id') {
		global $wpdb;
		$varset = self::clear_reserved($varset);
		$varset["updated_at"] = current_time('mysql');
		$varset["updated_by"] = get_current_user_id();
		$results = $wpdb->update( $table, $varset, array( 'id' => $id ) );		
		return $results;
	}

	protected static function gen_delete ($table, $id, $primary_id = 'id') {
		global $wpdb;
		$results = $wpdb->delete( $table, array( $primary_id => $id ) );
		return $results;
	}
}

class JWEB_Constants
{

	public static function yes_no()
	{
		return array(
			'' => '---',
			'1' => 'Yes',
			'0' => 'No'
		);
	}
	
	public static function get_all_countries()
	{
		$countries = array( 
			array( "value" => null, "label" => '' ),
			array( "value" => 'US', "label" => 'United States' ),
			array( "value" => 'GB', "label" => 'United Kingdom' ),
			array( "value" => 'FR', "label" => 'France' ),
			array( "value" => 'IT', "label" => 'Italy' ),
			array( "value" => 'ES', "label" => 'Spain' ),
			array( "value" => 'CA', "label" => 'Canada' ),
			array( "value" => 'AC', "label" => 'Ascension' ),
			array( "value" => 'AF', "label" => 'Afghanistan' ),
			array( "value" => 'AX', "label" => 'Aland Islands' ),
			array( "value" => 'AL', "label" => 'Albania' ),
			array( "value" => 'DZ', "label" => 'Algeria' ),
			array( "value" => 'AS', "label" => 'American Samoa' ),
			array( "value" => 'AD', "label" => 'Andorra' ),
			array( "value" => 'AO', "label" => 'Angola' ),
			array( "value" => 'AI', "label" => 'Anguilla' ),
			array( "value" => 'AQ', "label" => 'Antarctica' ),
			array( "value" => 'AG', "label" => 'Antigua And Barbuda' ),
			array( "value" => 'AR', "label" => 'Argentina' ),
			array( "value" => 'AM', "label" => 'Armenia' ),
			array( "value" => 'AN', "label" => 'Netherlands Antilles' ),
			array( "value" => 'AW', "label" => 'Aruba' ),
			array( "value" => 'AU', "label" => 'Australia' ),
			array( "value" => 'AT', "label" => 'Austria' ),
			array( "value" => 'AZ', "label" => 'Azerbaijan' ),
			array( "value" => 'BS', "label" => 'Bahamas' ),
			array( "value" => 'BH', "label" => 'Bahrain' ),
			array( "value" => 'BD', "label" => 'Bangladesh' ),
			array( "value" => 'BB', "label" => 'Barbados' ),
			array( "value" => 'BY', "label" => 'Belarus' ),
			array( "value" => 'BE', "label" => 'Belgium' ),
			array( "value" => 'BZ', "label" => 'Belize' ),
			array( "value" => 'BJ', "label" => 'Benin' ),
			array( "value" => 'BM', "label" => 'Bermuda' ),
			array( "value" => 'BT', "label" => 'Bhutan' ),
			array( "value" => 'BO', "label" => 'Bolivia' ),
			array( "value" => 'BA', "label" => 'Bosnia And Herzegovina' ),
			array( "value" => 'BW', "label" => 'Botswana' ),
			array( "value" => 'BV', "label" => 'Bouvet Island' ),
			array( "value" => 'BR', "label" => 'Brazil' ),
			array( "value" => 'IO', "label" => 'British Indian Ocean Territory' ),
			array( "value" => 'BN', "label" => 'Brunei Darussalam' ),
			array( "value" => 'BG', "label" => 'Bulgaria' ),
			array( "value" => 'BF', "label" => 'Burkina Faso' ),
			array( "value" => 'BI', "label" => 'Burundi' ),
			array( "value" => 'KH', "label" => 'Cambodia' ),
			array( "value" => 'CM', "label" => 'Cameroon' ),
			array( "value" => 'CA', "label" => 'Canada' ),
			array( "value" => 'CV', "label" => 'Cape Verde' ),
			array( "value" => 'KY', "label" => 'Cayman Islands' ),
			array( "value" => 'CF', "label" => 'Central African Republic' ),
			array( "value" => 'TD', "label" => 'Chad' ),
			array( "value" => 'CL', "label" => 'Chile' ),
			array( "value" => 'CN', "label" => 'China' ),
			array( "value" => 'CX', "label" => 'Christmas Island' ),
			array( "value" => 'CC', "label" => 'Cocos (Keeling) Islands' ),
			array( "value" => 'CO', "label" => 'Colombia' ),
			array( "value" => 'KM', "label" => 'Comoros' ),
			array( "value" => 'CG', "label" => 'Congo' ),
			array( "value" => 'CD', "label" => 'Congo, Democratic Republic' ),
			array( "value" => 'CK', "label" => 'Cook Islands' ),
			array( "value" => 'CR', "label" => 'Costa Rica' ),
			array( "value" => 'CI', "label" => 'Cote D\'Ivoire' ),
			array( "value" => 'HR', "label" => 'Croatia' ),
			array( "value" => 'CU', "label" => 'Cuba' ),
			array( "value" => 'CY', "label" => 'Cyprus' ),
			array( "value" => 'CZ', "label" => 'Czech Republic' ),
			array( "value" => 'DK', "label" => 'Denmark' ),
			array( "value" => 'DJ', "label" => 'Djibouti' ),
			array( "value" => 'DM', "label" => 'Dominica' ),
			array( "value" => 'DO', "label" => 'Dominican Republic' ),
			array( "value" => 'EC', "label" => 'Ecuador' ),
			array( "value" => 'EG', "label" => 'Egypt' ),
			array( "value" => 'SV', "label" => 'El Salvador' ),
			array( "value" => 'GQ', "label" => 'Equatorial Guinea' ),
			array( "value" => 'ER', "label" => 'Eritrea' ),
			array( "value" => 'EE', "label" => 'Estonia' ),
			array( "value" => 'ET', "label" => 'Ethiopia' ),
			array( "value" => 'FK', "label" => 'Falkland Islands (Malvinas)' ),
			array( "value" => 'FO', "label" => 'Faroe Islands' ),
			array( "value" => 'FJ', "label" => 'Fiji' ),
			array( "value" => 'FI', "label" => 'Finland' ),
			array( "value" => 'FR', "label" => 'France' ),
			array( "value" => 'GF', "label" => 'French Guiana' ),
			array( "value" => 'PF', "label" => 'French Polynesia' ),
			array( "value" => 'TF', "label" => 'French Southern Territories' ),
			array( "value" => 'GA', "label" => 'Gabon' ),
			array( "value" => 'GM', "label" => 'Gambia' ),
			array( "value" => 'GE', "label" => 'Georgia' ),
			array( "value" => 'DE', "label" => 'Germany' ),
			array( "value" => 'GH', "label" => 'Ghana' ),
			array( "value" => 'GI', "label" => 'Gibraltar' ),
			array( "value" => 'GR', "label" => 'Greece' ),
			array( "value" => 'GL', "label" => 'Greenland' ),
			array( "value" => 'GD', "label" => 'Grenada' ),
			array( "value" => 'GP', "label" => 'Guadeloupe' ),
			array( "value" => 'GU', "label" => 'Guam' ),
			array( "value" => 'GT', "label" => 'Guatemala' ),
			array( "value" => 'GG', "label" => 'Guernsey' ),
			array( "value" => 'GN', "label" => 'Guinea' ),
			array( "value" => 'GW', "label" => 'Guinea-Bissau' ),
			array( "value" => 'GY', "label" => 'Guyana' ),
			array( "value" => 'HT', "label" => 'Haiti' ),
			array( "value" => 'HM', "label" => 'Heard Island & Mcdonald Islands' ),
			array( "value" => 'VA', "label" => 'Holy See (Vatican City State)' ),
			array( "value" => 'HN', "label" => 'Honduras' ),
			array( "value" => 'HK', "label" => 'Hong Kong' ),
			array( "value" => 'HU', "label" => 'Hungary' ),
			array( "value" => 'IS', "label" => 'Iceland' ),
			array( "value" => 'IN', "label" => 'India' ),
			array( "value" => 'ID', "label" => 'Indonesia' ),
			array( "value" => 'IR', "label" => 'Iran, Islamic Republic Of' ),
			array( "value" => 'IQ', "label" => 'Iraq' ),
			array( "value" => 'IE', "label" => 'Ireland' ),
			array( "value" => 'IM', "label" => 'Isle Of Man' ),
			array( "value" => 'IL', "label" => 'Israel' ),
			array( "value" => 'IT', "label" => 'Italy' ),
			array( "value" => 'JM', "label" => 'Jamaica' ),
			array( "value" => 'JP', "label" => 'Japan' ),
			array( "value" => 'JE', "label" => 'Jersey' ),
			array( "value" => 'JO', "label" => 'Jordan' ),
			array( "value" => 'KZ', "label" => 'Kazakhstan' ),
			array( "value" => 'KE', "label" => 'Kenya' ),
			array( "value" => 'KI', "label" => 'Kiribati' ),
			array( "value" => 'KR', "label" => 'Korea' ),
			array( "value" => 'KW', "label" => 'Kuwait' ),
			array( "value" => 'KG', "label" => 'Kyrgyzstan' ),
			array( "value" => 'LA', "label" => 'Lao People\'s Democratic Republic' ),
			array( "value" => 'LV', "label" => 'Latvia' ),
			array( "value" => 'LB', "label" => 'Lebanon' ),
			array( "value" => 'LS', "label" => 'Lesotho' ),
			array( "value" => 'LR', "label" => 'Liberia' ),
			array( "value" => 'LY', "label" => 'Libyan Arab Jamahiriya' ),
			array( "value" => 'LI', "label" => 'Liechtenstein' ),
			array( "value" => 'LT', "label" => 'Lithuania' ),
			array( "value" => 'LU', "label" => 'Luxembourg' ),
			array( "value" => 'MO', "label" => 'Macao' ),
			array( "value" => 'MK', "label" => 'Macedonia' ),
			array( "value" => 'MG', "label" => 'Madagascar' ),
			array( "value" => 'MW', "label" => 'Malawi' ),
			array( "value" => 'MY', "label" => 'Malaysia' ),
			array( "value" => 'MV', "label" => 'Maldives' ),
			array( "value" => 'ML', "label" => 'Mali' ),
			array( "value" => 'MT', "label" => 'Malta' ),
			array( "value" => 'MH', "label" => 'Marshall Islands' ),
			array( "value" => 'MQ', "label" => 'Martinique' ),
			array( "value" => 'MR', "label" => 'Mauritania' ),
			array( "value" => 'MU', "label" => 'Mauritius' ),
			array( "value" => 'YT', "label" => 'Mayotte' ),
			array( "value" => 'MX', "label" => 'Mexico' ),
			array( "value" => 'FM', "label" => 'Micronesia, Federated States Of' ),
			array( "value" => 'MD', "label" => 'Moldova' ),
			array( "value" => 'MC', "label" => 'Monaco' ),
			array( "value" => 'MN', "label" => 'Mongolia' ),
			array( "value" => 'ME', "label" => 'Montenegro' ),
			array( "value" => 'MS', "label" => 'Montserrat' ),
			array( "value" => 'MA', "label" => 'Morocco' ),
			array( "value" => 'MZ', "label" => 'Mozambique' ),
			array( "value" => 'MM', "label" => 'Myanmar' ),
			array( "value" => 'NA', "label" => 'Namibia' ),
			array( "value" => 'NR', "label" => 'Nauru' ),
			array( "value" => 'NP', "label" => 'Nepal' ),
			array( "value" => 'NL', "label" => 'Netherlands' ),
			array( "value" => 'AN', "label" => 'Netherlands Antilles' ),
			array( "value" => 'NC', "label" => 'New Caledonia' ),
			array( "value" => 'NZ', "label" => 'New Zealand' ),
			array( "value" => 'NI', "label" => 'Nicaragua' ),
			array( "value" => 'NE', "label" => 'Niger' ),
			array( "value" => 'NG', "label" => 'Nigeria' ),
			array( "value" => 'NU', "label" => 'Niue' ),
			array( "value" => 'NF', "label" => 'Norfolk Island' ),
			array( "value" => 'MP', "label" => 'Northern Mariana Islands' ),
			array( "value" => 'NO', "label" => 'Norway' ),
			array( "value" => 'OM', "label" => 'Oman' ),
			array( "value" => 'PK', "label" => 'Pakistan' ),
			array( "value" => 'PW', "label" => 'Palau' ),
			array( "value" => 'PS', "label" => 'Palestine' ),
			array( "value" => 'PA', "label" => 'Panama' ),
			array( "value" => 'PG', "label" => 'Papua New Guinea' ),
			array( "value" => 'PY', "label" => 'Paraguay' ),
			array( "value" => 'PE', "label" => 'Peru' ),
			array( "value" => 'PH', "label" => 'Philippines' ),
			array( "value" => 'PN', "label" => 'Pitcairn' ),
			array( "value" => 'PL', "label" => 'Poland' ),
			array( "value" => 'PT', "label" => 'Portugal' ),
			array( "value" => 'PR', "label" => 'Puerto Rico' ),
			array( "value" => 'QA', "label" => 'Qatar' ),
			array( "value" => 'RE', "label" => 'Reunion' ),
			array( "value" => 'RO', "label" => 'Romania' ),
			array( "value" => 'RU', "label" => 'Russian Federation' ),
			array( "value" => 'RW', "label" => 'Rwanda' ),
			array( "value" => 'BL', "label" => 'Saint Barthelemy' ),
			array( "value" => 'SH', "label" => 'Saint Helena' ),
			array( "value" => 'KN', "label" => 'Saint Kitts And Nevis' ),
			array( "value" => 'LC', "label" => 'Saint Lucia' ),
			array( "value" => 'MF', "label" => 'Saint Martin' ),
			array( "value" => 'PM', "label" => 'Saint Pierre And Miquelon' ),
			array( "value" => 'VC', "label" => 'Saint Vincent And Grenadines' ),
			array( "value" => 'WS', "label" => 'Samoa' ),
			array( "value" => 'SM', "label" => 'San Marino' ),
			array( "value" => 'ST', "label" => 'Sao Tome And Principe' ),
			array( "value" => 'SA', "label" => 'Saudi Arabia' ),
			array( "value" => 'SN', "label" => 'Senegal' ),
			array( "value" => 'RS', "label" => 'Serbia' ),
			array( "value" => 'SC', "label" => 'Seychelles' ),
			array( "value" => 'SL', "label" => 'Sierra Leone' ),
			array( "value" => 'SG', "label" => 'Singapore' ),
			array( "value" => 'SK', "label" => 'Slovakia' ),
			array( "value" => 'SI', "label" => 'Slovenia' ),
			array( "value" => 'SB', "label" => 'Solomon Islands' ),
			array( "value" => 'SO', "label" => 'Somalia' ),
			array( "value" => 'ZA', "label" => 'South Africa' ),
			array( "value" => 'GS', "label" => 'South Georgia And Sandwich Isl.' ),
			array( "value" => 'ES', "label" => 'Spain' ),
			array( "value" => 'LK', "label" => 'Sri Lanka' ),
			array( "value" => 'SD', "label" => 'Sudan' ),
			array( "value" => 'SR', "label" => 'Suriname' ),
			array( "value" => 'SJ', "label" => 'Svalbard And Jan Mayen' ),
			array( "value" => 'SZ', "label" => 'Swaziland' ),
			array( "value" => 'SE', "label" => 'Sweden' ),
			array( "value" => 'CH', "label" => 'Switzerland' ),
			array( "value" => 'SY', "label" => 'Syrian Arab Republic' ),
			array( "value" => 'TW', "label" => 'Taiwan' ),
			array( "value" => 'TJ', "label" => 'Tajikistan' ),
			array( "value" => 'TZ', "label" => 'Tanzania' ),
			array( "value" => 'TH', "label" => 'Thailand' ),
			array( "value" => 'TL', "label" => 'Timor-Leste' ),
			array( "value" => 'TG', "label" => 'Togo' ),
			array( "value" => 'TK', "label" => 'Tokelau' ),
			array( "value" => 'TO', "label" => 'Tonga' ),
			array( "value" => 'TT', "label" => 'Trinidad And Tobago' ),
			array( "value" => 'TN', "label" => 'Tunisia' ),
			array( "value" => 'TR', "label" => 'Turkey' ),
			array( "value" => 'TM', "label" => 'Turkmenistan' ),
			array( "value" => 'TC', "label" => 'Turks And Caicos Islands' ),
			array( "value" => 'TV', "label" => 'Tuvalu' ),
			array( "value" => 'UG', "label" => 'Uganda' ),
			array( "value" => 'UA', "label" => 'Ukraine' ),
			array( "value" => 'AE', "label" => 'United Arab Emirates' ),
			array( "value" => 'GB', "label" => 'United Kingdom' ),
			array( "value" => 'US', "label" => 'United States' ),
			array( "value" => 'UM', "label" => 'United States Outlying Islands' ),
			array( "value" => 'UY', "label" => 'Uruguay' ),
			array( "value" => 'UZ', "label" => 'Uzbekistan' ),
			array( "value" => 'VU', "label" => 'Vanuatu' ),
			array( "value" => 'VE', "label" => 'Venezuela' ),
			array( "value" => 'VN', "label" => 'Viet Nam' ),
			array( "value" => 'VG', "label" => 'Virgin Islands, British' ),
			array( "value" => 'VI', "label" => 'Virgin Islands, U.S.' ),
			array( "value" => 'WF', "label" => 'Wallis And Futuna' ),
			array( "value" => 'EH', "label" => 'Western Sahara' ),
			array( "value" => 'YE', "label" => 'Yemen' ),
			array( "value" => 'ZM', "label" => 'Zambia' ),
			array( "value" => 'ZW', "label" => 'Zimbabwe' ),
		);
		return $countries;
	}
}

include(dirname(__FILE__).'/report_builder.php');


function jweb_cpt_reports() {
	/**
	 * Post Type: Reports
	 */
	$labels = array(
		"name" => __( "Report Builder", "" ),
		"singular_name" => __( "Report", "" ),
		"add_new_item" => __( "Add New Report", "" ),
		"edit_item" => __( "Edit Report", "" ),
		"search_items" => __( "Search Reports", "" ),
	);

	$args = array(
		"label" => __( "Reports", "" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => false,
		"rest_base" => "",
		"has_archive" => false,
		"show_in_menu" => true,
		"exclude_from_search" => true,
		"capability_type" => "page",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"rewrite" => array( "slug" => "reports", "with_front" => false ),
		"query_var" => "reports",
		"menu_icon" => "dashicons-list-view",
		"supports" => array( 'title', 'author' ),
	);

	register_post_type( "jweb_reports", $args );
}
add_action( 'init', 'jweb_cpt_reports' );


class JWEB_Report_Builder_Management 
{
	static $instance;
	public $entity_list;

	public function __construct() {
		add_filter( 'set-screen-option', array( __CLASS__, 'set_screen' ), 10, 3 );
		add_action( 'admin_menu', array( $this, 'plugin_menu' ) );
		add_action( 'admin_head', array( $this, 'admin_header' ) );
		add_action( 'edit_form_after_title', array( $this, 'in_admin_header_lw' ) );
	}

    public function plugin_menu() {
		add_submenu_page(
			'edit.php?post_type=jweb_reports',
			'Report Builder Settings',
			'Settings',
			'manage_options',
			'jweb-report-builder-settings',
			array( $this, 'report_builder_settings' )
		);
	}

	public function admin_header() {
        $page = ( isset($_GET['page'] ) ) ? esc_attr( $_GET['page'] ) : false;
        if( 'jweb-report-builder' != $page ) {
            return;
        }
        echo '<style type="text/css">';
        echo '</style>';
	}

	public static function set_screen( $status, $option, $value ) {
		return $value;
	}

    public function screen_option() {
        return;
	}

	public function in_admin_header_lw() {
		$screen = get_current_screen();
		if($screen->post_type=='jweb_reports' && $screen->id=='jweb_reports') {
			?>

			<div id="postbox-container-3" class="postbox-container" style="margin-top:16px;">
				<div id="normal-sortables" class="meta-box-sortables ui-sortable">
					<div id="aaaadiv" class="postbox " style="">
						<h2 class=" ui-sortable-handle"><span>Aaaa</span></h2>
						<div class="inside">
							<label class="screen-reader-text" for="post_name">Aaaa</label>
							<input name="post_name" type="text" size="13" id="post_name" value="sales-orders" />
						</div>
					</div>
					<div id="bbbdiv" class="postbox ">
						<h2 class=" ui-sortable-handle"><span>Bbb</span></h2>
						<div class="inside">
							<label class="screen-reader-text" for="post_author_override">Bbb</label>
							<select name="post_author_override" id="post_author_override" class="">
								<option value="2">a a (jweb_dummy)</option>
								<option value="1" selected="selected">jweb (jweb)</option>
							</select>
						</div>
					</div>
				</div>
				<div id="advanced-sortables" class="meta-box-sortables ui-sortable"></div>
			</div>

			<?php
		}
	}

	public function report_builder_settings() {
		$data = $_POST;
		if ( array_key_exists('submitted', $_POST) && $_POST['submitted'] == 1) {

			$jweb_report_builder = array(
				'selected_tables' => $_POST['selected_tables']
			);
			update_option('jweb_report_builder', $jweb_report_builder);
		}

		$jweb_report_builder = get_option('jweb_report_builder');

		$selected_tables = $jweb_report_builder['selected_tables'];

		global $wpdb;
		$tables = $wpdb->get_col("SELECT `TABLE_NAME` FROM information_schema.tables WHERE `table_type` = 'BASE TABLE'");

		$dir = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)); 
		
		?>
		<div class="wrap">
			<h2>Report Builder Management</h2>
			<div id="">
				<div id="post-body" class="metabox-holder columns-2">
					<div id="post-body-content">
						<div class="meta-box-sortables ui-sortable">
							<form action="http://magento.jweblab.com/jjt/wp-admin/admin.php?page=jweb-report-builder" method="post">
							<input type="hidden" name="submitted" value="1" />
                                <table class="form-table">
                                    <tbody>
                                        <tr valign="top">
                                            <th scope="row"><label for="selected_tables">Table Selection</label></th>
                                            <td>
												<select name="selected_tables[]" multiple size="8">
											<?php foreach ( $tables as $table ): ?>
												<?php if ( in_array($table, $selected_tables) ): ?>
													<option selected><?php echo $table ?></option>
												<?php else: ?>
													<option><?php echo $table ?></option>
												<?php endif; ?>
											<?php endforeach; ?>
												</select>
											</td>
                                        </tr>

                                    </tbody>
                                </table>
                                <input type="submit" name="Submit" class="button-primary" value="Save Changes">
                            </form>
						</div>
					</div>
				</div>
				<br class="clear">
			</div>
		</div>
	<?php
        return;
	}

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}
add_action( 'plugins_loaded', function (){JWEB_Report_Builder_Management::get_instance();});