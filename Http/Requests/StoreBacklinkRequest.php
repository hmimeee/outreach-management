<?php

namespace Modules\OutreachManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBacklinkRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'project_id.*' => 'required|exists:projects,id',
            'outreach_site_id' => 'required_with:paid|exists:outreach_sites,id',
            'website' => 'required_without:paid|max:255',
            'backlink.*' => 'required|max:190|active_url|unique:outreach_backlinks,backlink',
            'url.*' => 'required|max:190|active_url',
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
