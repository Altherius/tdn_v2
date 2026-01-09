<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TournamentRequest extends FormRequest
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
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'is_major' => ['boolean'],
            'is_balancing' => ['boolean'],
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['winner_team_id'] = ['nullable', 'exists:teams,id'];
            $rules['second_place_team_id'] = ['nullable', 'exists:teams,id'];
            $rules['third_place_team_id'] = ['nullable', 'exists:teams,id'];
            $rules['is_over'] = ['boolean'];
        }

        return $rules;
    }
}
