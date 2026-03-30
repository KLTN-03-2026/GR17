<?php
namespace App\Http\Requests;

class StoreChucVuRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'ten_chuc_vu' => 'required|string|min:3|max:50|unique:chuc_vu,ten_chuc_vu|regex:/^[a-zA-ZÃ€-á»¿\s]+$/',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'ten_chuc_vu.required' => 'TÃªn chá»©c vá»¥ khÃ´ng Ä‘Æ°á»£c Ä‘á»ƒ trá»‘ng',
            'ten_chuc_vu.string' => 'TÃªn chá»©c vá»¥ pháº£i lÃ  chuá»—i kÃ½ tá»±',
            'ten_chuc_vu.min' => 'TÃªn chá»©c vá»¥ pháº£i cÃ³ Ã­t nháº¥t 3 kÃ½ tá»±',
            'ten_chuc_vu.max' => 'TÃªn chá»©c vá»¥ khÃ´ng Ä‘Æ°á»£c vÆ°á»£t quÃ¡ 50 kÃ½ tá»±',
            'ten_chuc_vu.unique' => 'TÃªn chá»©c vá»¥ nÃ y Ä‘Ã£ tá»“n táº¡i',
            'ten_chuc_vu.regex' => 'TÃªn chá»©c vá»¥ chá»‰ Ä‘Æ°á»£c chá»©a chá»¯ cÃ¡i',
        ];
    }
}
