<?php

namespace App\Http\Requests;


class CreateUserRequest extends APIRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|unique:users|max:255',
            'email' => 'email:rfc,dns',
            'password' => 'required|string|max:255|min:8',
            'password_confirmation' => 'required|string|max:255|min:8|same:password',
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'Name can not empty.',
            'name.unique' => 'Name must be a unique.',
            'email.required'  => 'Email can not empty.',
        ];
    }
}
