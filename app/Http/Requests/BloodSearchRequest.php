<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BloodSearchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'admin_id' => 'required|exists:admins,id',
            'blood_group' => 'required|in:A+,A-,B+,B-,O+,O-,AB+,AB-',
            'blood_quantity' => 'required|integer|min:1|max:5',
            'request_type' => [
                'required',
                'in:Emergency,Normal,Rare',
                function ($attribute, $value, $fail) use ($data) {
                    $rareBloodTypes = ['AB-', 'B-', 'A-'];
                    if ($value === 'Rare' && !in_array($data['blood_group'], $rareBloodTypes)) {
                        $fail('Rare request type can only be selected for rare blood types (AB-, B-, A-)');
                    }
                }
            ],
            'request_form' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'payment' => 'required|numeric|min:0',
        ];
    }
    public function validatedData()
    {
        return [
            'admin_id' => $this->input('admin_id'),
        ];
    }
}
