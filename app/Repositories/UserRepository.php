<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository
{
    /**
     * Create a new user.
     */
    public function create(array $data): User
    {
        return User::create($data);
    }

    /**
     * Find a user by ID.
     */
    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    /**
     * Find a user by ID or fail.
     */
    public function findByIdOrFail(int $id): User
    {
        return User::findOrFail($id);
    }

    /**
     * Find a user by email.
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Get all users.
     */
    public function getAll(): Collection
    {
        return User::all();
    }

    /**
     * Get paginated users.
     */
    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return User::paginate($perPage);
    }

    /**
     * Update a user.
     */
    public function update(User $user, array $data): bool
    {
        return $user->update($data);
    }

    /**
     * Delete a user (soft delete).
     */
    public function delete(User $user): bool
    {
        return $user->delete();
    }

    /**
     * Force delete a user (permanent).
     */
    public function forceDelete(User $user): bool
    {
        return $user->forceDelete();
    }

    /**
     * Restore a soft deleted user.
     */
    public function restore(User $user): bool
    {
        return $user->restore();
    }

    /**
     * Get users by role.
     */
    public function getByRole(string $role): Collection
    {
        return User::where('role', $role)->get();
    }

    /**
     * Get all admin users.
     */
    public function getAdmins(): Collection
    {
        return $this->getByRole(User::ROLE_ADMIN);
    }

    /**
     * Get all employer users.
     */
    public function getEmployers(): Collection
    {
        return $this->getByRole(User::ROLE_EMPLOYER);
    }

    /**
     * Get all candidate users.
     */
    public function getCandidates(): Collection
    {
        return $this->getByRole(User::ROLE_CANDIDATE);
    }

    /**
     * Get paginated users by role.
     */
    public function getPaginatedByRole(string $role, int $perPage = 15): LengthAwarePaginator
    {
        return User::where('role', $role)->paginate($perPage);
    }

    /**
     * Get users with trashed (soft deleted).
     */
    public function getAllWithTrashed(): Collection
    {
        return User::withTrashed()->get();
    }

    /**
     * Get only trashed (soft deleted) users.
     */
    public function getOnlyTrashed(): Collection
    {
        return User::onlyTrashed()->get();
    }

    /**
     * Search users by name or email.
     */
    public function search(string $query): Collection
    {
        return User::where('name', 'LIKE', "%{$query}%")
            ->orWhere('email', 'LIKE', "%{$query}%")
            ->get();
    }

    /**
     * Search users by name or email with pagination.
     */
    public function searchPaginated(string $query, int $perPage = 15): LengthAwarePaginator
    {
        return User::where('name', 'LIKE', "%{$query}%")
            ->orWhere('email', 'LIKE', "%{$query}%")
            ->paginate($perPage);
    }

    /**
     * Count users by role.
     */
    public function countByRole(string $role): int
    {
        return User::where('role', $role)->count();
    }

    /**
     * Get user counts for all roles.
     */
    public function getRoleCounts(): array
    {
        return [
            'admins' => $this->countByRole(User::ROLE_ADMIN),
            'employers' => $this->countByRole(User::ROLE_EMPLOYER),
            'candidates' => $this->countByRole(User::ROLE_CANDIDATE),
            'total' => User::count(),
        ];
    }

    /**
     * Check if email exists.
     */
    public function emailExists(string $email): bool
    {
        return User::where('email', $email)->exists();
    }

    /**
     * Get recently registered users.
     */
    public function getRecentUsers(int $days = 7): Collection
    {
        return User::where('created_at', '>=', now()->subDays($days))
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get verified users.
     */
    public function getVerifiedUsers(): Collection
    {
        return User::whereNotNull('email_verified_at')->get();
    }

    /**
     * Get unverified users.
     */
    public function getUnverifiedUsers(): Collection
    {
        return User::whereNull('email_verified_at')->get();
    }

    /**
     * Assign role to user.
     */
    public function assignRole(User $user, string $role): bool
    {
        return $this->update($user, ['role' => $role]);
    }

    /**
     * Bulk update users.
     */
    public function bulkUpdate(array $userIds, array $data): int
    {
        return User::whereIn('id', $userIds)->update($data);
    }

    /**
     * Bulk delete users (soft delete).
     */
    public function bulkDelete(array $userIds): int
    {
        return User::whereIn('id', $userIds)->delete();
    }

    public function count(): int
    {
        return User::count();
    }
}
