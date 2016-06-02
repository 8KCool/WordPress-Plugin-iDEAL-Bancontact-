<h2><?php esc_html_e( 'Documentation', 'pronamic_ideal' ); ?></h2>

<?php

$providers = array(
	'abnamro.nl' => array(
		'name'      => 'ABN AMRO',
		'url'       => 'http://abnamro.nl/',
		'resources' => array(
			array(
				'url'     => 'http://pronamic.nl/wp-content/uploads/2011/12/iDEALeasy_NL.pdf',
				'name'    => 'Handleiding IDEAL EASY',
			),
			array(
				'url'     => 'http://pronamic.nl/wp-content/uploads/2011/12/User_Manual_NL.pdf',
				'name'    => 'Handleiding iDEAL',
				'version' => '1.17',
			),
			array(
				'url'     => 'http://pronamic.nl/wp-content/uploads/2011/12/iDEAL_Merchant_Integratie_Gids_NL_v2.2.3.pdf',
				'name'    => 'iDEAL Merchant Integratie gids',
				'version' => '2.2.3',
			),
			array(
				'url'     => 'http://pronamic.nl/wp-content/uploads/2012/02/FAQ-NL-V1.17.pdf',
				'name'    => 'Veelgestelde vragen iDEAL',
				'version' => '1.17',
			),
			array(
				'url'     => 'http://pronamic.nl/wp-content/uploads/2012/02/iDEAL_ABN_AMRO_Integrated_JAVA.pdf',
				'name'    => 'iDEAL Integrated JAVA - Shop Integration Guide',
				'version' => '1.7',
			),
			array(
				'url'     => 'http://pronamic.nl/wp-content/uploads/2012/02/iDEAL_ABN_AMRO_Integrated_NET.pdf',
				'name'    => 'iDEAL Integrated Asp.NET - Shop Integration Guide',
				'version' => '1.7',
			),
			array(
				'url'     => 'http://pronamic.nl/wp-content/uploads/2012/02/iDEAL_ABN_AMRO_Integrated_PHP.pdf',
				'name'    => 'iDEAL Integrated PHP - Shop Integration Guide',
				'version' => '1.7',
			),
			array(
				'url'     => 'http://pronamic.nl/wp-content/uploads/2012/02/Opzegbrief_V2.0.pdf',
				'name'    => 'Opzegbrief',
				'version' => '2.0',
			),
			array(
				'url'     => 'http://pronamic.nl/wp-content/uploads/2012/02/ABNAMRO_e-Com-ADV_EN.pdf',
				'name'    => 'e-Commerce Advanced - Technical Integration Guide for e-Commerce',
				'version' => '5.3.3',
			),
			array(
				'url'     => 'http://pronamic.nl/wp-content/uploads/2012/02/ABNAMRO_e-Com-BAS_EN.pdf',
				'name'    => 'Basic e-Commerce - Technical Integration Guide for e-Commerce',
				'version' => '3.2.2',
			),
			array(
				'url'     => 'https://internetkassa.abnamro.nl/ncol/param_cookbook.asp',
				'name'    => 'Parameter Cookbook',
			),
			array(
				'url'     => 'http://pronamic.nl/wp-content/uploads/2012/11/ABN-AMRO-List-of-the-payment-statuses-and-error-codes.pdf',
				'url2'    => 'https://internetkassa.abnamro.nl/ncol/paymentinfos1.asp',
				'name'    => 'List of the payment statuses and error codes',
			),
			array(
				'url'     => 'https://internetkassa.abnamro.nl/ncol/paymentinfos8.asp',
				'name'    => 'Lijst van betalingsstatussen en foutcodes',
			),
			array(
				'url'     => 'http://pronamic.nl/wp-content/uploads/2012/11/ABN-AMRO-Internetkassa_-Parameter-Cookbook.pdf',
				'url2'    => 'https://internetkassa.abnamro.nl/ncol/param_cookbook.asp',
				'name'    => 'Parameter Cookbook',
			),
		),
	),
	'adyen.com' => array(
		'name'      => 'Adyen',
		'url'       => 'http://adyen.com/',
		'resources' => array(),
	),
	'buckaroo' => array(
		'name'      => 'Buckaroo',
		'url'       => 'http://www.buckaroo.nl/',
		'resources' => array(
			array(
				'url'     => 'http://pronamic.nl/wp-content/uploads/2013/04/BPE-3.0-Gateway-HTML.1.02.pdf',
				'name'    => 'Buckaroo Payment Engine 3.0 - Implementation Manual - HTML gateway',
				'version' => '1.02',
				'date'    => new DateTime( '01-03-2012' ),
			),
			array(
				'url'     => 'http://pronamic.nl/wp-content/uploads/2013/04/BPE-3.0-Service-iDEAL.2.01.pdf',
				'name'    => 'Buckaroo Payment Engine 3.0 - iDEAL',
				'version' => '2.01',
				'date'    => new DateTime( '14-02-2013' ),
			),
		),
	),
	'cardgate.com' => array(
		'name'      => 'Card Gate Plus',
		'url'       => 'http://cardgate.com/',
		'resources' => array(
			array(
				'url'     => 'http://pronamic.nl/wp-content/uploads/2011/12/Merchant_Integratie_Gids_2.3_NL.pdf',
				'name'    => 'Technische aansluit documentatie',
				'version' => '2.23',
				'date'    => new DateTime( '19-08-2011' ),
			),
		),
	),
	'currence.nl' => array(
		'name'      => 'Currence',
		'url'       => 'http://currence.nl/',
		'resources' => array(
			array(
				'url'     => 'http://pronamic.nl/wp-content/uploads/2011/12/Merchant_Integratie_Gids_2.3_NL.pdf',
				'name'    => 'iDEAL Merchant Integratie gids',
				'version' => '2.2.3',
			),
			array(
				'url'     => 'http://pronamic.nl/wp-content/uploads/2011/12/20100519_iDEAL_Merchant_Integration_Guide_v2.2.3.pdf',
				'name'    => 'iDEAL Merchant Integration Guide',
				'version' => '2.2.3',
			),
			array(
				'url'     => 'http://pronamic.nl/wp-content/uploads/2012/09/iDEAL-Merchant-Integration-Guide-Mobile-Addendum-EN.pdf',
				'name'    => 'iDEAL Merchant Integration Guide - Mobile Addendum',
				'version' => '2.2.3',
			),
			array(
				'url'     => 'http://pronamic.nl/wp-content/uploads/2012/09/iDEAL_Merchant_Integratie_Gids__Overzicht_van_Wijzigingen_v3.3.1_NL.pdf',
				'name'    => 'iDEAL Merchant Integratie Gids - Overzicht van Wijzigingen',
				'version' => '3.3.1',
			),
			array(
				'url'     => 'http://pronamic.nl/wp-content/uploads/2012/09/iDEAL_Merchant_Integration_Guide_Summary_of_Changes_v3.3.1_ENG.pdf',
				'name'    => 'iDEAL Merchant Integration Guide - Summary of Changes',
				'version' => '3.3.1',
			),
			array(
				'url'     => 'http://pronamic.nl/wp-content/uploads/2012/09/iDEAL_Merchant_Integratie_Gids_v3.3.1_NL.pdf',
				'name'    => 'iDEAL Merchant Integratie Gids',
				'version' => '3.3.1',
				'date'    => new DateTime( '01-11-2012' ),
			),
			array(
				'url'     => 'http://pronamic.nl/wp-content/uploads/2012/12/iDEAL-Merchant-Integration-Guide-ENG-v3.3.1.pdf',
				'name'    => 'iDEAL Merchant Integration Guide',
				'version' => '3.3.1',
				'date'    => new DateTime( '01-11-2012' ),
			),
		),
	),
	'dutchpaymentgroup.com' => array(
		'name'      => 'Dutch Payment Group',
		'url'       => 'http://www.dutchpaymentgroup.com/',
		'resources' => array(
			array(
				'url'     => 'http://pronamic.nl/wp-content/uploads/2013/01/DPG-Merchant-Integration-Manual-V201.pdf',
				'name'    => 'Dutch Payment Group payment platform: Merchant Integration Manual',
				'version' => '020',
			),
		),
	),
	'icepay.com' => array(
		'name'      => 'ICEPAY',
		'url'       => 'http://www.icepay.com/',
		'resources' => array(
			array(
				'url'     => 'http://pronamic.nl/wp-content/uploads/2013/11/icepay_implementation_guide.pdf',
				'name'    => 'Implementation Guide - v1.3.0',
				'version' => '1.3.0',
			),
		),
	),
	'ideal.nl' => array(
		'name'      => 'iDEAL',
		'url'       => 'http://ideal.nl/',
		'resources' => array(
			array(
				'url'  => 'http://www.ideal.nl/',
				'name' => 'iDEAL',
			),
			array(
				'url'  => 'http://www.ideal.nl/acceptant/?s=banner',
				'name' => 'iDEAL-banners die altijd actueel blijven',
			),
		),
	),
	'idealdesk.com' => array(
		'name'      => 'iDEALdesk',
		'url'       => 'https://www.idealdesk.com/',
		'resources' => array(
			array(
				'url'  => 'http://huisstijl.idealdesk.com/',
				'name' => 'Online styleguide van iDEAL',
			),
		),
	),
	'ing.nl' => array(
		'name'      => 'ING',
		'url'       => 'http://ing.nl/',
		'resources' => array(
			array(
				'url'        => 'http://pronamic.nl/wp-content/uploads/2011/12/iDEAL_Basic_NL.pdf',
				'name'       => 'iDEAL Basic – Integratie handleiding',
				'version'    => '1.3',
				'deprecated' => true,
			),
			array(
				'url'     => 'http://pronamic.nl/wp-content/uploads/2011/12/iDEAL_Advanced_PHP_EN_V2.2.pdf',
				'name'    => 'iDEAL Advanced – PHP integration manual',
				'version' => '2.2',
			),
			array(
				'url'     => 'http://pronamic.nl/wp-content/uploads/2011/12/Wijzigen_van_een_acquiring_certificaat_in_iDEAL_Advanced_internet_tcm7-82882.pdf',
				'name'    => 'Wijzigen van een acquiring certificaat in iDEAL Advanced',
			),
			array(
				'url'     => 'http://pronamic.nl/wp-content/uploads/2011/12/iDEAL_Advanced_PHP_EN_V2.3.pdf',
				'name'    => 'PHP integration manual - iDEAL advanced',
				'version' => '2.3',
			),
			array(
				'url'     => 'http://pronamic.nl/wp-content/uploads/2011/12/iDEAL_Advanced_PHP_NL_V2.3.pdf',
				'name'    => 'Integratiehandleiding PHP voor iDEAL Advanced',
				'version' => '2.3',
			),
			array(
				'url'     => 'http://pronamic.nl/wp-content/uploads/2011/12/iDEAL_Algemeen_NL_v2.3.pdf',
				'name'    => 'Introductie en procedure voor iDEAL',
				'version' => '2.3',
			),
			array(
				'url'     => 'http://pronamic.nl/wp-content/uploads/2011/12/iDEAL_Basic_EN_v2.3.pdf',
				'name'    => 'Integration manual for iDEAL Basic',
				'version' => '2.3',
			),
			array(
				'url'     => 'http://pronamic.nl/wp-content/uploads/2011/12/iDEAL_Basic_NL_v2.3.pdf',
				'name'    => 'Integratiehandleiding voor iDEAL Basic',
				'version' => '2.3',
			),
			array(
				'url'     => 'http://pronamic.nl/wp-content/uploads/2011/12/iDEAL_Download_guide_EN_v2.2.pdf',
				'name'    => 'iDEAL Download Guide',
				'version' => '2.2',
			),
			array(
				'url'     => 'http://pronamic.nl/wp-content/uploads/2011/12/iDEAL_Downloadwijzer_NL_v2.2.pdf',
				'name'    => 'iDEAL Downloadwijzer',
				'version' => '2.2',
			),
			array(
				'url'     => 'http://pronamic.nl/wp-content/uploads/2011/12/iDEAL_General_EN_v2.3.pdf',
				'name'    => 'Introduction and procedure for iDEAL',
				'version' => '2.3',
			),
		),
	),
	'mollie.nl' => array(
		'name'      => 'Mollie',
		'url'       => 'http://mollie.nl/',
		'resources' => array(
			array(
				'url'        => 'http://www.mollie.nl/support/documentatie/betaaldiensten/ideal/lite/',
				'name'       => 'iDEAL Lite/Basic',
				'deprecated' => true,
			),
			array(
				'url'        => 'https://www.mollie.nl/support/documentatie/betaaldiensten/ideal/professional/',
				'name'       => 'iDEAL Professional/Advanced',
				'deprecated' => true,
			),
			array(
				'url'        => 'http://pronamic.nl/wp-content/uploads/2014/07/Integrate-iDEAL-into-your-website-Dutch-payment-method-Mollie.pdf',
				'name'       => 'iDEAL API',
				'deprecated' => true,
			),
			array(
				'url'  => 'http://pronamic.nl/wp-content/uploads/2013/12/payments-api-nl.pdf',
				'name' => 'De Mollie betalings-API (universal API)',
			),
		),
	),
	'multisafepay.com' => array(
		'name'      => 'MultiSafepay',
		'url'       => 'https://www.multisafepay.com/',
		'resources' => array(
			array(
				'url'  => 'http://pronamic.nl/wp-content/uploads/2014/01/Handleiding_connectENG.pdf',
				'name' => 'MultiSafepay Connect - Implementation Guide Connect',
				'date' => new DateTime( '27-12-2013' ),
			),
			array(
				'url'  => 'http://pronamic.nl/wp-content/uploads/2014/01/Handleiding_connectNL.pdf',
				'name' => 'MultiSafepay Connect - Implementatie handleiding Connect',
				'date' => new DateTime( '27-12-2013' ),
			),
		),
	),
	'buckaroo.nl' => array(
		'name'      => 'Buckaroo',
		'url'       => 'http://buckaroo.nl/',
		'resources' => array(
			array(
				'url'  => 'http://payment.buckaroo.nl/',
				'name' => 'iDEAL Payment Gateway and Support',
			),
		),
	),
	'ogone.nl' => array(
		'name'      => 'Ogone',
		'url'       => 'http://www.ogone.nl/',
		'resources' => array(
			array(
				'url'     => 'http://pronamic.nl/wp-content/uploads/2011/12/Ogone_eCom_STD_Integration_20041224_EN.pdf',
				'name'    => 'Ogone Document II: Ogone e-Commerce, integration in the merchant\'s WEB site',
				'date'    => new DateTime( '26-10-2006' ),
			),
			array(
				'url'     => 'http://pronamic.nl/wp-content/uploads/2013/08/Ogone_DirectLink_EN.pdf',
				'name'    => 'Ogone - DirectLink - Integration Guide for the Server-to-Server Solution',
				'version' => '4.3.3',
			),
			array(
				'url'     => 'http://pronamic.nl/wp-content/uploads/2013/08/Ogone-Parameter-Cookbook.pdf',
				'name'    => 'Ogone - Parameter Cookbook',
			),
			array(
				'url'     => 'http://pronamic.nl/wp-content/uploads/2013/08/Ogone-List-of-the-payment-statuses-and-error-codes.pdf',
				'name'    => 'Ogone - List of the payment statuses and error codes',
			),
			array(
				'url'     => 'http://pronamic.nl/wp-content/uploads/2013/08/Ogone-Lijst-van-de-betaalstatussen-en-foutcodes.pdf',
				'name'    => 'Ogone - Lijst van de betaalstatussen en foutcodes',
			),
			array(
				'url'     => 'http://pronamic.nl/wp-content/uploads/2013/11/Ogone_DirectLink-3-D_EN.pdf',
				'name'    => 'Ogone - DirectLink with 3-D Secure',
			),
			array(
				'url'     => 'http://pronamic.nl/wp-content/uploads/2014/06/Ogone-Integratieparameters.pdf',
				'name'    => 'Ogone - Integratieparameters',
			),
		),
	),
	'paygate.co.za' => array(
		'name'      => 'PayGate',
		'url'       => 'http://paygate.co.za/',
		'resources' => array(
			array(
				'url'     => 'http://pronamic.nl/wp-content/uploads/2013/02/PayGate-PayWebv2-v1.15.pdf',
				'name'    => 'PayGate - PayWeb',
				'date'    => new DateTime( '01-02-2012' ),
				'version' => '1.0',
			),
		),
	),
	'rabobank.nl' => array(
		'name'      => 'Rabobank',
		'url'       => 'http://rabobank.nl/',
		'resources' => array(
			array(
				'url'        => 'http://pronamic.nl/wp-content/uploads/2011/12/handleiding_ideal_lite_2966321.pdf',
				'name'       => 'Rabo iDEAL Lite - Winkel Integratie Handleiding',
				'version'    => '2.3',
				'date'       => new DateTime( '01-12-2009' ),
				'deprecated' => true,
			),
			array(
				'url'        => 'http://pronamic.nl/wp-content/uploads/2011/12/handleiding_ideal_professional_2966322.pdf',
				'name'       => 'Handleiding iDEAL Professional',
				'version'    => '2.1',
				'date'       => new DateTime( '01-03-2007' ),
				'deprecated' => true,
			),
			array(
				'url'        => 'http://pronamic.nl/wp-content/uploads/2011/12/kennismaking_rabobank_ideal_dashboard.pdf',
				'name'       => 'Kennismaking Rabobank iDEAL Dashboard',
			),
			array(
				'url'        => 'http://pronamic.nl/wp-content/uploads/2012/01/rabo_omnikassa_gebruikerhandleiding_dashboard_d1_1_dutch_20120117_final_29420243.pdf',
				'name'       => 'Gebruikshandleiding Rabo OmniKassa Dashboard',
				'version'    => '2.0',
				'deprecated' => true,
			),
			array(
				'url'        => 'http://pronamic.nl/wp-content/uploads/2012/01/rabo_omnikassa_gebruikshandleiding_downloadsite_29420244.pdf',
				'name'       => 'Gebruikshandleiding Rabo OmniKassa Downloadsite',
				'version'    => '2.0',
				'deprecated' => true,
			),
			array(
				'url'        => 'http://pronamic.nl/wp-content/uploads/2012/01/rabo_omnikassa_redirect_connector_user_guide_v1_0_10_dutch_final_29420242.pdf',
				'name'       => 'Integratiehandleiding Rabo OmniKassa Versie 1.0.10 – januari 2012',
				'version'    => '1.0.10',
				'deprecated' => true,
			),
			array(
				'url'        => 'http://pronamic.nl/wp-content/uploads/2012/05/zo_werkt_het_aanvragen_en_aansluiten_van_de_rabo_omnikassa_29417568.pdf',
				'name'       => 'Zo werkt het aanvragen en aansluiten van de Rabo OmniKassa',
				'date'       => new DateTime( '17-04-2012' ),
			),
			array(
				'url'        => 'http://pronamic.nl/wp-content/uploads/2012/07/integration_guide_rabo_omnikassa_v2_0_1_final_29451215.pdf',
				'name'       => 'Integration guide Rabo OmniKassa',
				'date'       => new DateTime( '01-04-2012' ),
				'version'    => '2.0.1',
				'deprecated' => true,
			),
			array(
				'url'        => 'http://pronamic.nl/wp-content/uploads/2012/09/Handleiding-iDEAL-Professional.pdf',
				'name'       => 'Handleiding iDEAL Professional',
				'date'       => new DateTime( '01-05-2012' ),
				'version'    => '1.1',
				'deprecated' => true,
			),
			array(
				'url'        => 'http://pronamic.nl/wp-content/uploads/2012/09/Handleiding-iDEAL-Lite.pdf',
				'name'       => 'Handleiding iDEAL Lite',
				'date'       => new DateTime( '01-01-2012' ),
				'version'    => '2.6',
				'deprecated' => true,
			),
			array(
				'url'        => 'http://pronamic.nl/wp-content/uploads/2012/09/Integratiehandleiding_Rabo_Omnikassa_v300_nl.pdf',
				'name'       => 'Integratiehandleiding Rabo OmniKassa - Versie 3.0 September 2012',
				'version'    => '3.0',
				'date'       => new DateTime( '01-09-2012' ),
				'deprecated' => true,
			),
			array(
				'url'        => 'http://pronamic.nl/wp-content/uploads/2012/09/Integration_Guide_Rabo_Omnikassa_v300_en.pdf',
				'name'       => 'Integration guide Rabo OmniKassa - Version 3.0 September 2012',
				'version'   => '3.0',
				'date'       => new DateTime( '01-09-2012' ),
			),
			array(
				'url'        => 'http://pronamic.nl/wp-content/uploads/2012/09/zo_werkt_het_aanvragen_en_aansluiten_van_de_rabo_omnikassa_29417568.pdf',
				'name'       => 'Zo werkt het aanvragen en aansluiten van de Rabo OmniKassa',
				'date'       => new DateTime( '03-09-2012' ),
			),
			array(
				'url'       => 'http://www.rabobank.nl/omnikassa-actueel',
				'name'       => 'Rabo OmniKassa Actueel',
			),
			array(
				'url'        => 'http://pronamic.nl/wp-content/uploads/2012/11/integratiehandleiding_rabo_omnikassa_versie_7_11_2012_29420242.pdf',
				'name'       => 'Integratiehandleiding Rabo OmniKassa - Versie 3.1, november 2012',
				'version'    => '3.1',
				'date'       => new DateTime( '01-11-2012' ),
				'deprecated' => true,
			),
			array(
				'url'        => 'http://pronamic.nl/wp-content/uploads/2013/01/rabobank_omnikassa_integratiehandleiding_versie_4_1_december_2012_29420242.pdf',
				'name'       => 'Integratiehandleiding Rabo OmniKassa - Versie 4.1, december 2012',
				'version'    => '4.1',
				'date'       => new DateTime( '01-12-2012' ),
				'deprecated' => true,
			),
			array(
				'url'        => 'http://pronamic.nl/wp-content/uploads/2013/01/rabobank_omnikassa_gebruikshandleiding_downloadsite_versie_2_1_december_2012_29420244.pdf',
				'name'       => 'Rabo OmniKassa - Gebruikshandleiding Downloadsite - Versie 2.1, december 2012',
				'version'    => '2.1',
				'date'       => new DateTime( '01-12-2012' ),
			),
			array(
				'url'        => 'http://pronamic.nl/wp-content/uploads/2013/01/rabo_omnikassa_gebruikerhandleiding_dashboard_29420243.pdf',
				'name'       => 'Gebruikshandleiding - Dashboard van de Rabo OmniKassa - ROK 2.3 07-11-2012',
				'version'    => '2.3',
				'date'       => new DateTime( '07-11-2012' ),
				'deprecated' => true,
			),
			array(
				'url'        => 'http://pronamic.nl/wp-content/uploads/2013/06/rabo_omnikassa_gebruikshandleiding_dashboard_new_29550801.pdf',
				'name'       => 'Gebruikshandleiding - Dashboard van de Rabo OmniKassa - versie 2.4, 5 juni 2013',
				'version'    => '2.4',
				'date'       => new DateTime( '05-06-2013' ),
			),
			array(
				'url'        => 'http://pronamic.nl/wp-content/uploads/2013/06/Handleiding-iDEAL-Lite.pdf',
				'name'       => 'Rabo iDEAL Lite - Integratie Handleiding',
				'version'    => '2.6',
				'date'       => new DateTime( '01-01-2012' ),
			),
			array(
				'url'        => 'http://pronamic.nl/wp-content/uploads/2013/06/Handleiding-iDEAL-Professional.pdf',
				'name'       => 'Handleiding iDEAL Professional',
				'version'    => '1.2',
				'date'       => new DateTime( '01-11-2012' ),
			),
			array(
				'url'        => 'http://pronamic.nl/wp-content/uploads/2013/06/rabobank_omnikassa_integratiehandleiding_29420242.pdf',
				'name'       => 'Integratiehandleiding Rabobank OmniKassa - Versie 4.2, 5 juni 2013',
				'version'    => '4.2',
				'date'       => new DateTime( '05-06-2013' ),
				'deprecated' => true,
			),
			array(
				'url'        => 'http://pronamic.nl/wp-content/uploads/2013/08/integratiehandleiding_rabo_omnikassa_en_versie_5_0_juni_2013_10_29451215.pdf',
				'name'       => 'Integration Guide Rabo OmniKassa - Versie 5.0, 28 June 2013',
				'version'    => '5.0',
				'date'       => new DateTime( '28-06-2013' ),
				'deprecated' => true,
			),
			array(
				'url'        => 'http://pronamic.nl/wp-content/uploads/2013/08/integratiehandleiding_rabobank_omnikassa_nl_versie_5_0_juni_2013_10_29420242.pdf',
				'name'       => 'Integratiehandleiding Rabo OmniKassa - Versie 5.1, 28 juni 2013',
				'version'    => '5.0',
				'date'       => new DateTime( '28-06-2013' ),
				'deprecated' => true,
			),
			array(
				'url'        => 'http://pronamic.nl/wp-content/uploads/2013/10/integratiehandleiding_rabo_omnikassa_en_versie_5_0_juni_2013_10_29451215.pdf',
				'name'       => 'Integration Guide Rabo OmniKassa - Versie 5.1, October 2013',
				'version'    => '5.1',
				'date'       => new DateTime( '01-10-2013' ),
				'deprecated' => true,
			),
			array(
				'url'        => 'http://pronamic.nl/wp-content/uploads/2013/10/integratiehandleiding_rabobank_omnikassa_nl_versie_5_0_juni_2013_10_29420242.pdf',
				'name'       => 'Integratiehandleiding Rabo OmniKassa - Versie 5.1, oktboer 2013',
				'version'    => '5.1',
				'date'       => new DateTime( '01-10-2013' ),
				'deprecated' => true,
			),
			array(
				'url'        => 'http://pronamic.nl/wp-content/uploads/2014/01/integratiehandleiding_en_12_2013_29451215.pdf',
				'name'       => 'Integration Guide Rabo OmniKassa, Version 6.0, November 2013',
				'version'    => '6.0',
				'date'       => new DateTime( '01-11-2013' ),
				'deprecated' => true,
			),
			array(
				'url'        => 'http://pronamic.nl/wp-content/uploads/2014/01/integratiehandleiding_nl_12_2013_29420242.pdf',
				'name'       => 'Integratiehandleiding Rabo OmniKassa, versie 6.0, november 2013',
				'version'    => '6.0',
				'date'       => new DateTime( '01-11-2013' ),
				'deprecated' => true,
			),
			array(
				'url'        => 'http://pronamic.nl/wp-content/uploads/2014/07/integratiehandleiding_rabo_omnikassa_en_version_7_1_april_2014_final_2_0_29637101.pdf',
				'name'       => 'Integration Guide Rabo OmniKassa, Version 7.1, April 2014',
				'version'    => '7.1',
				'date'       => new DateTime( '01-04-2014' ),
			),
			array(
				'url'        => 'http://pronamic.nl/wp-content/uploads/2014/07/integratiehandleiding_nl_12_2013_29420242.pdf',
				'name'       => 'Integratiehandleiding Rabo OmniKassa, versie 7.1, april 2014',
				'version'    => '7.1',
				'date'       => new DateTime( '01-04-2014' ),
			),
		),
	),
	'sisow.nl' => array(
		'name'      => 'Sisow',
		'url'       => 'http://sisow.nl/',
		'resources' => array(
			array(
				'url'     => 'http://pronamic.nl/wp-content/uploads/2012/06/Sisow-Pronamic-iDEAL.pdf',
				'name'    => 'Sisow - Pronamic iDEAL',
			),
			array(
				'url'     => 'http://pronamic.nl/wp-content/uploads/2013/02/sisow-rest-api-v3.2.1.pdf',
				'name'    => 'Sisow - REST API',
				'version' => '3.2.1',
			),
			array(
				'url'     => 'http://pronamic.nl/wp-content/uploads/2013/02/sisow-webservice-api-v2.0.pdf',
				'name'    => 'Sisow - WebService API',
				'version' => '2.0',
			),
		),
	),
	'qantani.com' => array(
		'name'      => 'Qantani',
		'url'       => 'http://qantani.com/',
		'resources' => array(
			array(
				'url'     => 'http://pronamic.nl/wp-content/uploads/2013/05/documentation-for-qantani-frontend-v1.pdf',
				'name'    => 'Qantani - Connecting to the Frontend',
				'version' => '1.0',
			),
			array(
				'url'     => 'http://pronamic.nl/wp-content/uploads/2013/05/documentation-for-qantani-xml-v1.pdf',
				'name'    => 'Qantani - Connecting to the API',
				'version' => '1.0',
			),
		),
	),
);

?>

<table class="pronamic-pay-table wp-list-table widefat" cellspacing="0">

	<?php foreach ( array( 'thead', 'tfoot' ) as $tag ) : ?>

		<<?php echo esc_html( $tag ); ?>>
			<tr>
				<th scope="col" class="manage-column"><?php esc_html_e( 'Title', 'pronamic_ideal' ); ?></th>
				<th scope="col" class="manage-column"><?php esc_html_e( 'Date', 'pronamic_ideal' );  ?></th>
				<th scope="col" class="manage-column"><?php esc_html_e( 'Version', 'pronamic_ideal' );  ?></th>
			</tr>
		</<?php echo esc_html( $tag ); ?>>

	<?php endforeach; ?>

	<tobdy>

		<?php foreach ( $providers as $provider ) : ?>

			<?php if ( isset( $provider['resources'] ) && ! empty( $provider['resources'] ) ) : ?>

				<tr class="alternate">
					<td colspan="4">
						<strong><?php echo esc_html( $provider['name'] ); ?></strong>
						<small><a href="<?php echo esc_attr( $provider['url'] ); ?>"><?php echo esc_html( $provider['url'] ); ?></a></small>
					</td>
				</tr>

				<?php foreach ( $provider['resources'] as $resource ) : ?>

					<?php

					$href = null;

					if ( isset( $resource['path'] ) ) {
						$href = plugins_url( $resource['path'], Pronamic_WP_Pay_Plugin::$file );
					}

					if ( isset( $resource['url'] ) ) {
						$href = $resource['url'];
					}

					$classes = array();

					if ( isset( $resource['deprecated'] ) ) {
						$classes[] = 'deprecated';
					}

					?>
					<tr class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
						<td>
							<a href="<?php echo esc_attr( $href ); ?>">
								<?php echo esc_html( $resource['name'] ); ?>
							</a>
						</td>
						<td>
							<?php

							if ( isset( $resource['date'] ) ) {
								echo esc_html( $resource['date']->format( 'd-m-Y' ) );
							}

							?>
						</td>
						<td>
							<?php

							if ( isset( $resource['version'] ) ) {
								echo esc_html( $resource['version'] );
							}

							?>
						</td>
					</tr>

				<?php endforeach; ?>

			<?php endif; ?>

		<?php endforeach; ?>

	</tobdy>
</table>
