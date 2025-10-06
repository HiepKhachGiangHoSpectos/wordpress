<?php

namespace CTKM\Enums;

class PromotionStatuses extends BaseEnum
{
    public const COMING_SOON = 'coming_soon';
    public const IN_PROGRESS = 'in_progress';
    public const EXPIRE_SOON = 'expire_soon';
    public const EXPIRED = 'expired';
    public const STOP_EARLY = 'stop_early';
    public const DEACTIVE = 'deactive';
    public const DRAFT = 'draft';

    public const MAP = [
        self::COMING_SOON => 'Sắp diễn ra',
        self::IN_PROGRESS => 'Đang diễn ra',
        self::EXPIRE_SOON => 'Sắp hết hạn',
        self::EXPIRED => 'Đã hết hạn',
        self::STOP_EARLY => 'Dừng trước hạn',
        self::DEACTIVE => 'Deactive',
        self::DRAFT => 'Draft',
    ];

    public const OPTIONS = [
        self::COMING_SOON => 'Sắp diễn ra',
        self::IN_PROGRESS => 'Đang diễn ra',
        self::EXPIRE_SOON => 'Sắp hết hạn',
        self::EXPIRED => 'Đã hết hạn',
        self::STOP_EARLY => 'Dừng trước hạn',
        self::DEACTIVE => 'Deactive',
    ];
}