<?php
namespace CTKM\Enums;

class PromotionType extends BaseEnum
{
    public const MKT_MIEN_NAM = 'mkt_mien_nam';
    public const MKT_MIEN_BAC = 'mkt_mien_bac';
    public const GOLDEN_SPOONS = 'golden_spoons';
    public const DOI_TAC = 'doi_tac';

    public const MAP = [
        self::MKT_MIEN_NAM => 'Marketing Miền Nam',
        self::MKT_MIEN_BAC => 'Marketing Miền Bắc',
        self::GOLDEN_SPOONS => 'Golden SpoonS',
        self::DOI_TAC => 'Đối tác',
    ];
}
