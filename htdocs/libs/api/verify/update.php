<?php
use App\Verification;
use Aether\Session;

$update = function () {
    if (!$this->paramsExists(['staff_sno', 'college_code', 'status'])) {
        $this->response($this->json(['error' => 'Missing parameters']), 400);
    }

    $staffSno    = (int)$this->_request['staff_sno'];
    $collegeCode = (string)$this->_request['college_code'];
    $status      = strtolower(trim((string)$this->_request['status']));
    
    $userId = Session::isAuthenticated() ? Session::getUser()->getID() : null;
    $phone  = $this->_request['phone'] ?? null;
    $email  = $this->_request['email'] ?? null;

    if (!in_array($status, ['yes', 'no'])) {
        $this->response($this->json(['error' => 'Status must be yes or no']), 400);
    }

    $success = Verification::updateStatus($staffSno, $collegeCode, $status, $userId, $phone, $email);
    $this->response($this->json([
        'status'  => $success ? 'success' : 'failed',
        'message' => 'Status updated successfully',
    ]), $success ? 200 : 500);
};
