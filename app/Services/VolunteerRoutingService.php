<?php

namespace App\Services;

use App\Models\VolunteerAssignment;
use App\Models\User;
use App\Models\City;
use App\Models\State;
use App\Models\Country;

class VolunteerRoutingService
{
    /**
     * Find the nearest head volunteer for a given location.
     *
     * @param int|null $cityId
     * @param int|null $stateId
     * @param int|null $countryId
     * @param int|null $excludeUserId - User ID to exclude from assignment (e.g., the creator)
     * @return User|null
     */
    public function findNearestHeadVolunteer(?int $cityId = null, ?int $stateId = null, ?int $countryId = null, ?int $excludeUserId = null): ?User
    {
        // Priority order: City -> State -> Country
        $headVolunteer = null;

        // 1. Try to find head volunteer for the specific city
        if ($cityId) {
            $headVolunteer = $this->getHeadVolunteerForCity($cityId, $excludeUserId);
            if ($headVolunteer) {
                return $headVolunteer;
            }
        }

        // 2. Try to find head volunteer for the state
        if ($stateId) {
            $headVolunteer = $this->getHeadVolunteerForState($stateId, $excludeUserId);
            if ($headVolunteer) {
                return $headVolunteer;
            }
        }

        // 3. Try to find head volunteer for the country
        if ($countryId) {
            $headVolunteer = $this->getHeadVolunteerForCountry($countryId, $excludeUserId);
            if ($headVolunteer) {
                return $headVolunteer;
            }
        }

        // 4. If no head volunteer found, try to find any active volunteer in the hierarchy
        return $this->findAnyVolunteerInHierarchy($cityId, $stateId, $countryId, $excludeUserId);
    }

    /**
     * Get head volunteer for a specific city.
     */
    private function getHeadVolunteerForCity(int $cityId, ?int $excludeUserId = null): ?User
    {
        $query = VolunteerAssignment::where('assignment_type', 'city')
            ->where('city_id', $cityId)
            ->where('role', 'head_volunteer')
            ->where('is_active', true)
            ->with('user');

        if ($excludeUserId) {
            $query->where('user_id', '!=', $excludeUserId);
        }

        $assignment = $query->first();
        return $assignment?->user;
    }

    /**
     * Get head volunteer for a specific state.
     */
    private function getHeadVolunteerForState(int $stateId, ?int $excludeUserId = null): ?User
    {
        $query = VolunteerAssignment::where('assignment_type', 'state')
            ->where('state_id', $stateId)
            ->where('role', 'head_volunteer')
            ->where('is_active', true)
            ->with('user');

        if ($excludeUserId) {
            $query->where('user_id', '!=', $excludeUserId);
        }

        $assignment = $query->first();
        return $assignment?->user;
    }

    /**
     * Get head volunteer for a specific country.
     */
    private function getHeadVolunteerForCountry(int $countryId, ?int $excludeUserId = null): ?User
    {
        $query = VolunteerAssignment::where('assignment_type', 'country')
            ->where('country_id', $countryId)
            ->where('role', 'head_volunteer')
            ->where('is_active', true)
            ->with('user');

        if ($excludeUserId) {
            $query->where('user_id', '!=', $excludeUserId);
        }

        $assignment = $query->first();
        return $assignment?->user;
    }

    /**
     * Find any active volunteer in the hierarchy if no head volunteer is found.
     */
    private function findAnyVolunteerInHierarchy(?int $cityId = null, ?int $stateId = null, ?int $countryId = null, ?int $excludeUserId = null): ?User
    {
        // Try city volunteers first
        if ($cityId) {
            $query = VolunteerAssignment::where('assignment_type', 'city')
                ->where('city_id', $cityId)
                ->where('is_active', true)
                ->with('user');

            if ($excludeUserId) {
                $query->where('user_id', '!=', $excludeUserId);
            }

            $assignment = $query->first();
            if ($assignment) {
                return $assignment->user;
            }
        }

        // Try state volunteers
        if ($stateId) {
            $query = VolunteerAssignment::where('assignment_type', 'state')
                ->where('state_id', $stateId)
                ->where('is_active', true)
                ->with('user');

            if ($excludeUserId) {
                $query->where('user_id', '!=', $excludeUserId);
            }

            $assignment = $query->first();
            if ($assignment) {
                return $assignment->user;
            }
        }

        // Try country volunteers
        if ($countryId) {
            $query = VolunteerAssignment::where('assignment_type', 'country')
                ->where('country_id', $countryId)
                ->where('is_active', true)
                ->with('user');

            if ($excludeUserId) {
                $query->where('user_id', '!=', $excludeUserId);
            }

            $assignment = $query->first();
            if ($assignment) {
                return $assignment->user;
            }
        }

        return null;
    }

    /**
     * Get all volunteers for a specific region.
     *
     * @param string $assignmentType
     * @param int|null $countryId
     * @param int|null $stateId
     * @param int|null $cityId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getVolunteersForRegion(string $assignmentType, ?int $countryId = null, ?int $stateId = null, ?int $cityId = null)
    {
        $query = VolunteerAssignment::where('assignment_type', $assignmentType)
            ->where('is_active', true)
            ->with('user');

        switch ($assignmentType) {
            case 'city':
                $query->where('city_id', $cityId);
                break;
            case 'state':
                $query->where('state_id', $stateId);
                break;
            case 'country':
                $query->where('country_id', $countryId);
                break;
        }

        return $query->get();
    }

    /**
     * Get the region hierarchy for a given location.
     *
     * @param int|null $cityId
     * @param int|null $stateId
     * @param int|null $countryId
     * @return array
     */
    public function getRegionHierarchy(?int $cityId = null, ?int $stateId = null, ?int $countryId = null): array
    {
        $hierarchy = [];

        if ($cityId) {
            $city = City::with(['state.country'])->find($cityId);
            if ($city) {
                $hierarchy = [
                    'city' => $city,
                    'state' => $city->state,
                    'country' => $city->state->country,
                ];
            }
        } elseif ($stateId) {
            $state = State::with('country')->find($stateId);
            if ($state) {
                $hierarchy = [
                    'state' => $state,
                    'country' => $state->country,
                ];
            }
        } elseif ($countryId) {
            $country = Country::find($countryId);
            if ($country) {
                $hierarchy = [
                    'country' => $country,
                ];
            }
        }

        return $hierarchy;
    }

    /**
     * Check if a user is assigned to a specific region.
     *
     * @param int $userId
     * @param string $assignmentType
     * @param int|null $countryId
     * @param int|null $stateId
     * @param int|null $cityId
     * @return bool
     */
    public function isUserAssignedToRegion(int $userId, string $assignmentType, ?int $countryId = null, ?int $stateId = null, ?int $cityId = null): bool
    {
        $query = VolunteerAssignment::where('user_id', $userId)
            ->where('assignment_type', $assignmentType)
            ->where('is_active', true);

        switch ($assignmentType) {
            case 'city':
                $query->where('city_id', $cityId);
                break;
            case 'state':
                $query->where('state_id', $stateId);
                break;
            case 'country':
                $query->where('country_id', $countryId);
                break;
        }

        return $query->exists();
    }
}
