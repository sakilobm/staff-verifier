<?php
use App\College;

$colleges = function () {
    $search = $_GET['search'] ?? $this->_request['search'] ?? '';
    $filter = $_GET['filter'] ?? $this->_request['filter'] ?? 'all';

    $data = College::getAllWithStats($search, $filter);
    $this->response($this->json([
        'status'   => 'success',
        'count'    => count($data),
        'colleges' => $data,
    ]), 200);
};
