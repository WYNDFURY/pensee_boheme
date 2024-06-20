<?php

namespace App\Models;

use App\Enums\models\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class Order
 * @property int $id
 * @property int $user_id
 * @property float $total
 * @property OrderStatus $status
 * @property string $address
 * @property string $payment_method
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 */


class Order extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $casts = [
        'status' => OrderStatus::class,
    ];

    protected $fillable = [
        'user_id',
        'total',
        'status',
        'address',
        'payment_method',
    ];
}
