<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EntidadesRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    // public function rules()
    // {
    //     return [
    //         'nome' => 'required|string|max:100',
    //         'cnpj' => 'required|string|size:14|unique:entidades,cnpj',
    //     ];
    // }
    public function rules()
    {
        $entidadeId = $this->route('entidade') ? $this->route('entidade')->id : null;

        return [
            'nome' => 'required|string|max:100',
            'cnpj' => [
                'required',
                'string',
                Rule::unique('entidades')->ignore($entidadeId),
            ],
        ];
    }

    public function messages()
    {
        return [
            'nome.required' => 'O nome da entidade é obrigatório',
            'cnpj.required' => 'O CNPJ é obrigatório',
            'cnpj.unique' => 'Este CNPJ já está cadastrado',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->cnpj) {
            $this->merge([
                'cnpj' => preg_replace('/\D/', '', $this->cnpj) // Remove formatação antes da validação
            ]);
        }
    }
}
