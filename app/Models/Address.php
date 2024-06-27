<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class Address
 * @property int $id
 * @property int $user_id
 * @property string $street
 * @property string $number
 * @property string $city
 * @property string $postal_code
 * @property string $country
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 */

class Address extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'street',
        'number',
        'city',
        'postal_code',
        'country',
    ];

    public function user()
    {
        return $this->hasOne(User::class);
    }
}
