<?php
class ControllerPlazaTestimonial extends Controller
{
    public function add() {
        $this->load->language('plaza/module/pttestimonial');

        $this->load->model('plaza/testimonial');

        $json = array();

        if(isset($this->request->post['author'])) {
            $author = $this->request->post['author'];
        } else {
            $author = '';
        }
        
        if (isset($this->request->post['text'])) {
            $text = $this->request->post['text'];
        } else {
            $text = '';
        }

        if($author && $text) {
            $this->model_plaza_testimonial->addTestimonial($author, $text);
            $json['status'] = true;
            $json['success'] = $this->language->get('testimonial_added');
        } else {
            $json['status'] = false;
            $json['error'] = $this->language->get('error_empty_field');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}