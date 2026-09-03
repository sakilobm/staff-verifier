<?php
use App\Verification;

$clear_college = function () {
    $code = $this->_request['code'] ?? $_GET['code'] ?? '';
    if (!$code) {
        $this->response($this->json(['error' => 'Missing college code']), 400);
    }

    $deleted = Verification::clearCollege($code);
    $this->response($this->json([
        'status'  => 'success',
        'deleted' => $deleted,
        'message' => "Cleared verifications for college {$code}",
    ]), 200);
};
