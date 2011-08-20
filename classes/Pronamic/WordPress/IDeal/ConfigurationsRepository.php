<?php

namespace Pronamic\WordPress\IDeal;

use Pronamic\IDeal\Transaction;
use Pronamic\IDeal\IDeal;

/**
 * Title: Configurations repository
 * Description: 
 * Copyright: Copyright (c) 2005 - 2011
 * Company: Pronamic
 * @author Remco Tolsma
 * @version 1.0
 */
class ConfigurationsRepository {
	/**
	 * The iDEAL providers
	 * 
	 * @var array
	 */
	public static $providers;

	/**
	 * The iDEAL variants
	 * 
	 * @var array
	 */
	public static $variants;

	//////////////////////////////////////////////////
	
	/**
	 * Load the providers and variants from an XML file
	 */
	public static function load() {
		if(self::$providers == null) {
			self::$providers = array();
			self::$variants = array();

			$file = plugin_dir_path(Plugin::$file) . 'ideal.xml';
	
			self::$providers = IDeal::getProvidersFromXml($file);

			foreach(self::$providers as $provider) {
				foreach($provider->getVariants() as $variant) {
					self::$variants[$variant->getId()] = $variant;
				}
			}
		}
	}

	//////////////////////////////////////////////////

	/**
	 * Update table
	 */
	public static function updateTable() {
        require_once ABSPATH . '/wp-admin/includes/upgrade.php';

		global $wpdb;

		$charsetCollate = '';
        if(!empty($wpdb->charset)) {
            $charsetCollate = 'DEFAULT CHARACTER SET ' . $wpdb->charset;
        }
        if(!empty($wpdb->collate)) {
            $charsetCollate .= ' COLLATE ' . $wpdb->collate;
        }

        // iDEAL configurations table
        $tableName = self::getConfigurationsTableName();

        $sql = "CREATE TABLE $tableName (
			id MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT ,  
			variant_id VARCHAR(64) NULL ,
			merchant_id VARCHAR(64) NULL ,  
			sub_id VARCHAR(64) NULL ,
			mode VARCHAR(64) NULL ,
			hash_key VARCHAR(64) NULL ,
			private_key TEXT NULL ,
			private_key_password VARCHAR(64) NULL ,
			private_certificate TEXT NULL , 
			PRIMARY KEY (id) 
			) $charsetCollate;";

        dbDelta($sql);
    }

	//////////////////////////////////////////////////
    
    /**
     * Drop the tables
     */
    public static function dropTables() {
		global $wpdb;

		$wpdb->query('DROP TABLE IF EXISTS ' . self::getConfigurationsTableName());
    }

	//////////////////////////////////////////////////

    /**
     * Get the iDEAL configurations table name
     * 
     * @return string
     */
    public static function getConfigurationsTableName() {
		global $wpdb;

		return $wpdb->prefix . 'pronamic_ideal_configurations';
    }


	//////////////////////////////////////////////////
	
    /**
     * Get configuration from the specified result object
     * 
     * @param stdClass $result
     * @return Configuration
     */
	private function getConfigurationFromResult($result) {
		$configuration = new Configuration();

		$configuration->setId($result->configurationId);
		$configuration->setVariant(self::getVariantById($result->variantId));
		$configuration->merchantId = $result->merchantId;
		$configuration->subId = $result->subId;
		$configuration->mode = $result->mode;
		$configuration->hashKey = $result->hashKey;
		$configuration->privateKey = $result->privateKey;
		$configuration->privateKeyPassword = $result->privateKeyPassword;
		$configuration->privateCertificate = $result->privateCertificate;
       	$configuration->numberPayments = $result->numberPayments;

		return $configuration;
	}
	
	private function getConfigurationQuery($extra = '') {
        $configurationsTable = self::getConfigurationsTableName();
        $paymentsTable = PaymentsRepository::getPaymentsTableName();

        $query = sprintf("
        	SELECT 
        		configuration.id AS configurationId ,
        		configuration.variant_id AS variantId ,  
        		configuration.merchant_id AS merchantId ,
        		configuration.sub_id AS subId ,
        		configuration.mode AS mode ,
        		configuration.hash_key AS hashKey ,
        		configuration.private_key AS privateKey ,
        		configuration.private_key_password AS privateKeyPassword ,
        		configuration.private_certificate AS privateCertificate ,
        		COUNT(payment.id) AS numberPayments
			FROM 
	        	$configurationsTable AS configuration
	        		LEFT JOIN
	        	$paymentsTable AS payment
	        			ON configuration.id = payment.configuration_id
			%s
	        GROUP BY
	        	configuration.id
        ", $extra);

		return $query;
	}

	//////////////////////////////////////////////////

    /**
     * Get the configurations
     * 
     * @return array
     */
    public static function getConfigurations() {
        global $wpdb;

		$configurations = array();

        $results = $wpdb->get_results(self::getConfigurationQuery(), OBJECT_K);
        foreach($results as $result) {
        	$configurations[] = self::getConfigurationFromResult($result);
        }

        return $configurations;
    }

	//////////////////////////////////////////////////

    /**
     * Get the iDEAL configurations by the specified query
     * 
     * @param string $query
     */
    private static function getConfigurationByQuery($query) {
		global $wpdb;

		$configuration = null;

        $result = $wpdb->get_row($query, OBJECT);
        if($result != null) {
        	$configuration = self::getConfigurationFromResult($result);
        }

        return $configuration;
    }

	//////////////////////////////////////////////////

    /**
     * Get iDEAL configuration by the specified ID
     * 
     * @param string $id
     */
    public static function getConfigurationById($id) {
		global $wpdb;

        $table = self::getConfigurationsTableName();

        $query = $wpdb->prepare(self::getConfigurationQuery("WHERE configuration.id = %d") , $id);

        return self::getConfigurationByQuery($query);
    }

	//////////////////////////////////////////////////

    /**
     * Update configuration
     * 
     * @param string $id
     * @param string $form_id
     * @param boolean $is_active
     * @param string $setting
     */
    public static function updateConfiguration($configuration) {
		global $wpdb;

		$table = self::getConfigurationsTableName();

		$id = $configuration->getId();
		$variantId = $configuration->getVariant() == null ? null : $configuration->getVariant()->getId();

		$data = array( 
			'variant_id' => $variantId , 
			'merchant_id' => $configuration->merchantId ,
			'sub_id' => $configuration->subId ,  
			'mode' => $configuration->mode ,
			'hash_key' => $configuration->hashKey ,
			'private_key' => $configuration->privateKey ,
			'private_key_password' => $configuration->privateKeyPassword , 
			'private_certificate' => $configuration->privateCertificate 
		);

		$format = array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s');

        if(empty($id)) {
            // Insert
            $result = $wpdb->insert($table, $data, $format);

            if($result !== false) {
            	$configuration->setId($wpdb->insert_id);
            }
        } else {
            // Update
			$result = $wpdb->update($table, $data, array('id' => $id), $format, array('%d'));
        }

        return $result;
    }

	//////////////////////////////////////////////////

    /**
     * Delete the configurations with the specified IDs
     * 
     * @param array $ids
     */
	public static function deleteConfigurations(array $ids) {
		global $wpdb;

		$table = self::getConfigurationsTableName();
		
		$list = implode(',', array_map('absint', $ids));

		$query = $wpdb->prepare("
			DELETE 
			FROM 
				$table 
			WHERE 
				id IN ($list)
			");

        return $wpdb->query($query);
    }

	//////////////////////////////////////////////////

    /**
     * Get the providers within this repository
     * 
     * @return array
     */
	public static function getProviders() {
		self::load();

		return self::$providers;
	}

	//////////////////////////////////////////////////

	/**
	 * Get the iDEAL variants within this repository
	 * 
	 * @return array
	 */
	public static function getVariants() {
		self::load();

		return self::$variants;
	}

	/**
	 * Get the iDEAL variant by the specified ID
	 * 
	 * @param string $id
	 * @return Variant
	 */
	public function getVariantById($id) {
		self::load();

		$variant = null;

		if(isset(self::$variants[$id])) {
			$variant = self::$variants[$id];
		}

		return $variant;
	}
}
