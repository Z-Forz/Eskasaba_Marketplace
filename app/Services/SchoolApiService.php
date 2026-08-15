<?php

namespace App\Services;

/**
 * Dummy SchoolApiService used as a placeholder until the real
 * implementation is available. Keeps the container resolution
 * from failing during local development.
 */
class SchoolApiService
{
    /**
     * Create a new dummy service instance.
     */
    public function __construct()
    {
        // placeholder
    }

    /**
     * Return a list of school profiles (empty placeholder).
     *
     * @return array<int, array<string,mixed>>
     */
    public function fetchProfiles(): array
    {
        return [];
    }

    /**
     * Return a single profile by id or null if not found.
     *
     * @param  int  $id
     * @return array<string,mixed>|null
     */
    public function getProfile(int $id): ?array
    {
        return null;
    }
    /**
     * Validate user credentials against the school system.
     *
     * This is currently a placeholder implementation that always returns true.
     * Replace with real API call when the school integration is ready.
     */
    public function validate(string $username, string $password): bool
    {
        // TODO: Integrate with actual school API.
        return true;
    }
}

