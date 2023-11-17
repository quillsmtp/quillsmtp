<?php

namespace QuillSMTP\Vendor;

use QuillSMTP\Vendor\SendGrid\EventWebhook\EventWebhook;
use QuillSMTP\Vendor\SendGrid\EventWebhook\EventWebhookHeader;
function isValidSignature($request)
{
    $publicKey = 'base64-encoded public key';
    $eventWebhook = new EventWebhook();
    $ecPublicKey = $eventWebhook->convertPublicKeyToECDSA($publicKey);
    return $eventWebhook->verifySignature($ecPublicKey, $request->getContent(), $request->header(EventWebhookHeader::SIGNATURE), $request->header(EventWebhookHeader::TIMESTAMP));
}
