<?php

namespace App\Enums\models;

enum OrderStatus: string
{
  case PENDING = 'pending';
  case PROCESSING = 'processing';
  case COMPLETED = 'completed';
  case CANCELLED = 'cancelled';
  case REFUNDED = 'refunded';
  case FAILED = 'failed';

  public static function getRandomValue(): OrderStatus
  {
    $statuses = [
      self::PENDING,
      self::PROCESSING,
      self::COMPLETED,
      self::CANCELLED,
      self::REFUNDED,
      self::FAILED,
    ];

    return $statuses[array_rand($statuses)];
  }
};
