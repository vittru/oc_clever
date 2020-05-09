<?php
class ModelExtensionShippingSpecialFree extends Model {
	function getQuote($address) {
		$this->load->language('extension/shipping/specialfree');
		$this->load->model('extension/module/special_offer');

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('shipping_specialfree_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

		if (!$this->config->get('shipping_specialfree_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}

		if ($this->cart->getSubTotal() < $this->config->get('shipping_specialfree_total')) {
			$status = false;
		}

		$special_status = false;
		
		$ship_offers = array();
		$products = $this->cart->getProducts();
		foreach ($products as $product) {
			
			$special_offer_id = $this->model_extension_module_special_offer->getProductSpecialOfferID($product['product_id']);	
			if (!empty($special_offer_id)) {
				$special_offer_info = $this->model_extension_module_special_offer->getSpecialOffer($special_offer_id);
				if ($special_offer_info['offer_type'] == 2) {
					if (array_key_exists($special_offer_id, $ship_offers)) { 
						$ship_offers[$special_offer_id]['qty'] += $product['quantity'];
					} else {
						$ship_offers[$special_offer_id]['qty'] = $product['quantity'];
						$ship_offers[$special_offer_id]['offer_id'] = $special_offer_id;
						$ship_offers[$special_offer_id]['product_quantity'] = $special_offer_info['product_quantity'];
						$ship_offers[$special_offer_id]['offer_name'] = $special_offer_info['name'];
					}
				}
			}
		}
		
		foreach ($ship_offers as $ship_offer) {
			if ($ship_offer['qty']>=$ship_offer['product_quantity']) {
				$special_status = true;
				$offer_name = '&laquo;' . $ship_offer['offer_name'] . '&raquo;'; 
				break;					
			}	
		}

		if (!$special_status) {
			$status = false;
		}	

		$method_data = array();

		if ($status) {
			$quote_data = array();
			
			$description = $this->config->get('shipping_specialfree_description');
			$name = (!empty($description[$this->config->get('config_language_id')]['name'])) ? $description[$this->config->get('config_language_id')]['name'] : $this->language->get('text_default_name');
			$name = str_replace('{SOM}', $offer_name, $name); 
			$info = str_replace('{SOM}', $offer_name, $description[$this->config->get('config_language_id')]['info']); 
			$quote_data['specialfree'] = array(
				'code'         => 'specialfree.specialfree',
				'title'        => $name,
				'description'  => $info,
				'cost'         => 0.00,
				'tax_class_id' => 0,
				'text'         => $this->currency->format(0.00, $this->session->data['currency'])
			);

			$method_data = array(
				'code'       => 'specialfree',
				'title'      => $this->language->get('text_title'),
				'quote'      => $quote_data,
				'sort_order' => $this->config->get('shipping_specialfree_sort_order'),
				'error'      => false
			);
		}

		return $method_data;
	}
}