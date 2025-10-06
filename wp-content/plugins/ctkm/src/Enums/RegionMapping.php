<?php
namespace CTKM\Enums;

class RegionMapping extends BaseEnum
{
    public const HA_NOI = 'Ha Noi';
    public const HO_CHI_MINH = 'Ho Chi Minh';
    public const VINH = 'Vinh';

    public const MAP = [
        self::HA_NOI => 'Miền Bắc',
        self::HO_CHI_MINH => 'Miền Nam',
        self::VINH => 'Miền Trung',
    ];
}
