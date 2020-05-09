<?php
class ModelExtensionModuleSpecialOffer extends Model {
	public function getSpecialOffer($special_offer_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "special_offer s LEFT JOIN " . DB_PREFIX . "special_offer_description sd ON (s.special_offer_id = sd.special_offer_id) LEFT JOIN " . DB_PREFIX . "special_offer_to_store s2s ON (s.special_offer_id = s2s.special_offer_id) WHERE s.special_offer_id = '" . (int)$special_offer_id . "' AND sd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND s2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");

		return $query->row;
	}
	
	public function getProductSpecialOfferID($product_id) {		// Product Specials Offer ID
		$product_special_query = $this->db->query("SELECT special_offer_id FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY priority ASC, price ASC LIMIT 1");

		if ($product_special_query->num_rows) {
			$special_offer_id = $product_special_query->row['special_offer_id'];
		} else { 
			$special_offer_id = 0;
		}
		return $special_offer_id;
	}

	public function getProductSpecialInfo($product_id) {		// Product Specials Info
		$query = $this->db->query("SELECT ps.special_offer_id, ps.product_special_id, ps.date_start, ps.date_end FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = '" . (int)$product_id . "' AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1");
		
		if ($query->num_rows) {
			return array(
				'special_offer_id'    => $query->row['special_offer_id'],
				'product_special_id'    => $query->row['product_special_id'],
				'date_start'    	   => $query->row['date_start'],	
				'date_end'    	   => $query->row['date_end']
			);
		} else {
			return false;
		}		
	}		
	
	public function getSpecialOfferInfoBySpecID($product_special_id) {		// Special Offer Info by product_special_id
		$query = $this->db->query("SELECT ps.special_offer_id, ps.price, ps.product_special_id, ps.date_start, ps.date_end, so.offer_type, so.image_label, so.gift_product_id, so.cycle_of_timer FROM " . DB_PREFIX . "product_special ps LEFT JOIN " . DB_PREFIX . "special_offer so ON (ps.special_offer_id = so.special_offer_id) WHERE ps.product_special_id = '" . (int)$product_special_id . "'");
		
		if ($query->num_rows) {
			
			if ($query->row['date_end'] == '0000-00-00') {
				$stop_timer = '0000-00-00';
			} else {
				$stop_timer = date('m/d/Y G:i:s', strtotime( $query->row['date_end'] ));
			}	
			
			if ($query->row['cycle_of_timer']) {
				$cycle_of_timer = $query->row['cycle_of_timer'];

				$diff = time() - strtotime( $query->row['date_start'] );
				$diff = (floor($diff/$cycle_of_timer)+1) * $cycle_of_timer;
				$stop_timer=date('m/d/Y G:i:s', strtotime( $query->row['date_start'].'+'.$diff.' seconds' ));						
			}			

			return array(
				'special'				=> $query->row['price'],			
				'special_offer_id'		=> $query->row['special_offer_id'],
				'product_special_id'	=> $query->row['product_special_id'],
				'date_start'			=> $query->row['date_start'],	
				'date_end'				=> $query->row['date_end'],
				'offer_type'			=> $query->row['offer_type'],
				'gift_product_id'		=> $query->row['gift_product_id'],
				'image_label'			=> $query->row['image_label'],	
				'stop_timer'			=> $stop_timer			
			);
		} else {
			return false;
		}		
	}
	
	public function getSpecialOffers($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "special_offer s LEFT JOIN " . DB_PREFIX . "special_offer_description sd ON (s.special_offer_id = sd.special_offer_id) LEFT JOIN " . DB_PREFIX . "special_offer_to_store s2s ON (s.special_offer_id = s2s.special_offer_id) WHERE sd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND s2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND s.list_customer_group_id LIKE '%" . $this->config->get('config_customer_group_id') . "%'";

		if (isset($data['show_ended']) && ($data['show_ended'] == '0')) {
			$sql .= " AND ((s.date_start = '0000-00-00' OR s.date_start < NOW()) AND (s.date_end = '0000-00-00' OR s.date_end > NOW())) ORDER BY s.date_end ASC";
		} else {
			$sql .= " AND (s.date_start = '0000-00-00' OR s.date_start < NOW()) ORDER BY s.date_end DESC";
		}	
		
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}
	
	public function getCategoriesInSpecials($special_offer_id=0) {
		$sql = "SELECT cd.name, p2c.category_id AS category_id FROM " . DB_PREFIX . "product_to_category p2c LEFT JOIN " . DB_PREFIX . "category_description cd ON (p2c.category_id = cd.category_id) WHERE cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p2c.product_id IN (SELECT ps.product_id FROM " . DB_PREFIX . "product_special ps LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (ps.product_id = p2s.product_id) WHERE p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW()))";
		
		if (!empty($special_offer_id)) {	
			$sql .= " AND ps.special_offer_id = '" . $special_offer_id . "'";
		}
		$sql .= ") GROUP BY category_id ORDER BY cd.name";
		
		$query = $this->db->query($sql);
		return $query->rows;		
	}						
}