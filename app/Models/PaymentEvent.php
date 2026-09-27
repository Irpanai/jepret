<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['subscription_order_id', 'photo_order_id', 'provider', 'event_key', 'provider_transaction_id', 'provider_status', 'gross_amount', 'signature_valid', 'payload', 'processing_result', 'provider_event_at'])]
class PaymentEvent extends Model
{
    protected function casts(): array
    {
        return ['payload' => 'array', 'signature_valid' => 'boolean', 'provider_event_at' => 'datetime'];
    }
}
