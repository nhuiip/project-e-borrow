<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class IsImageRequest implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        dd(json_decode($value));
        die;
        if (is_array($value)) {
            $checkType = array();
            $allowType = array("image/jpeg", "image/JPEG", "image/jpg", "image/JPG", "image/png", "image/PNG");
            foreach ($value as $key => $item) {
                if ($item != null) {
                    $data = json_decode($item, true);
                    if ($data["mimeType"] == "image/jpeg") {
                        array_push($checkType, 1);
                    } else {
                        array_push($checkType, 0);
                    }
                }
            }
            if (count($checkType) != 0) {
                if (!in_array(0, $checkType)) {
                    return true;
                }
            }
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'รูปแบบไฟล์ภาพผิดพลาดกรุณาอัพโหลดเฉพาะไฟล์รูปภาพเท่านั้น';
    }
}
