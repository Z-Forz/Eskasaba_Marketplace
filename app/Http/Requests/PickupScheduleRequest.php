<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PickupScheduleRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [

            'order_id' => [
                'required',
                'exists:orders,id',
            ],

            'pickup_date' => [
                'required',
                'date',
            ],

            'pickup_time' => [
                'required',
                'date_format:H:i',
            ],

            'is_picked_up' => [
                'nullable',
                'boolean',
            ],

        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [

            'order_id.required'      => 'Pesanan wajib dipilih.',
            'order_id.exists'        => 'Pesanan tidak ditemukan.',

            'pickup_date.required'   => 'Tanggal pengambilan wajib diisi.',
            'pickup_date.date'       => 'Format tanggal tidak valid.',

            'pickup_time.required'   => 'Jam pengambilan wajib diisi.',
            'pickup_time.date_format'=> 'Format jam harus HH:MM.',

            'is_picked_up.boolean'   => 'Status pengambilan tidak valid.',

        ];
    }
}