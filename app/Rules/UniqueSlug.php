<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class UniqueSlug implements Rule
{
    protected $repository;
    protected $id;
    protected $msg;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($repository, $id=null)
    {
        $this->repository = $repository;
        $this->id = $id;
        $this->msg = [];
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
        if(is_array($value))
        {

            $validate = true;

            foreach ($value as $slug) {
                $slug = Str::slug($slug);
                if(!$this->repository->isUniqueSlug(Str::slug($slug)))
                {
                    $validate = false; 
                    array_push($this->msg, $slug);
                }
            }

            return $validate;
        }
        else
        {
            $slug = Str::slug($value);
            if($this->id)
            {
                $object = $this->repository->getById($this->id);
                if($object->slug == $slug)
                {
                    return true;
                }
            }
            array_push($this->msg, $slug);
            return $this->repository->isUniqueSlug(Str::slug($slug));
        }

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return  implode(", ", $this->msg) . ' already exists, please choose another value.';
    }
}
