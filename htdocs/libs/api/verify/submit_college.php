<?php
use App\Verification;
use Aether\Session;

$submit_college = function () {
    if (!$this->paramsExists(['code', 'answers'])) {
        $this->response($this->json(['error' => 'Missing code or answers']), 400);
    }

    $code    = (string)$this->_request['code'];
    $raw     = $this->_request['answers'];
    $answers = is_array($raw) ? $raw : json_decode($raw, true);

    if (!is_array($answers)) {
        $this->response($this->json(['error' => 'Invalid answers format']), 400);
    }

    $userId = Session::isAuthenticated() ? Session::getUser()->getID() : null;
    $phone  = $this->_request['phone'] ?? null;
    $email  = $this->_request['email'] ?? null;

    $updated = Verification::bulkSave($code, $answers, $userId, $phone, $email);
    $this->response($this->json([
        'status'  => 'success',
        'updated' => $updated,
        'message' => "Successfully saved {$updated} verifications",
    ]), 200);
};
