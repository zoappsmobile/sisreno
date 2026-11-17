<?php
class SupabaseClient {
    private $url;
    private $key;

    public function __construct($url, $key) {
        $this->url = rtrim($url, '/');
        $this->key = $key;
    }

    private function request($method, $endpoint, $data = null) {
        $ch = curl_init();
        $headers = [
            'apikey: ' . $this->key,
            'Authorization: Bearer ' . $this->key,
            'Content-Type: application/json',
            'Prefer: return=representation'
        ];

        $url = $this->url . '/rest/v1/' . $endpoint;

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($data !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($http_code >= 400) {
            throw new Exception("Supabase API Error ($http_code): " . $response . ($error ? " | cURL: $error" : ""));
        }

        return json_decode($response, true);
    }

    public function select($table, $columns = '*', $filters = [], $order = null) {
        $query = http_build_query($filters);
        $endpoint = $table . '?select=' . urlencode($columns) . ($query ? '&' . $query : '');
        if ($order) {
            $endpoint .= '&order=' . urlencode($order);
        }
        return $this->request('GET', $endpoint);
    }

    public function insert($table, $data) {
        return $this->request('POST', $table, $data);
    }

    public function upsert($table, $data, $on_conflict) {
        $ch = curl_init();
        $headers = [
            'apikey: ' . $this->key,
            'Authorization: Bearer ' . $this->key,
            'Content-Type: application/json',
            'Prefer: resolution=merge-duplicates'
        ];
        $url = $this->url . '/rest/v1/' . $table . '?on_conflict=' . $on_conflict;
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($http_code >= 400) {
            throw new Exception("Supabase Upsert Error: " . $response);
        }
        return json_decode($response, true);
    }

    public function update($table, $data, $filters) {
        $query = http_build_query($filters);
        $endpoint = $table . '?' . $query;
        return $this->request('PATCH', $endpoint, $data);
    }

    public function delete($table, $filters) {
        $query = http_build_query($filters);
        $endpoint = $table . '?' . $query;
        return $this->request('DELETE', $endpoint);
    }

    public function fetchOne($table, $columns = '*', $filters = []) {
        $result = $this->select($table, $columns, $filters);
        return !empty($result) ? $result[0] : null;
    }
}
?>