<?php

// namespace App\Rules;

// use Closure;
// use Illuminate\Contracts\Validation\ValidationRule;

// class CnpjValidation implements ValidationRule
// {
//     public function validate(string $attribute, mixed $value, Closure $fail): void
//     {
//         // Remove formatação
//         $cnpj = preg_replace('/[^0-9]/', '', $value);

//         if (strlen($cnpj) !== 14) {
//             $fail('O CNPJ deve conter 14 dígitos.');
//             return;
//         }

//         if (preg_match('/^(\d)\1{13}$/', $cnpj)) {
//             $fail('CNPJ com dígitos repetidos é inválido.');
//             return;
//         }

//         // Cálculo do primeiro dígito verificador
//         $soma = 0;
//         $peso = 5;
//         for ($i = 0; $i < 12; $i++) {
//             $soma += $cnpj[$i] * $peso;
//             $peso = ($peso == 2) ? 9 : $peso - 1;
//         }
//         $digito1 = ($soma % 11) < 2 ? 0 : 11 - ($soma % 11);

//         // Cálculo do segundo dígito verificador
//         $soma = 0;
//         $peso = 6;
//         for ($i = 0; $i < 13; $i++) {
//             $soma += $cnpj[$i] * $peso;
//             $peso = ($peso == 2) ? 9 : $peso - 1;
//         }
//         $digito2 = ($soma % 11) < 2 ? 0 : 11 - ($soma % 11);

//         if ($cnpj[12] != $digito1 || $cnpj[13] != $digito2) {
//             $fail('CNPJ inválido (dígitos verificadores incorretos).');
//         }
//     }
// }
