<?php

namespace Modules\OutreachManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBacklinkRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'project_id' => 'nullable|exists:projects,id',
            'outreach_site_id' => 'nullable|exists:outreach_sites,id',
            'website' => 'nullable|max:255',
            'backlink' => 'nullable|max:190|active_url|unique:outreach_backlinks,backlink,'.$this->id,
            'url' => 'nullable|max:190|active_url',
            'published_date' => 'nullable|date|date_format:Y-m-d',
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
