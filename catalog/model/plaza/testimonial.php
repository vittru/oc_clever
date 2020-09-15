<?php
class ModelPlazaTestimonial extends Model
{
    public function getTestimonial($testimonial_id) {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "pttestimonial t LEFT JOIN " . DB_PREFIX . "pttestimonial_description td ON (t.pttestimonial_id = td.pttestimonial_id) WHERE t.pttestimonial_id = '" . (int) $testimonial_id . "'  AND t.status = '1'");
        return $query->row;
    }

    public function getTestimonials($start = 0, $limit = 10) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "pttestimonial_description td LEFT JOIN " . DB_PREFIX . "pttestimonial t ON (t.pttestimonial_id = td.pttestimonial_id) WHERE t.status = '1' ORDER BY t.sort_order ASC LIMIT " . (int) $start . "," . (int) $limit);
        return $query->rows;
    }

    public function getTotalTestimonials() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "pttestimonial AS t WHERE t.status = '1'");
        return $query->row['total'];
    }
    
    public function addTestimonial($author, $text) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "pttestimonial SET status = 1, sort_order = 0");

        $testimonial_id = $this->db->getLastId();

        $this->db->query("INSERT INTO " . DB_PREFIX . "pttestimonial_description SET pttestimonial_id = '" . (int) $testimonial_id . "', language_id=2, customer_name = '" . $this->db->escape($author) . "', image = '', content = '" . $this->db->escape($text) . "'");

        $this->cache->delete('testimonial');
    }

}