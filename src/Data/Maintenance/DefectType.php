<?php

namespace Samsara\Data\Maintenance;

use Samsara\Data\Entity;

/**
 * DefectType entity.
 *
 * Represents a DVIR defect type.
 *
 * @author Erik Galloway <erik@erikgall.com>
 *
 * @property-read string|null $id Defect type ID
 * @property-read string|null $label Defect type label
 * @property-read string|null $sectionType Inspection section type
 * @property-read string|null $severity Defect severity (major, minor, notApplicable)
 * @property-read string|null $createdAtTime Creation time (RFC 3339)
 */
class DefectType extends Entity {}
