<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDonationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'donor_name' => $this->input('donor_name', $this->input('name')),
            'donor_email' => $this->input('donor_email', $this->input('email')),
            'donation_type' => $this->input('donation_type', $this->input('type')),
            'currency' => strtoupper((string) $this->input('currency')),
        ]);
    }

    public function rules(): array
    {
        $supportedCurrencies = array_keys(config('donations.rates_to_usd', []));

        return [
            'donor_name' => ['required', 'string', 'max:255'],
            'donor_email' => ['required', 'email', 'max:255'],
            'amount' => ['required', 'numeric', 'min:1'],
            'currency' => ['required', 'string', 'size:3', Rule::in($supportedCurrencies)],
            'donation_type' => ['required', 'in:one_time,monthly'],
            'create_account' => ['nullable', 'boolean'],
        ];
    }
}
