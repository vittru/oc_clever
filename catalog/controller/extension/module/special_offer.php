<?php
class ControllerExtensionModuleSpecialOffer extends Controller {
	public function index($setting) {
		$this->load->language('extension/module/special_offer');

		$this->load->model('extension/module/special_offer');
		$this->load->model('tool/image');
		
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_days'] = $this->language->get('text_days');
		$data['text_hours'] = $this->language->get('text_hours');
		$data['text_minutes'] = $this->language->get('text_minutes');
		$data['text_seconds'] = $this->language->get('text_seconds');
		$data['text_ended'] = $this->language->get('text_ended');
		$data['text_before_deadline'] = $this->language->get('text_before_deadline');	
		$data['text_continuous_offer'] = $this->language->get('text_continuous_offer');	
		
		$data['button_more_detailed'] = $this->language->get('button_more_detailed');
		$data['link_special'] = $this->url->link('product/special');

		$data['special_offers']	= array();

		$filter_special_offers = array(
			'show_ended'  => $this->config->get('module_special_offer_show_ended_offers'),
			'start' => 0,			
			'limit' => $this->config->get('module_special_offer_module_limit')
		);		
		
		
		$results = $this->model_extension_module_special_offer->getSpecialOffers($filter_special_offers);
		
		if ($results) {
			foreach ($results as $result) {
				if(!empty($result['image'])) {
					$image = $result['image'];
				} else {
				   $image = "no-image-placeholder.png";
				}
				
				if (!empty($result['cycle_of_timer']) and (($result['date_end'] == '0000-00-00') or (strtotime($result['date_end'])>time())) ) {
						$cycle_of_timer = $result['cycle_of_timer'];
						$diff = time() - strtotime( $result['date_start'] );					
						$diff = (floor($diff/$cycle_of_timer)+1) * $cycle_of_timer;
						$stop_timer=date('m/d/Y G:i:s', strtotime( $result['date_start'].'+'.$diff.' seconds' ));
				} else {
					if ($result['date_end'] == '0000-00-00') {
						$stop_timer = '0000-00-00';
					} else { 
						$stop_timer = date('m/d/Y G:i:s', strtotime( $result['date_end'] ));
					}
				}
				
				if ($result['date_start'] != '0000-00-00') {
					$result['date_start'] = date('m/d/Y G:i:s', strtotime( $result['date_start'] ));
				}

				$data['special_offers'][] = array(
					'special_offer_id' => $result['special_offer_id'],
					'name'        => $result['name'],
					'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, 160) . '..',
					'date_start'  => $result['date_start'],	
					'date_end'    => $stop_timer,		
					'href'        => $this->url->link('extension/module/special_offer/info', 'special_offer_id=' . $result['special_offer_id']),
					'thumb'       => $this->model_tool_image->resize($image, 350, 300),
				);
			}

			return $this->load->view('extension/module/special_offer', $data);
		}
	}
	
	public function offerlist() {
		$this->load->language('extension/module/special_offer');

		$this->load->model('extension/module/special_offer');
		$this->load->model('tool/image');
		

		$this->document->setTitle($this->language->get('heading_title'));
		$data['link_special'] = $this->url->link('product/special');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/special_offer/offerlist')
		);

		$data['special_offers']	= array();
		$filter_special_offers = array();
		$filter_special_offers['show_ended'] = $this->config->get('module_special_offer_show_ended_offers');
		
		$results = $this->model_extension_module_special_offer->getSpecialOffers($filter_special_offers);

		foreach ($results as $result) {
		    if(!empty($result['image'])) {
                $image = $result['image'];
            } else {
               $image = "no-image-placeholder.png";
            }
			
			if (!empty($result['cycle_of_timer']) and (($result['date_end'] == '0000-00-00') or (strtotime($result['date_end'])>time())) ) {
					$cycle_of_timer = $result['cycle_of_timer'];
					$diff = time() - strtotime( $result['date_start'] );					
					$diff = (floor($diff/$cycle_of_timer)+1) * $cycle_of_timer;
					$stop_timer=date('m/d/Y G:i:s', strtotime( $result['date_start'].'+'.$diff.' seconds' ));
            } else {
 				if ($result['date_end'] == '0000-00-00') {
					$stop_timer = '0000-00-00';
				} else { 
					$stop_timer = date('m/d/Y G:i:s', strtotime( $result['date_end'] ));
 				}
            }
			
			if ($result['date_start'] != '0000-00-00') {
				$result['date_start'] = date('m/d/Y G:i:s', strtotime( $result['date_start'] ));
			}
			
			$data['special_offers'][] = array(
				'special_offer_id' => $result['special_offer_id'],
				'name'        => $result['name'],
				'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, 160) . '..',
                'date_start'  => $result['date_start'],	
                'date_end'    => $stop_timer,		
				'href'        => $this->url->link('extension/module/special_offer/info', 'special_offer_id=' . $result['special_offer_id']),
                'thumb'       => $this->model_tool_image->resize($image, 350, 300),
            );
		}

		$data['continue'] = $this->url->link('common/home');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('extension/module/special_offer_list', $data));
	}

	public function info() {
		$this->load->language('extension/module/special_offer');

		$this->load->model('extension/module/special_offer');
		$this->load->model('catalog/product');
		$this->load->model('tool/image');
		
		$text_all_categories = $this->language->get('text_all_categories');				
		
		if (isset($this->request->get['special_offer_id'])) {
			$special_offer_id = (int)$this->request->get['special_offer_id'];
		} else {
			$special_offer_id = 0;
		}
		
		$data['go_back'] = $this->url->link('extension/module/special_offer/offerlist');
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/special_offer/offerlist')
		);

		$special_offer_info = $this->model_extension_module_special_offer->getSpecialOffer($special_offer_id);

		if ($special_offer_info) {
			$url = '';
			
			if (isset($this->request->get['sort'])) {
				$sort = $this->request->get['sort'];
			} else {
				$sort = 'p.sort_order';
			}

			if (isset($this->request->get['order'])) {
				$order = $this->request->get['order'];
			} else {
				$order = 'ASC';
			}

			if (isset($this->request->get['page'])) {
				$page = $this->request->get['page'];
			} else {
				$page = 1;
			}

			if (isset($this->request->get['limit'])) {
				$limit = (int)$this->request->get['limit'];
			} else {
				$limit = (int)$this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit');
			}

			if (isset($this->request->get['categ'])) {
				$categ = $this->request->get['categ'];
			} else {
				$categ = 0;
			}					

			$data['breadcrumbs'][] = array(
				'text' => $special_offer_info['name'],
				'href' => $this->url->link('extension/module/special_offer/info', 'special_offer_id=' . $special_offer_id . $url)
			);			
			
			$this->document->setTitle($special_offer_info['meta_title']);
			$this->document->setDescription($special_offer_info['meta_description']);
			$this->document->setKeywords($special_offer_info['meta_keyword']);
			
			$data['heading_title'] = $special_offer_info['name'];
			$data['name'] = $special_offer_info['name'];			
            $data['description'] = html_entity_decode($special_offer_info['description'], ENT_QUOTES, 'UTF-8');
			
			if (!empty($special_offer_info['cycle_of_timer']) and (($special_offer_info['date_end'] == '0000-00-00') or (strtotime($special_offer_info['date_end'])>time())) ) {
					$cycle_of_timer = $special_offer_info['cycle_of_timer'];
					$diff = time() - strtotime( $special_offer_info['date_start'] );					
					$diff = (floor($diff/$cycle_of_timer)+1) * $cycle_of_timer;
					$stop_timer=date('m/d/Y G:i:s', strtotime( $special_offer_info['date_start'].'+'.$diff.' seconds' ));
            } else {
 				if ($special_offer_info['date_end'] == '0000-00-00') {
					$stop_timer = '0000-00-00';
				} else { 
					$stop_timer = date('m/d/Y G:i:s', strtotime( $special_offer_info['date_end'] ));
 				}
            }	
			
			$data['date_end'] = $stop_timer;			
			
			if ($special_offer_info['date_start'] == '0000-00-00') {
				$data['date_start'] = '0000-00-00';
			} else {
				$data['date_start'] = date('m/d/Y G:i:s', strtotime( $special_offer_info['date_start'] ));
			}
			
            if(!empty($special_offer_info['image'])) {
                $special_offer_image = $special_offer_info['image'];
            } else {
                $special_offer_image = 'no-image-special_offer.jpg';
            }
			
            $data['image'] = $this->model_tool_image->resize($special_offer_image, 350, 300);
			
            if(!empty($special_offer_info['image_label'])) {
                $data['special_offer_label'] = $this->model_tool_image->resize($special_offer_info['image_label'], 100, 100);
            } else {
                $data['special_offer_label'] = '';
            }
			
			$data['gift'] = array();
			if (($special_offer_info['offer_type'] == 1) && (!empty($special_offer_info['gift_product_id']))) {
				$gift_info = $this->model_catalog_product->getProduct($special_offer_info['gift_product_id']);
				
				if ($gift_info['image']) {
					$gift_image = $this->model_tool_image->resize($gift_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_cart_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_cart_height'));
				} else {
					$gift_image = '';
				}					

				$data['gift'] = array(
					'thumb'     => $gift_image,
					'name'      => $gift_info['name'],
					'href'      => $this->url->link('product/product', 'product_id=' . $special_offer_info['gift_product_id'])
				);						
			}							
			
			$data['special_offer_id'] = $special_offer_id;
			
			$data['text_compare'] = sprintf($this->language->get('text_compare'), (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0));


			$data['compare'] = $this->url->link('product/compare');

			$data['products'] = array();

			$filter_data = array(
				'filter_category_id' => $categ,		
				'filter_special_offer_id' => $special_offer_id,
				'sort'                   => $sort,
				'order'                  => $order,
				'start'                  => ($page - 1) * $limit,
				'limit'                  => $limit
			);

			$product_total = $this->model_catalog_product->getTotalProductSpecials($filter_data);

			$results = $this->model_catalog_product->getProductSpecials($filter_data);

			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
				}

				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				if ((float)$result['special']) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$special = false;
				}

				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
				} else {
					$tax = false;
				}

				if ($this->config->get('config_review_status')) {
					$rating = (int)$result['rating'];
				} else {
					$rating = false;
				}

				$data['products'][] = array(
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'name'        => $result['name'],
					'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
					'price'       => $price,
					'special'     => $special,
					'tax'         => $tax,
					'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
					'rating'      => $result['rating'],
					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'] . $url)					
				);
			}

			$url = '';

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}
			
			if (isset($this->request->get['categ'])) {
				$url .= '&categ=' . $this->request->get['categ'];
			}							

			$data['sorts'] = array();

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_default'),
				'value' => 'p.sort_order-ASC',
				'href'  => $this->url->link('extension/module/special_offer/info', 'special_offer_id=' . $special_offer_id . '&sort=p.sort_order&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_name_asc'),
				'value' => 'pd.name-ASC',
				'href'  => $this->url->link('extension/module/special_offer/info', 'special_offer_id=' . $special_offer_id . '&sort=pd.name&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_name_desc'),
				'value' => 'pd.name-DESC',
				'href'  => $this->url->link('extension/module/special_offer/info', 'special_offer_id=' . $special_offer_id . '&sort=pd.name&order=DESC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_price_asc'),
				'value' => 'p.price-ASC',
				'href'  => $this->url->link('extension/module/special_offer/info', 'special_offer_id=' . $special_offer_id . '&sort=p.price&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_price_desc'),
				'value' => 'p.price-DESC',
				'href'  => $this->url->link('extension/module/special_offer/info', 'special_offer_id=' . $special_offer_id . '&sort=p.price&order=DESC' . $url)
			);

			if ($this->config->get('config_review_status')) {
				$data['sorts'][] = array(
					'text'  => $this->language->get('text_rating_desc'),
					'value' => 'rating-DESC',
					'href'  => $this->url->link('extension/module/special_offer/info', 'special_offer_id=' . $special_offer_id . '&sort=rating&order=DESC' . $url)
				);

				$data['sorts'][] = array(
					'text'  => $this->language->get('text_rating_asc'),
					'value' => 'rating-ASC',
					'href'  => $this->url->link('extension/module/special_offer/info', 'special_offer_id=' . $special_offer_id . '&sort=rating&order=ASC' . $url)
				);
			}

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_model_asc'),
				'value' => 'p.model-ASC',
				'href'  => $this->url->link('extension/module/special_offer/info', 'special_offer_id=' . $special_offer_id . '&sort=p.model&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_model_desc'),
				'value' => 'p.model-DESC',
				'href'  => $this->url->link('extension/module/special_offer/info', 'special_offer_id=' . $special_offer_id . '&sort=p.model&order=DESC' . $url)
			);

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}
			
			if (isset($this->request->get['categ'])) {
				$url .= '&categ=' . $this->request->get['categ'];
			}							

			$data['limits'] = array();

			$limits = array_unique(array($this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit'), 25, 50, 75, 100));

			sort($limits);

			foreach($limits as $value) {
				$data['limits'][] = array(
					'text'  => $value,
					'value' => $value,
					'href'  => $this->url->link('extension/module/special_offer/info', 'special_offer_id=' . $special_offer_id . $url . '&limit=' . $value)
				);
			}

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			// Categories with special products
			$categories = $this->model_extension_module_special_offer->getCategoriesInSpecials($special_offer_id);
			$data['categories'] = array();
			
			$data['categories'][0] = array(
				'category_id' => '0',
				'name'        => $text_all_categories,
				'href'  	  => $this->url->link('extension/module/special_offer/info', 'special_offer_id=' . $special_offer_id . $url)			
			);		

			foreach ($categories as $category_info) {
					$data['categories'][] = array(
						'category_id' => $category_info['category_id'],
						'name'        => $category_info['name'],	
						'href'  => $this->url->link('extension/module/special_offer/info', 'special_offer_id=' . $special_offer_id . $url . '&categ=' . $category_info['category_id'])					
					);
			}		

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}
			
			if (isset($this->request->get['categ'])) {
				$url .= '&categ=' . $this->request->get['categ'];
			}									
			
			$pagination = new Pagination();
			$pagination->total = $product_total;
			$pagination->page = $page;
			$pagination->limit = $limit;
			$pagination->url = $this->url->link('extension/module/special_offer/info', 'special_offer_id=' . $special_offer_id .  $url . '&page={page}');

			$data['pagination'] = $pagination->render();

			$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($product_total - $limit)) ? $product_total : ((($page - 1) * $limit) + $limit), $product_total, ceil($product_total / $limit));

			// http://googlewebmastercentral.blogspot.com/2011/09/pagination-with-relnext-and-relprev.html
			if ($page == 1) {
			    $this->document->addLink($this->url->link('extension/module/special_offer/info', 'special_offer_id=' . $special_offer_id, true), 'canonical');
			} elseif ($page == 2) {
			    $this->document->addLink($this->url->link('extension/module/special_offer/info', 'special_offer_id=' . $special_offer_id, true), 'prev');
			} else {
			    $this->document->addLink($this->url->link('extension/module/special_offer/info', 'special_offer_id=' . $special_offer_id . $url . '&page='. ($page - 1), true), 'prev');
			}

			if ($limit && ceil($product_total / $limit) > $page) {
			    $this->document->addLink($this->url->link('extension/module/special_offer/info', 'special_offer_id=' . $special_offer_id . $url . '&page='. ($page + 1), true), 'next');
			}

			$data['sort'] = $sort;
			$data['order'] = $order;
			$data['limit'] = $limit;
			$data['categ'] = $categ;					

			$data['continue'] = $this->url->link('common/home');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('extension/module/special_offer_info', $data));
		} else {
			$url = '';

			if (isset($this->request->get['special_offer_id'])) {
				$url .= '&special_offer_id=' . $this->request->get['special_offer_id'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_error'),
				'href' => $this->url->link('extension/module/special_offer/info', $url)
			);

			$this->document->setTitle($this->language->get('text_error'));

			$data['heading_title'] = $this->language->get('text_error');

			$data['text_error'] = $this->language->get('text_error');

			$data['button_back'] = $this->language->get('button_back');

			$data['continue'] = $this->url->link('common/home');

			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

			$data['header'] = $this->load->controller('common/header');
			$data['footer'] = $this->load->controller('common/footer');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');

			$this->response->setOutput($this->load->view('error/not_found', $data));
		}
	}
	
	public function modalOfferInfoByID() {
		$this->load->model('extension/module/special_offer');

		if (isset($this->request->get['offer_id'])) {
			$offer_id = $this->request->get['offer_id'];
		} else {
			$offer_id = 0;
		}

		$special_offer_info = $this->model_extension_module_special_offer->getSpecialOffer($offer_id);

		if ($special_offer_info) {
			
			$this->load->language('extension/module/special_offer');
			$this->load->model('catalog/product');			
			$this->load->model('tool/image');

			$data['text_special_gift'] = $this->language->get('text_special_gift');			
		
			$data['heading_title'] = $special_offer_info['name'];
			$data['name'] = $special_offer_info['name'];			
            $data['description'] = html_entity_decode($special_offer_info['description'], ENT_QUOTES, 'UTF-8');
			
			if ($special_offer_info['date_end'] == '0000-00-00') {
				$data['date_end'] = '0000-00-00';
			} else {
				$data['date_end'] = date('m/d/Y G:i:s', strtotime( $special_offer_info['date_end'] ));
			}			
						
            if(!empty($special_offer_info['image'])) {
                $special_offer_image = $special_offer_info['image'];
            } else {
                $special_offer_image = 'no-image-special_offer.jpg';
            }
			
            $data['image'] = $this->model_tool_image->resize($special_offer_image, 350, 300);
			
			$data['gift'] = array();
			if (($special_offer_info['offer_type'] == 1) && (!empty($special_offer_info['gift_product_id']))) {
				$gift_info = $this->model_catalog_product->getProduct($special_offer_info['gift_product_id']);
				
				if ($gift_info['image']) {
					$gift_image = $this->model_tool_image->resize($gift_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_cart_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_cart_height'));
				} else {
					$gift_image = '';
				}					

				$data['gift'] = array(
					'thumb'     => $gift_image,
					'name'      => $gift_info['name'],
					'href'      => $this->url->link('product/product', 'product_id=' . $special_offer_info['gift_product_id'])
				);						
			}										
			
			$this->response->setOutput($this->load->view('extension/module/som_modal_offerinfo', $data));			

		}
	}	

	public function offerLabel($product) {
		$special_info = $product['special_offer_info'];
		$special_label = '';
		if (!empty($special_info['special_offer_id'])) {		
			if(!empty($special_info['image_label'])) {
				$special_label='<div class="special-offer-label"><img src="' . $this->model_tool_image->resize($special_info['image_label'], 100, 100) . '" /></div>';
			} else {
				if (($special_info['offer_type'] == 1) && ($this->config->get('module_special_offer_giftphoto_as_label') == 1)) {
					$this->load->language('extension/module/special_offer_ext');
					$text_gift = $this->language->get('text_gift');
					
					if (!empty($special_info['gift_product_id'])) {
						$gift_info = $this->model_catalog_product->getProduct($special_info['gift_product_id']);
						if ($gift_info['image']) {
							$special_label='<div class="special-offer-label som-label-gift"><div class="gift-header">' . $text_gift . '</div><img src="' . $this->model_tool_image->resize($gift_info['image'], 100, 100) . '" /></div>';
						}
					} else {
						if ($product['image']) {
							$special_label='<div class="special-offer-label som-label-gift"><div class="gift-header">' . $text_gift . '</div><img src="' . $this->model_tool_image->resize($product['image'], 100, 100) . '" /></div>';
						}
					}
				}
			}
		}
		return $special_label;
	}	

	public function GiftsInCart() {
		$this->load->model('tool/image');
		$this->load->model('catalog/product');	
		$this->load->model('extension/module/special_offer');	
			
		$gifts = array();
		$gift_offers = array();
		$data['products'] = array();

		foreach ($this->cart->getProducts() as $product) {
			$special_offer_id = $this->model_extension_module_special_offer->getProductSpecialOfferID($product['product_id']);	
			$gift_quantity = 0;
			if (!empty($special_offer_id)) {
				$special_offer_info = $this->model_extension_module_special_offer->getSpecialOffer($special_offer_id);
				if ($special_offer_info['offer_type'] == 1) { 
					if (!empty($special_offer_info['gift_product_id'])) { 
						if (array_key_exists($special_offer_id, $gift_offers)) { 
							$gift_offers[$special_offer_id]['qty'] += $product['quantity'];
						} else {
							$gift_offers[$special_offer_id]['qty'] = $product['quantity'];
							$gift_offers[$special_offer_id]['offer_id'] = $special_offer_id;
							$gift_offers[$special_offer_id]['gift_id'] = $special_offer_info['gift_product_id'];
							$gift_offers[$special_offer_id]['product_quantity'] = $special_offer_info['product_quantity'];
							$gift_offers[$special_offer_id]['gift_quantity'] = $special_offer_info['gift_quantity'];
							$gift_offers[$special_offer_id]['offer_name'] = $special_offer_info['name'];
						}
					} else {
						$gift_quantity=intval($product['quantity']/$special_offer_info['product_quantity'])*$special_offer_info['gift_quantity'];
				
						if ($gift_quantity>0) {
							
							if ($product['image']) {
								$gift_image = $this->model_tool_image->resize($product['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_cart_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_cart_height'));
							} else {
								$gift_image = '';
							}
							
							$gifts[] = array(
								'product_id'    => $product['product_id'],
								'thumb'     	=> $gift_image,
								'name'      	=> $product['name'],
								'model'     	=> $product['model'],
								'quantity'  	=> $gift_quantity,
								'price'     	=> 0,
								'total'     	=> 0,
								'offer_name' 	=> $special_offer_info['name'],
								'href'      	=> $this->url->link('product/product', 'product_id=' . $product['product_id'])
							);
						}
					}
				}
			}								
		}

		foreach ($gift_offers as $gift_offer) {
			$gift_quantity=$gift_offer['gift_quantity']*intval($gift_offer['qty']/$gift_offer['product_quantity']);
			
			if ($gift_quantity>0) {
				$gift_info = $this->model_catalog_product->getProduct($gift_offer['gift_id']);
				
				if ($gift_info['image']) {
					$gift_image = $this->model_tool_image->resize($gift_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_cart_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_cart_height'));
				} else {
					$gift_image = '';
				}
			
				$gifts[] = array(
					'product_id'    => $gift_offer['gift_id'],				
					'thumb'     	=> $gift_image,
					'name'      	=> $gift_info['name'],
					'model'     	=> $gift_info['model'],
					'quantity'  	=> $gift_quantity,
					'price'     	=> 0,
					'total'     	=> 0,
					'offer_name' 	=> $gift_offer['offer_name'],
					'href'      	=> $this->url->link('product/product', 'product_id=' . $gift_offer['gift_id'])
				);
			}
		}
		return $gifts;
	}			
}
