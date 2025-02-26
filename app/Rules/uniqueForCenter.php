<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class uniqueForCenter implements Rule {
    /**
     * @var array|mixed
     */
    private mixed $column;
    private mixed $table;
    /**
     * @var mixed|null
     */
    private mixed $ignoreID;
    /**
     * @var mixed|null
     */
    private mixed $centerID;

    /**
     * Create a new rule instance.
     *
     * @return void
     */

    public function __construct($table, $column = null, $ignoreID = null, $centerID = null) {
        $this->table = $table;
        $this->column = $column;
        $this->ignoreID = $ignoreID;
        $this->centerID = $centerID;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value) {
        DB::enableQueryLog();
        $columns = $this->column ?? $attribute;

        $query = DB::table($this->table);

        if (!is_array($columns)) {
            $query = $query->where($columns, $value);
        } else {
            $query = $query->where($columns);
        }

        if (!empty($this->ignoreID)) {
            $query = $query->whereNot('id', $this->ignoreID);
        }

        // Check for School ID
        if (!empty($this->centerID)) {
            $query = $query->where('center_id', $this->centerID);
        } else {
            $query = $query->where('center_id', Auth::user()->center->id);
        }
        return !$query->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message() {
        return 'The :attribute must be Unique.';
    }
}
