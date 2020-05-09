<?php
class ModelExtensionModuleSpecialOffer extends Model {
    public function addSpecialOffer($data) {

		
		$list_customer_group_id = "," . implode(",", $data['special_offer_customer_group']) . ",";
        $this->db->query("INSERT INTO " . DB_PREFIX . "special_offer SET list_customer_group_id = '" . $list_customer_group_id . "', offer_type = '" . (int)$data['offer_type'] . "', gift_product_id = '" . (int)$data['gift_product_id'] . "', gift_quantity = '" . (int)$data['gift_quantity'] . "', product_quantity = '" . (int)$data['product_quantity'] . "', date_start = '" . $this->db->escape($data['date_start']) . "', date_end = '" . $this->db->escape($data['date_end']) . "', cycle_of_timer = '" . (int)$data['cycle_of_timer'] . "', priority = '" . (int)$data['priority'] . "'");
 
        $special_offer_id = $this->db->getLastId();

        if (isset($data['image'])) {
            $this->db->query("UPDATE " . DB_PREFIX . "special_offer SET image = '" . $this->db->escape($data['image']) . "' WHERE special_offer_id = '" . (int)$special_offer_id . "'");
        }
		
        if (isset($data['image_label'])) {
            $this->db->query("UPDATE " . DB_PREFIX . "special_offer SET image_label = '" . $this->db->escape($data['image_label']) . "' WHERE special_offer_id = '" . (int)$special_offer_id . "'");
        }		

		foreach ($data['special_offer_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "special_offer_description SET special_offer_id = '" . (int)$special_offer_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}		
		
        if (isset($data['special_offer_store'])) {
            foreach ($data['special_offer_store'] as $store_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "special_offer_to_store SET special_offer_id = '" . (int)$special_offer_id . "', store_id = '" . (int)$store_id . "'");
            }
        }

		if (isset($data['special_offer_seo_url'])) {
			foreach ($data['special_offer_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'special_offer_id=" . (int)$special_offer_id . "', keyword = '" . $this->db->escape($keyword) . "'");
					}
				}
			}
		}
		
		/* Table Product_Special */
		if (isset($data['product'])) {
			foreach ($data['product'] as $product) {
				if (isset($product['status'])) {
					$status=1;
				} else {
					$status=0;					
				}				

				foreach ($data['special_offer_customer_group'] as $customer_group_id) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product['product_id'] . "', special_offer_id = '" . (int)$special_offer_id . "', customer_group_id = '" . (int)$customer_group_id . "', priority = '" . (int)$data['priority'] . "', price = '" . (float)$product['special_price'] . "', date_start = '" . $this->db->escape($data['date_start']) . "', date_end = '" . $this->db->escape($data['date_end']) . "'");
				}	
				
				$this->db->query("UPDATE " . DB_PREFIX . "product SET status = '" . (int)$status . "', date_modified = NOW() WHERE product_id = '" . (int)$product['product_id'] . "' AND status != '" . (int)$status . "'");
				}
		}		

        $this->cache->delete('special_offer');

        return $special_offer_id;
    }

    public function editSpecialOffer($special_offer_id, $data) {
		$list_customer_group_id = "," . implode(",", $data['special_offer_customer_group']) . ",";
        $this->db->query("UPDATE " . DB_PREFIX . "special_offer SET list_customer_group_id = '" . $list_customer_group_id . "', offer_type = '" . (int)$data['offer_type'] . "', gift_product_id = '" . (int)$data['gift_product_id'] . "', gift_quantity = '" . (int)$data['gift_quantity'] . "', product_quantity = '" . (int)$data['product_quantity'] . "', date_start = '" . $this->db->escape($data['date_start']) . "', date_end = '" . $this->db->escape($data['date_end']) . "', cycle_of_timer = '" . (int)$data['cycle_of_timer'] . "', priority = '" . (int)$data['priority'] . "' WHERE special_offer_id = '" . (int)$special_offer_id . "'");
        if (isset($data['image'])) {
            $this->db->query("UPDATE " . DB_PREFIX . "special_offer SET image = '" . $this->db->escape($data['image']) . "' WHERE special_offer_id = '" . (int)$special_offer_id . "'");
        }

        if (isset($data['image_label'])) {
            $this->db->query("UPDATE " . DB_PREFIX . "special_offer SET image_label = '" . $this->db->escape($data['image_label']) . "' WHERE special_offer_id = '" . (int)$special_offer_id . "'");
        }		

		$this->db->query("DELETE FROM " . DB_PREFIX . "special_offer_description WHERE special_offer_id = '" . (int)$special_offer_id . "'");

		foreach ($data['special_offer_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "special_offer_description SET special_offer_id = '" . (int)$special_offer_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}		
		
        $this->db->query("DELETE FROM " . DB_PREFIX . "special_offer_to_store WHERE special_offer_id = '" . (int)$special_offer_id . "'");

        if (isset($data['special_offer_store'])) {
            foreach ($data['special_offer_store'] as $store_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "special_offer_to_store SET special_offer_id = '" . (int)$special_offer_id . "', store_id = '" . (int)$store_id . "'");
            }
        }

		$this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE query = 'special_offer_id=" . (int)$special_offer_id . "'");

		if (isset($data['special_offer_seo_url'])) {
			foreach ($data['special_offer_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						$this->db->query("INSERT INTO `" . DB_PREFIX . "seo_url` SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'special_offer_id=" . (int)$special_offer_id . "', keyword = '" . $this->db->escape($keyword) . "'");
					}
				}
			}
		}
		
		/* Table Product_Special */
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE special_offer_id = '" . (int)$special_offer_id . "'");
		
		if (isset($data['product'])) {
			foreach ($data['product'] as $product) {
				
				if (isset($product['status'])) {
					$status=1;
				} else {
					$status=0;					
				}				

				foreach ($data['special_offer_customer_group'] as $customer_group_id) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product['product_id'] . "', special_offer_id = '" . (int)$special_offer_id . "', customer_group_id = '" . (int)$customer_group_id . "', priority = '" . (int)$data['priority'] . "', price = '" . (float)$product['special_price'] . "', date_start = '" . $this->db->escape($data['date_start']) . "', date_end = '" . $this->db->escape($data['date_end']) . "'");
				}	

				$this->db->query("UPDATE " . DB_PREFIX . "product SET status = '" . (int)$status . "', date_modified = NOW() WHERE product_id = '" . (int)$product['product_id'] . "' AND status != '" . (int)$status . "'");
			}
		}		

        $this->cache->delete('special_offer');
    }

    public function deleteSpecialOffer($special_offer_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "special_offer WHERE special_offer_id = '" . (int)$special_offer_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "special_offer_description WHERE special_offer_id = '" . (int)$special_offer_id . "'");		
        $this->db->query("DELETE FROM " . DB_PREFIX . "special_offer_to_store WHERE special_offer_id = '" . (int)$special_offer_id . "'");
  		$this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE query = 'special_offer_id=" . (int)$special_offer_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE special_offer_id = '" . (int)$special_offer_id . "'");		

        $this->cache->delete('special_offer');
    }

    public function getSpecialOffer($special_offer_id) {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "special_offer so LEFT JOIN " . DB_PREFIX . "special_offer_description sod ON (so.special_offer_id = sod.special_offer_id) WHERE so.special_offer_id = '" . (int)$special_offer_id . "' AND sod.language_id = '" . (int)$this->config->get('config_language_id') . "'");

        return $query->row;
    }

    public function getSpecialOffers($data = array()) {
        $sql = "SELECT * FROM " . DB_PREFIX . "special_offer so LEFT JOIN " . DB_PREFIX . "special_offer_description sod ON (so.special_offer_id = sod.special_offer_id) WHERE sod.language_id = '" . (int)$this->config->get('config_language_id') . "'";

        $sort_data = array(
            'name',
            'offer_type',
            'date_start',			
            'priority'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY name";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
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

	public function getSpecialOfferDescriptions($special_offer_id) {
		$special_offer_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "special_offer_description WHERE special_offer_id = '" . (int)$special_offer_id . "'");

		foreach ($query->rows as $result) {
			$special_offer_data[$result['language_id']] = array(
				'name'             => $result['name'],
				'description'      => $result['description'],				
				'meta_title'       => $result['meta_title'],
				'meta_description' => $result['meta_description'],
				'meta_keyword'     => $result['meta_keyword']
			);
		}

		return $special_offer_data;
	}	
	
    public function getSpecialOfferStores($special_offer_id) {
        $special_offer_store_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "special_offer_to_store WHERE special_offer_id = '" . (int)$special_offer_id . "'");

        foreach ($query->rows as $result) {
            $special_offer_store_data[] = $result['store_id'];
        }

        return $special_offer_store_data;
    }

	public function getSpecialOfferSeoUrls($special_offer_id) {
		$special_offer_seo_url_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'special_offer_id=" . (int)$special_offer_id . "'");

		foreach ($query->rows as $result) {
			$special_offer_seo_url_data[$result['store_id']][$result['language_id']] = $result['keyword'];
		}

		return $special_offer_seo_url_data;

	}	
	
    public function getTotalSpecialOffers() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "special_offer");

        return $query->row['total'];
    }
	
	public function setStatusProduct($product_id, $status) {
		$this->db->query("UPDATE " . DB_PREFIX . "product SET status = '" . (int)$status . "', date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
	}
	
	public function getProductsBySpecialOfferId($special_offer_id) {
 		$query = $this->db->query("SELECT DISTINCT p.product_id, p.status, p.quantity, ps.price AS special_price FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_special ps ON (p.product_id = ps.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND ps.special_offer_id = '" . (int)$special_offer_id . "' ORDER BY pd.name ASC");

		return $query->rows;
	}

	public function getProductsByFilter($data = array()) {
		$sql = "SELECT *, p.product_id AS product_id, p.price AS price, ps.price AS special_price";		

		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
			} else {
				$sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
			}

			$sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";

		} else {
			$sql .= " FROM " . DB_PREFIX . "product p";
		}

		$sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)  LEFT JOIN " . DB_PREFIX . "product_special ps ON (p.product_id = ps.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		
		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
		}
		
		if (isset($data['filter_special_status'])) {
			if ($data['filter_special_status'] == '1') { 
				$sql .= " AND p.product_id NOT IN (SELECT product_id FROM " . DB_PREFIX . "product_special)";	
			} elseif ($data['filter_special_status'] == '2') {
				$sql .= " AND p.product_id IN (SELECT product_id FROM " . DB_PREFIX . "product_special WHERE special_offer_id = '0')";
			} elseif (isset($data['filter_exclude_special_offer_id']) && ($data['filter_exclude_special_offer_id'] != '0' )) {

				$sql .= " AND p.product_id NOT IN (SELECT product_id FROM " . DB_PREFIX . "product_special WHERE special_offer_id = '" . (int)$data['filter_exclude_special_offer_id'] . "')";
			}
		}

		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
			} else {
				$sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
			}
		}
		
		if (!empty($data['filter_manufacturer_id'])) {
			$sql .= " AND p.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
		}
		
		if (!empty($data['filter_name'])) {
			$sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}	

		if (!empty($data['filter_sku'])) {
			$sql .= " AND p.sku LIKE '" . $this->db->escape($data['filter_sku']) . "%'";
		}								

		if (!empty($data['filter_min_price'])) {
			$sql .= " AND p.price >= '" . (int)$data['filter_min_price'] . "'";
		}	

		if (!empty($data['filter_max_price'])) {
			$sql .= " AND p.price <= '" . (int)$data['filter_max_price'] . "'";
		}
		
		$sql .= " GROUP BY p.product_id";

		$product_data = array();

		$query = $this->db->query($sql);
		
		return $query->rows;				

	}	

    public function getTotalProductsBySpecialOfferId($special_offer_id) {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_special WHERE special_offer_id = '" . (int)$special_offer_id . "'");

        return $query->row['total'];
    }	
	
    public function getSpecialOfferPageUrl() {
		$special_offer_seo_url_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = '" . $this->db->escape('extension/module/special_offer/offerlist') . "'");

		foreach ($query->rows as $result) {
			$special_offer_seo_url_data[$result['store_id']][$result['language_id']] = $result['keyword'];
		}

		return $special_offer_seo_url_data;		
    }

    public function setSpecialOfferPageUrl($special_offer_seo_url) {
		
		$this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE query = 'extension/module/special_offer/offerlist'");

		if (isset($special_offer_seo_url)) {
			foreach ($special_offer_seo_url as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						$this->db->query("INSERT INTO `" . DB_PREFIX . "seo_url` SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'extension/module/special_offer/offerlist', keyword = '" . $this->db->escape($keyword) . "'");
					}
				}
			}
		}	
    }

}
