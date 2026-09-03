<?php
use App\Staff;
use App\College;

$college_staff = function () {
    $code = $_GET['code'] ?? $this->_request['code'] ?? '';
    if (!$code) {
        $this->response($this->json(['error' => 'Missing college code']), 400);
    }

    $college = College::getByCode($code);
    if (!$college) {
        $this->response($this->json(['error' => 'College not found']), 404);
    }

    $staff = Staff::getByCollege($code);
    $this->response($this->json([
        'status'  => 'success',
        'college' => $college,
        'count'   => count($staff),
        'staff'   => $staff,
    ]), 200);
};
