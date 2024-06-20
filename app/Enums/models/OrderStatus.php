<?php 

namespace App\Enums\models;

enum OrderStatus : string {
  case PENDING = 'pending';
  case PROCESSING = 'processing';
  case COMPLETED = 'completed';
  case CANCELLED = 'cancelled';
  case REFUNDED = 'refunded';
  case FAILED = 'failed';
}