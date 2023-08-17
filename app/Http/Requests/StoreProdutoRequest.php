<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProdutoRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            "nome" =>  'min:2|unique:produtos,nome|required',
            "descricao" => 'min:2|required',
            "preco" => 'numeric|required',
            "estoque" => 'numeric|required',
            "tipo_id" => 'required|exists:tipos,id',
        ];
    }
}
