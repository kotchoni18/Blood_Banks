<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('id') ?? $this->route('user') ?? null;

        // Mot de passe obligatoire en POST (création), optionnel en PUT/PATCH (mise à jour)
        $passwordRule = $this->isMethod('post')
            ? 'required|min:8'
            : 'nullable|min:8';

        return [
            // Informations communes
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $userId,
            'phone'      => 'nullable|string|max:20',
            'password'   => $passwordRule,
            'role'       => 'required|in:admin,agent,donor',
            'is_active'  => 'boolean',

            // Informations générales
            'birth_date' => 'nullable|date',
            'gender'     => 'nullable|in:M,F,O',
            'address'    => 'nullable|string',
            'city'       => 'nullable|string|max:100',

            // DONNEUR
            'blood_group'         => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'last_donation_date'  => 'nullable|date',
            'donation_count'      => 'nullable|integer|min:0',

            // AGENT
            'department'       => 'nullable|string|max:100',
            // unique mais on ignore l'utilisateur actuel si update
            'employee_number'  => 'nullable|string|max:50|unique:users,employee_number,' . $userId,
            'hire_date'        => 'nullable|date',

            // ADMIN
            'super_admin' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'Le prénom est obligatoire.',
            'last_name.required'  => 'Le nom est obligatoire.',
            'email.required'      => "L'email est obligatoire.",
            'email.email'         => "Le format de l'email est invalide.",
            'email.unique'        => "Cet email est déjà utilisé.",
            'password.required'   => 'Le mot de passe est obligatoire.',
            'password.min'        => 'Le mot de passe doit contenir au moins 8 caractères.',
            'role.required'       => 'Le rôle est obligatoire.',
            'role.in'             => 'Le rôle doit être admin, agent ou donor.',
            'gender.in'           => 'Le genre doit être M, F ou O.',
            'blood_group.in'      => 'Le groupe sanguin est invalide.',
            'employee_number.unique' => "Ce numéro d'employé est déjà utilisé.",
        ];
    }
}
