<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UserService
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    /**
     * Create a new user with business logic validation.
     */
    public function createUser(array $data): User
    {
        // Check if email already exists
        if ($this->userRepository->emailExists($data['email'])) {
            throw ValidationException::withMessages([
                'email' => ['The email address is already registered.']
            ]);
        }

        // Hash password if provided
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        // Set default role if not provided
        if (!isset($data['role'])) {
            $data['role'] = User::ROLE_CANDIDATE;
        }

        // Validate role
        $this->validateRole($data['role']);

        DB::beginTransaction();
        try {
            $user = $this->userRepository->create($data);

            $this->sendWelcomeEmail($user);

            \Log::info('User created', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role
            ]);

            DB::commit();
            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update user with business logic validation.
     */
    public function updateUser(User $user, array $data): User
    {
        // Check if email is being changed and if it already exists
        if (isset($data['email']) && $data['email'] !== $user->email) {
            if ($this->userRepository->emailExists($data['email'])) {
                throw ValidationException::withMessages([
                    'email' => ['The email address is already taken.']
                ]);
            }
        }

        // Hash password if being updated
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        // Validate role if being updated
        if (isset($data['role'])) {
            $this->validateRole($data['role']);
            
            // Log role change if different
            if ($data['role'] !== $user->role) {
                \Log::info('User role changed', [
                    'user_id' => $user->id,
                    'old_role' => $user->role,
                    'new_role' => $data['role']
                ]);
            }
        }

        $this->userRepository->update($user, $data);
        return $user->fresh();
    }

    /**
     * Get user by ID with error handling.
     */
    public function getUserById(int $id): User
    {
        return $this->userRepository->findByIdOrFail($id);
    }

    /**
     * Get user by email.
     */
    public function getUserByEmail(string $email): ?User
    {
        return $this->userRepository->findByEmail($email);
    }

    /**
     * Get all users with pagination.
     */
    public function getAllUsers(int $perPage = 15): LengthAwarePaginator
    {
        return $this->userRepository->getPaginated($perPage);
    }

    /**
     * Get users by role with pagination.
     */
    public function getUsersByRole(string $role, int $perPage = 15): LengthAwarePaginator
    {
        $this->validateRole($role);
        return $this->userRepository->getPaginatedByRole($role, $perPage);
    }

    /**
     * Search users with business logic.
     */
    public function searchUsers(string $query, int $perPage = 15): LengthAwarePaginator
    {
        // Sanitize search query
        $query = trim($query);
        
        if (strlen($query) < 2) {
            throw ValidationException::withMessages([
                'query' => ['Search query must be at least 2 characters long.']
            ]);
        }

        return $this->userRepository->searchPaginated($query, $perPage);
    }

    /**
     * Change user role with validation.
     */
    public function changeUserRole(User $user, string $newRole): User
    {
        $this->validateRole($newRole);
        
        $oldRole = $user->role;
        
        if ($oldRole === $newRole) {
            throw ValidationException::withMessages([
                'role' => ['User already has this role.']
            ]);
        }

        $this->userRepository->assignRole($user, $newRole);
        
        // Send role change notification
        $this->sendRoleChangeNotification($user, $oldRole, $newRole);
        
        return $user->fresh();
    }

    /**
     * Soft delete user with business logic.
     */
    public function deleteUser(User $user): bool
    {
        // Prevent deletion of the last admin
        if ($user->isAdmin() && $this->userRepository->countByRole(User::ROLE_ADMIN) <= 1) {
            throw ValidationException::withMessages([
                'user' => ['Cannot delete the last admin user.']
            ]);
        }

        $result = $this->userRepository->delete($user);

        if ($result) {
            \Log::info('User soft deleted', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);
        }

        return $result;
    }

    /**
     * Restore soft deleted user.
     */
    public function restoreUser(User $user): bool
    {
        $result = $this->userRepository->restore($user);

        if ($result) {
            \Log::info('User restored', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);
        }

        return $result;
    }

    /**
     * Get user statistics.
     */
    public function getUserStatistics(): array
    {
        $stats = $this->userRepository->getRoleCounts();
        
        // Add additional statistics
        $stats['verified'] = $this->userRepository->getVerifiedUsers()->count();
        $stats['unverified'] = $stats['total'] - $stats['verified'];
        $stats['recent'] = $this->userRepository->getRecentUsers(30)->count();
        $stats['trashed'] = $this->userRepository->getOnlyTrashed()->count();

        return $stats;
    }

    /**
     * Get recent users for dashboard.
     */
    public function getRecentUsers(int $days = 7): Collection
    {
        return $this->userRepository->getRecentUsers($days);
    }

    /**
     * Bulk operations for users.
     */
    public function bulkUpdateUsers(array $userIds, array $data): int
    {
        // Validate role if being updated
        if (isset($data['role'])) {
            $this->validateRole($data['role']);
        }

        // Hash password if being updated
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return $this->userRepository->bulkUpdate($userIds, $data);
    }

    /**
     * Bulk delete users with validation.
     */
    public function bulkDeleteUsers(array $userIds): int
    {
        // Check if trying to delete all admins
        $adminIds = $this->userRepository->getAdmins()->pluck('id')->toArray();
        $adminIdsToDelete = array_intersect($userIds, $adminIds);
        
        if (count($adminIdsToDelete) >= count($adminIds)) {
            throw ValidationException::withMessages([
                'users' => ['Cannot delete all admin users.']
            ]);
        }

        return $this->userRepository->bulkDelete($userIds);
    }

    /**
     * Get employers for hiring functionality.
     */
    public function getActiveEmployers(): Collection
    {
        return $this->userRepository->getEmployers()
            ->where('email_verified_at', '!=', null);
    }

    /**
     * Get candidates for hiring functionality.
     */
    public function getActiveCandidates(): Collection
    {
        return $this->userRepository->getCandidates()
            ->where('email_verified_at', '!=', null);
    }

    /**
     * Validate user role.
     */
    private function validateRole(string $role): void
    {
        $validRoles = [User::ROLE_ADMIN, User::ROLE_EMPLOYER, User::ROLE_CANDIDATE];
        
        if (!in_array($role, $validRoles)) {
            throw ValidationException::withMessages([
                'role' => ['Invalid role specified.']
            ]);
        }
    }

    /**
     * Send welcome email based on user role.
     */
    private function sendWelcomeEmail(User $user): void
    {
        try {
            switch ($user->role) {
                case User::ROLE_ADMIN:
                    // Mail::to($user)->send(new AdminWelcomeEmail($user));
                    break;
                case User::ROLE_EMPLOYER:
                    // Mail::to($user)->send(new EmployerWelcomeEmail($user));
                    break;
                case User::ROLE_CANDIDATE:
                    // Mail::to($user)->send(new CandidateWelcomeEmail($user));
                    break;
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send welcome email', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send role change notification.
     */
    private function sendRoleChangeNotification(User $user, string $oldRole, string $newRole): void
    {
        try {
            // Mail::to($user)->send(new RoleChangeNotification($user, $oldRole, $newRole));
        } catch (\Exception $e) {
            \Log::error('Failed to send role change notification', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }
} 