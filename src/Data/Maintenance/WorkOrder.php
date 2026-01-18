<?php

namespace Samsara\Data\Maintenance;

use Samsara\Data\Entity;

/**
 * WorkOrder entity.
 *
 * Represents a maintenance work order.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $id Work order ID
 * @property-read string|null $assetId Asset ID
 * @property-read string|null $status Work order status
 * @property-read string|null $priority Work order priority (High, Low, Medium, Urgent)
 * @property-read string|null $category Work order category
 * @property-read string|null $description Description of work needed
 * @property-read string|null $closingNotes Closing notes
 * @property-read string|null $invoiceNumber Invoice number
 * @property-read string|null $poNumber Purchase order number
 * @property-read string|null $assignedUserId Assigned mechanic user ID
 * @property-read string|null $createdByUserId Creator user ID
 * @property-read string|null $vendorUuid Vendor UUID
 * @property-read string|null $createdAtTime Creation time (RFC 3339)
 * @property-read string|null $updatedAtTime Last update time (RFC 3339)
 * @property-read string|null $completedAtTime Completion time (RFC 3339)
 * @property-read string|null $dueAtTime Due date (RFC 3339)
 * @property-read string|null $archivedAtTime Archive time (RFC 3339)
 * @property-read int|null $odometerMeters Odometer reading in meters
 * @property-read int|null $engineHours Engine hours
 * @property-read array<int, array{name?: string, quantity?: int}>|null $items Work order items
 * @property-read array<int, array{id?: string, name?: string}>|null $serviceTaskInstances Service task instances
 * @property-read array<int, array{name?: string, url?: string}>|null $attachments Attachments
 * @property-read array{amount?: float}|null $discount Discount information
 * @property-read array{amount?: float}|null $tax Tax information
 */
class WorkOrder extends Entity
{
    /**
     * Check if the work order is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'Cancelled';
    }

    /**
     * Check if the work order is closed.
     */
    public function isClosed(): bool
    {
        return $this->status === 'Closed';
    }

    /**
     * Check if the work order is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'Completed';
    }

    /**
     * Check if the work order is high priority.
     */
    public function isHighPriority(): bool
    {
        return $this->priority === 'High';
    }

    /**
     * Check if the work order is in progress.
     */
    public function isInProgress(): bool
    {
        return $this->status === 'In Progress';
    }

    /**
     * Check if the work order is open.
     */
    public function isOpen(): bool
    {
        return $this->status === 'Open';
    }

    /**
     * Check if the work order is urgent.
     */
    public function isUrgent(): bool
    {
        return $this->priority === 'Urgent';
    }
}
