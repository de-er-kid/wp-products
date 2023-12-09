<?php

if (!defined('ABSPATH')) {
    exit;
}

// Settings Page: StoreSettings
// Retrieving values: get_option( 'your_field_id' )
class StoreSettings_Settings_Page {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'wbthnk_ecom_create_settings' ) );
		add_action( 'admin_init', array( $this, 'wbthnk_ecom_setup_sections' ) );
		add_action( 'admin_init', array( $this, 'wbthnk_ecom_setup_fields' ) );
	}

	public function wbthnk_ecom_create_settings() {
        $parent_slug = 'edit.php?post_type=product'; // Products menu
        $page_title = 'Store Settings';
        $menu_title = 'Store Settings';
        $capability = 'manage_options';
        $slug = 'StoreSettings';
        $callback = array($this, 'wbthnk_ecom_settings_content');
        add_submenu_page($parent_slug, $page_title, $menu_title, $capability, $slug, $callback);
    }    
    
	public function wbthnk_ecom_settings_content() { ?>
		<div class="wrap">
			<h1>Store Settings</h1>
			<?php settings_errors(); ?>
			<form method="POST" action="options.php">
				<?php
					settings_fields( 'StoreSettings' );
					do_settings_sections( 'StoreSettings' );
					submit_button();
				?>
			</form>
		</div> <?php
	}

	public function wbthnk_ecom_setup_sections() {
		add_settings_section( 'StoreSettings_section', 'General store settings for our eCommerce plugin WP Products by webth.ink.', array(), 'StoreSettings' );
	}

	public function wbthnk_ecom_setup_fields() {
		$fields = array(
                    array(
                        'section' => 'StoreSettings_section',
                        'label' => 'Address Line 1',
                        'id' => 'wpecom_store_address',
                        'type' => 'text',
                    ),
        
                    array(
                        'section' => 'StoreSettings_section',
                        'label' => 'Address Line 2',
                        'id' => 'wpecom_store_address_2',
                        'type' => 'text',
                    ),
        
                    array(
                        'section' => 'StoreSettings_section',
                        'label' => 'City',
                        'id' => 'wpecom_store_city',
                        'type' => 'text',
                    ),
        
                    array(
                        'section' => 'StoreSettings_section',
                        'label' => 'Country / State',
                        'id' => 'wpecom_default_country',
                        'type' => 'text',
                    ),
        
                    array(
                        'section' => 'StoreSettings_section',
                        'label' => 'Postcode / ZIP',
                        'id' => 'wpecom_store_postcode',
                        'type' => 'text',
                    ),
        
                    array(
                        'section' => 'StoreSettings_section',
                        'label' => 'Currency',
                        'placeholder' => 'Select',
                        'id' => 'wpecom_currency',
                        'type' => 'select',
                        'options' => array(
                            'AED' => 'United Arab Emirates dirham (د.إ)',
                            'AFN' => 'Afghan afghani (؋)',
                            'ALL' => 'Albanian lek (L)',
                            'AMD' => 'Armenian dram (֏)',
                            'ANG' => 'Netherlands Antillean guilder (ƒ)',
                            'AOA' => 'Angolan kwanza (Kz)',
                            'ARS' => 'Argentine peso ($)',
                            'AUD' => 'Australian dollar ($)',
                            'AWG' => 'Aruban florin (ƒ)',
                            'AZN' => 'Azerbaijani manat (₼)',
                            'BAM' => 'Bosnia and Herzegovina convertible mark (KM)',
                            'BBD' => 'Barbadian dollar ($)',
                            'BDT' => 'Bangladeshi taka (৳)',
                            'BGN' => 'Bulgarian lev (лв.)',
                            'BHD' => 'Bahraini dinar (.د.ب)',
                            'BIF' => 'Burundian franc (Fr)',
                            'BMD' => 'Bermudian dollar ($)',
                            'BND' => 'Brunei dollar ($)',
                            'BOB' => 'Bolivian boliviano (Bs.)',
                            'BRL' => 'Brazilian real (R$)',
                            'BSD' => 'Bahamian dollar ($)',
                            'BTN' => 'Bhutanese ngultrum (Nu.)',
                            'BWP' => 'Botswana pula (P)',
                            'BYN' => 'Belarusian ruble (Br)',
                            'BZD' => 'Belize dollar ($)',
                            'CAD' => 'Canadian dollar ($)',
                            'CDF' => 'Congolese franc (Fr)',
                            'CHF' => 'Swiss franc (Fr)',
                            'CLP' => 'Chilean peso ($)',
                            'CNY' => 'Chinese yuan (¥)',
                            'COP' => 'Colombian peso ($)',
                            'CRC' => 'Costa Rican colón (₡)',
                            'CUC' => 'Cuban convertible peso ($)',
                            'CUP' => 'Cuban peso ($)',
                            'CVE' => 'Cape Verdean escudo ($)',
                            'CZK' => 'Czech koruna (Kč)',
                            'DJF' => 'Djiboutian franc (Fr)',
                            'DKK' => 'Danish krone (kr)',
                            'DOP' => 'Dominican peso (RD$)',
                            'DZD' => 'Algerian dinar (د.ج)',
                            'EGP' => 'Egyptian pound (£)',
                            'ERN' => 'Eritrean nakfa (Nfk)',
                            'ETB' => 'Ethiopian birr (Br)',
                            'EUR' => 'Euro (€)',
                            'FJD' => 'Fijian dollar ($)',
                            'FKP' => 'Falkland Islands pound (£)',
                            'GBP' => 'British pound (£)',
                            'GEL' => 'Georgian lari (₾)',
                            'GGP' => 'Guernsey pound (£)',
                            'GHS' => 'Ghanaian cedi (₵)',
                            'GIP' => 'Gibraltar pound (£)',
                            'GMD' => 'Gambian dalasi (D)',
                            'GNF' => 'Guinean franc (Fr)',
                            'GTQ' => 'Guatemalan quetzal (Q)',
                            'GYD' => 'Guyanese dollar ($)',
                            'HKD' => 'Hong Kong dollar ($)',
                            'HNL' => 'Honduran lempira (L)',
                            'HRK' => 'Croatian kuna (kn)',
                            'HTG' => 'Haitian gourde (G)',
                            'HUF' => 'Hungarian forint (Ft)',
                            'IDR' => 'Indonesian rupiah (Rp)',
                            'ILS' => 'Israeli new shekel (₪)',
                            'IMP' => 'Manx pound (£)',
                            'INR' => 'Indian rupee (₹)',
                            'IQD' => 'Iraqi dinar (ع.د)',
                            'IRR' => 'Iranian rial (﷼)',
                            'ISK' => 'Icelandic króna (kr)',
                            'JEP' => 'Jersey pound (£)',
                            'JMD' => 'Jamaican dollar (J$)',
                            'JOD' => 'Jordanian dinar (د.ا)',
                            'JPY' => 'Japanese yen (¥)',
                            'KES' => 'Kenyan shilling (KSh)',
                            'KGS' => 'Kyrgyzstani som (som)',
                            'KHR' => 'Cambodian riel (៛)',
                            'KMF' => 'Comorian franc (Fr)',
                            'KPW' => 'North Korean won (₩)',
                            'KRW' => 'South Korean won (₩)',
                            'KWD' => 'Kuwaiti dinar (د.ك)',
                            'KYD' => 'Cayman Islands dollar ($)',
                            'KZT' => 'Kazakhstani tenge (₸)',
                            'LAK' => 'Lao kip (₭)',
                            'LBP' => 'Lebanese pound (ل.ل)',
                            'LKR' => 'Sri Lankan rupee (Rs)',
                            'LRD' => 'Liberian dollar ($)',
                            'LSL' => 'Lesotho loti (L)',
                            'LYD' => 'Libyan dinar (ل.د)',
                            'MAD' => 'Moroccan dirham (د.م)',
                            'MDL' => 'Moldovan leu (L)',
                            'MGA' => 'Malagasy ariary (Ar)',
                            'MKD' => 'Macedonian denar (ден)',
                            'MMK' => 'Burmese kyat (K)',
                            'MNT' => 'Mongolian tögrög (₮)',
                            'MOP' => 'Macanese pataca (P)',
                            'MRO' => 'Mauritanian ouguiya (UM)',
                            'MRU' => 'Mauritanian ouguiya (MRU)',
                            'MUR' => 'Mauritian rupee (₨)',
                            'MVR' => 'Maldivian rufiyaa (.ރ)',
                            'MWK' => 'Malawian kwacha (MK)',
                            'MXN' => 'Mexican peso ($)',
                            'MYR' => 'Malaysian ringgit (RM)',
                            'MZN' => 'Mozambican metical (MT)',
                            'NAD' => 'Namibian dollar ($)',
                            'NGN' => 'Nigerian naira (₦)',
                            'NIO' => 'Nicaraguan córdoba (C$)',
                            'NOK' => 'Norwegian krone (kr)',
                            'NPR' => 'Nepalese rupee (रू)',
                            'NZD' => 'New Zealand dollar ($)',
                            'OMR' => 'Omani rial (ر.ع.)',
                            'PAB' => 'Panamanian balboa (B/.)',
                            'PEN' => 'Peruvian sol (S/.)',
                            'PGK' => 'Papua New Guinean kina (K)',
                            'PHP' => 'Philippine peso (₱)',
                            'PKR' => 'Pakistani rupee (₨)',
                            'PLN' => 'Polish złoty (zł)',
                            'PYG' => 'Paraguayan guaraní (₲)',
                            'QAR' => 'Qatari riyal (ر.ق)',
                            'RON' => 'Romanian leu (lei)',
                            'RSD' => 'Serbian dinar (дин)',
                            'RUB' => 'Russian ruble (₽)',
                            'RWF' => 'Rwandan franc (Fr)',
                            'SAR' => 'Saudi riyal (ر.س)',
                            'SBD' => 'Solomon Islands dollar ($)',
                            'SCR' => 'Seychellois rupee (₨)',
                            'SDG' => 'Sudanese pound (ج.س.)',
                            'SEK' => 'Swedish krona (kr)',
                            'SGD' => 'Singapore dollar ($)',
                            'SHP' => 'Saint Helena pound (£)',
                            'SLL' => 'Sierra Leonean leone (Le)',
                            'SOS' => 'Somali shilling (Sh)',
                            'SRD' => 'Surinamese dollar ($)',
                            'SSP' => 'South Sudanese pound (£)',
                            'STD' => 'São Tomé and Príncipe dobra (Db)',
                            'SVC' => 'Salvadoran colón ($)',
                            'SYP' => 'Syrian pound (ل.س)',
                            'SZL' => 'Swazi lilangeni (L)',
                            'THB' => 'Thai baht (฿)',
                            'TJS' => 'Tajikistani somoni (ЅМ)',
                            'TMT' => 'Turkmenistan manat (T)',
                            'TND' => 'Tunisian dinar (د.ت)',
                            'TOP' => 'Tongan paʻanga (T$)',
                            'TRY' => 'Turkish lira (₺)',
                            'TTD' => 'Trinidad and Tobago dollar ($)',
                            'TWD' => 'New Taiwan dollar (NT$)',
                            'TZS' => 'Tanzanian shilling (Sh)',
                            'UAH' => 'Ukrainian hryvnia (₴)',
                            'UGX' => 'Ugandan shilling (Sh)',
                            'USD' => 'United States dollar ($)',
                            'UYU' => 'Uruguayan peso ($)',
                            'UZS' => 'Uzbekistani soʻm (soʻm)',
                            'VEF' => 'Venezuelan bolívar (Bs.F.)',
                            'VND' => 'Vietnamese đồng (₫)',
                            'VUV' => 'Vanuatu vatu (VT)',
                            'WST' => 'Samoan tālā (T)',
                            'XAF' => 'Central African CFA franc (CFA)',
                            'XCD' => 'Eastern Caribbean dollar ($)',
                            'XDR' => 'Special drawing rights (SDR)',
                            'XOF' => 'West African CFA franc (CFA)',
                            'XPF' => 'CFP franc (Fr)',
                            'YER' => 'Yemeni rial (﷼)',
                            'ZAR' => 'South African rand (R)',
                            'ZMW' => 'Zambian kwacha (ZK)'
                        )
                    ),
        
                    array(
                        'section' => 'StoreSettings_section',
                        'label' => 'New Order Recipient Mail',
                        'id' => 'wpecom_new_order_recipient',
                        'desc' => 'Email address to recive new orders.',
                        'type' => 'email',
                    ),
        
                    array(
                        'section' => 'StoreSettings_section',
                        'label' => 'Customer Support Email',
                        'id' => 'wpecom_customer_support_email',
                        'desc' => 'Email address for customer care support & other contact purposes.',
                        'type' => 'text',
                    ),
        
                    array(
                        'section' => 'StoreSettings_section',
                        'label' => 'Customer Support Phone',
                        'id' => 'wpecom_customer_support_phone',
                        'desc' => 'Phone number for customer care support & other contact purposes.',
                        'type' => 'tel',
                    )
		);
		foreach( $fields as $field ){
			add_settings_field( $field['id'], $field['label'], array( $this, 'wbthnk_ecom_field_callback' ), 'StoreSettings', $field['section'], $field );
			register_setting( 'StoreSettings', $field['id'] );
		}
	}
	public function wbthnk_ecom_field_callback( $field ) {
		$value = get_option( $field['id'] );
		$placeholder = '';
		if ( isset($field['placeholder']) ) {
			$placeholder = $field['placeholder'];
		}
		switch ( $field['type'] ) {
            
            
                        case 'select':
                            case 'multiselect':
                                if( ! empty ( $field['options'] ) && is_array( $field['options'] ) ) {
                                    $attr = '';
                                    $options = '';
                                    foreach( $field['options'] as $key => $label ) {
                                        $options.= sprintf('<option value="%s" %s>%s</option>',
                                            $key,
                                            selected($value, $key, false),
                                            $label
                                        );
                                    }
                                    if( $field['type'] === 'multiselect' ){
                                        $attr = ' multiple="multiple" ';
                                    }
                                    printf( '<select name="%1$s" id="%1$s" %2$s>%3$s</select>',
                                        $field['id'],
                                        $attr,
                                        $options
                                    );
                                }
                                break;

			default:
				printf( '<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" />',
					$field['id'],
					$field['type'],
					$placeholder,
					$value
				);
		}
		if( isset($field['desc']) ) {
			if( $desc = $field['desc'] ) {
				printf( '<p class="description">%s </p>', $desc );
			}
		}
	}
    
}
new StoreSettings_Settings_Page();