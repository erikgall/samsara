<?php

namespace ErikGall\Samsara\Enums;

/**
 * Document type enum.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
enum DocumentType: string
{
    case BILL_OF_LADING = 'billOfLading';
    case DISPATCH = 'dispatch';
    case INSPECTION = 'inspection';
    case OTHER = 'other';
    case PHOTO = 'photo';
    case PROOF_OF_DELIVERY = 'proofOfDelivery';
    case RECEIPT = 'receipt';
    case SCAN = 'scan';
    case SIGNATURE = 'signature';
}
