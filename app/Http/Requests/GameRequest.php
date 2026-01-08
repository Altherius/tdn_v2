<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GameRequest extends FormRequest
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
            'tournament_id' => ['nullable', 'exists:tournaments,id'],
            'team1_id' => ['required', 'exists:teams,id', 'different:team2_id'],
            'team2_id' => ['required', 'exists:teams,id', 'different:team1_id'],
            'leg1_team1_score' => ['required', 'integer', 'min:0'],
            'leg1_team2_score' => ['required', 'integer', 'min:0'],
            'leg2_team1_score' => ['required', 'integer', 'min:0'],
            'leg2_team2_score' => ['required', 'integer', 'min:0'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'team1_id.different' => 'The hosting team and receiving team must be different.',
            'team2_id.different' => 'The hosting team and receiving team must be different.',
        ];
    }
}
