<?php

namespace Modules\OutreachManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'outreach_site_id' => 'required|exists:outreach_sites,id',
            'payment_method' => 'required|string|max:190',
            'payment_details' => 'required',
            'reviewer_id' => 'required|exists:users,id',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
