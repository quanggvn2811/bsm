<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    const STATUS_WAITING = 1; // Chưa đủ hàng
    const STATUS_PENDING = 2; // Chờ đóng hàng
    const STATUS_TODAY_HANDLE = 3; // Đã đóng hàng, đã xử lý hôm nay, chờ vận đơn.
    const STATUS_PROCESS = 4; // Đang xử lý vận chuyển

    const STATUS_TAKE_CARE = 5; // Đơn hàng cần lưu ý

    const STATUS_SHIPPED = 6; // Đã giao chưa ck

    const STATUS_FAILED = 7; // Failed

    const STATUS_COMPLETED = 8; // Đã ck

    const ORDER_STATUS = [
        self::STATUS_WAITING => 'Waiting',
        self::STATUS_PENDING => 'Pending',
        self::STATUS_TODAY_HANDLE => 'Today Handle',
        self::STATUS_PROCESS => 'Processing',
        self::STATUS_TAKE_CARE => 'Take Care',
        self::STATUS_SHIPPED => 'Shipped',
        self::STATUS_COMPLETED => 'Completed',
        self::STATUS_FAILED => 'Failed',
    ];

    const PRIORITY_LOW = 1;

    const PRIORITY_NORMAL = 2;

    const PRIORITY_HIGH = 3;

    const ORDER_PRIORITY = [
        self::PRIORITY_LOW => 'Low',
        self::PRIORITY_NORMAL => 'Normal',
        self::PRIORITY_HIGH => 'High',
    ];

    const ORDER_EVIDENCE_FOLDER = 'Evidence_Images';
}
