<?php
class ControllerExtensionShippingSpecialFree extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/shipping/specialfree');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('shipping_specialfree', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true));			
		}

		$data['heading_title'] = $this->language->get('heading_smart_title');
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_smart_title'),
			'href' => $this->url->link('extension/shipping/specialfree', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/shipping/specialfree', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true);

		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();
		

		if (isset($this->request->post['shipping_specialfree_description'])) {
			$data['shipping_specialfree_description'] = $this->request->post['shipping_specialfree_description'];
		} else {
			$data['shipping_specialfree_description'] = $this->config->get('shipping_specialfree_description');
		}
		
		if (isset($this->request->post['specialfree_total'])) {
			$data['shipping_specialfree_total'] = $this->request->post['shipping_specialfree_total'];
		} else {
			$data['shipping_specialfree_total'] = $this->config->get('shipping_specialfree_total');
		}

		if (isset($this->request->post['specialfree_geo_zone_id'])) {
			$data['shipping_specialfree_geo_zone_id'] = $this->request->post['shipping_specialfree_geo_zone_id'];
		} else {
			$data['shipping_specialfree_geo_zone_id'] = $this->config->get('shipping_specialfree_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['shipping_specialfree_status'])) {
			$data['shipping_specialfree_status'] = $this->request->post['shipping_specialfree_status'];
		} else {
			$data['shipping_specialfree_status'] = $this->config->get('shipping_specialfree_status');
		}

		if (isset($this->request->post['shipping_specialfree_sort_order'])) {
			$data['shipping_specialfree_sort_order'] = $this->request->post['shipping_specialfree_sort_order'];
		} else {
			$data['shipping_specialfree_sort_order'] = $this->config->get('shipping_specialfree_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/shipping/specialfree', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/shipping/specialfree')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}