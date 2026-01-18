<?php

namespace ErikGall\Samsara\Tests\Unit\Enums;

use ErikGall\Samsara\Tests\TestCase;
use ErikGall\Samsara\Enums\AlertType;
use ErikGall\Samsara\Enums\AssetType;
use ErikGall\Samsara\Enums\HosLogType;
use ErikGall\Samsara\Enums\RouteState;
use PHPUnit\Framework\Attributes\Test;
use ErikGall\Samsara\Enums\DocumentType;
use ErikGall\Samsara\Enums\SafetyEventType;
use ErikGall\Samsara\Enums\MaintenanceStatus;

/**
 * Unit tests for remaining enums.
 *
 * @author Erik Galloway <erik@erikgall.com>
 */
class RemainingEnumsTest extends TestCase
{
    #[Test]
    public function alert_type_has_geofence(): void
    {
        $this->assertSame('geofence', AlertType::GEOFENCE->value);
    }

    #[Test]
    public function alert_type_has_speeding(): void
    {
        $this->assertSame('speeding', AlertType::SPEEDING->value);
    }

    #[Test]
    public function asset_type_has_equipment(): void
    {
        $this->assertSame('equipment', AssetType::EQUIPMENT->value);
    }

    #[Test]
    public function asset_type_has_trailer(): void
    {
        $this->assertSame('trailer', AssetType::TRAILER->value);
    }

    #[Test]
    public function document_type_has_bill_of_lading(): void
    {
        $this->assertSame('billOfLading', DocumentType::BILL_OF_LADING->value);
    }

    #[Test]
    public function document_type_has_proof_of_delivery(): void
    {
        $this->assertSame('proofOfDelivery', DocumentType::PROOF_OF_DELIVERY->value);
    }

    #[Test]
    public function hos_log_type_has_certification(): void
    {
        $this->assertSame('certification', HosLogType::CERTIFICATION->value);
    }

    #[Test]
    public function hos_log_type_has_duty_status(): void
    {
        $this->assertSame('dutyStatus', HosLogType::DUTY_STATUS->value);
    }

    #[Test]
    public function maintenance_status_has_completed(): void
    {
        $this->assertSame('completed', MaintenanceStatus::COMPLETED->value);
    }

    #[Test]
    public function maintenance_status_has_open(): void
    {
        $this->assertSame('open', MaintenanceStatus::OPEN->value);
    }

    #[Test]
    public function route_state_has_in_progress(): void
    {
        $this->assertSame('inProgress', RouteState::IN_PROGRESS->value);
    }

    #[Test]
    public function route_state_has_not_started(): void
    {
        $this->assertSame('notStarted', RouteState::NOT_STARTED->value);
    }

    #[Test]
    public function safety_event_type_has_crash(): void
    {
        $this->assertSame('crash', SafetyEventType::CRASH->value);
    }

    #[Test]
    public function safety_event_type_has_harsh_acceleration(): void
    {
        $this->assertSame('harshAcceleration', SafetyEventType::HARSH_ACCELERATION->value);
    }
}
