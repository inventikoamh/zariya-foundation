<?php

namespace App\Services;

use App\Models\Status;

class StatusHelper
{
    /**
     * Get status options for a specific type
     */
    public static function getStatusOptions(string $type): array
    {
        return Status::getStatusOptions($type);
    }

    /**
     * Get status by name and type
     */
    public static function getStatus(string $name, string $type): ?Status
    {
        return Status::where('name', $name)
            ->where('type', $type)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Get all statuses for a type
     */
    public static function getStatuses(string $type)
    {
        return Status::active()->byType($type)->ordered()->get();
    }

    /**
     * Check if a status exists for a type
     */
    public static function statusExists(string $name, string $type): bool
    {
        return Status::where('name', $name)
            ->where('type', $type)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Get status badge class
     */
    public static function getStatusBadgeClass(string $statusName, string $type): string
    {
        $status = self::getStatus($statusName, $type);
        if ($status && $status->badge_class) {
            return $status->badge_class;
        }

        // Fallback to default badge class
        return 'bg-gray-100 text-gray-800';
    }

    /**
     * Get status display name
     */
    public static function getStatusDisplayName(string $statusName, string $type): string
    {
        $status = self::getStatus($statusName, $type);
        if ($status && $status->display_name) {
            return $status->display_name;
        }

        // Fallback to a safe string conversion
        if (is_array($statusName)) {
            return 'Unknown Status';
        }

        return ucfirst(str_replace('_', ' ', $statusName));
    }

    /**
     * Get status color
     */
    public static function getStatusColor(string $statusName, string $type): string
    {
        $status = self::getStatus($statusName, $type);
        return $status ? $status->color : '#6B7280';
    }
}
