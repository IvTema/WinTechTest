<?php

namespace App\Http\Requests\Balance;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            'id' => 'required|integer|min:1',
            'amount' => 'required|numeric|min:1',
            'transaction' => 'required|string|in:debit,credit',
            'currency' => 'required|string|in:usd,rub',
            'issue' => 'required|string|in:refund,stock,renunciation',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string>
     */
    public function all($keys = null)
    {
        $data = parent::all($keys);
        
        // Include query parameters in validation data
        $data['id'] = $this->input('id', $this->route('balance')); // assuming the parameter name in the route is 'balance'
        
        return $data;
    }
}
