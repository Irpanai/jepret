<?php

namespace App\Http\Requests;

use App\Models\Voucher;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveVoucherRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->has('code')) {
            $this->merge(['code' => Voucher::normalizeCode($this->string('code')->toString())]);
        }
    }

    public function authorize(): bool
    {
        return $this->user()?->role === 'superadmin';
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:50', 'regex:/^[A-Za-z0-9_-]+$/', Rule::unique('vouchers')->ignore($this->route('voucher'))],
            'name' => ['required', 'string', 'max:120'], 'description' => ['nullable', 'string', 'max:1000'],
            'scope' => ['required', Rule::in(['photo', 'subscription'])], 'discount_type' => ['required', Rule::in(['percentage', 'fixed'])],
            'discount_value' => ['required', 'integer', 'min:1', Rule::when($this->input('discount_type') === 'percentage', ['max:100'])],
            'maximum_discount' => ['nullable', 'integer', 'min:1'], 'minimum_order_amount' => ['required', 'integer', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'], 'per_user_limit' => ['nullable', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'date'], 'expires_at' => ['nullable', 'date', Rule::when($this->filled('starts_at'), ['after:starts_at'])], 'is_active' => ['nullable', 'boolean'],
            'applies_to_all_packages' => ['nullable', 'boolean'],
            'package_ids' => [
                Rule::excludeIf($this->input('scope') !== 'subscription' || $this->boolean('applies_to_all_packages')),
                'required',
                'array',
                'min:1',
            ],
            'package_ids.*' => ['integer', Rule::exists('packages', 'id')],
        ];
    }

    public function voucherData(): array
    {
        return array_merge($this->safe()->except(['is_active', 'applies_to_all_packages', 'package_ids']), [
            'code' => Voucher::normalizeCode($this->string('code')->toString()),
            'is_active' => $this->boolean('is_active'),
            'applies_to_all_subscription_packages' => $this->input('scope') !== 'subscription' || $this->boolean('applies_to_all_packages'),
        ]);
    }

    /** @return array<int, int> */
    public function selectedPackageIds(): array
    {
        if ($this->input('scope') !== 'subscription' || $this->boolean('applies_to_all_packages')) {
            return [];
        }

        return array_map('intval', $this->validated('package_ids', []));
    }
}
