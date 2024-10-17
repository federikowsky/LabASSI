<?php

namespace App\Services;

use App\Models\User;
use App\Services\AuthService;

class UserService
{
    protected $userModel;
    protected $authService;

    public function __construct(User $userModel, AuthService $authService)
    {
        $this->userModel = $userModel;
        $this->authService = $authService;
    }

    public function get_users(): array
    {
        return $this->userModel->all();
    }

    public function user($key = null)
    {
        if ($this->authService->is_user_logged_in()) {
            if ($key) {
                return session()->get($key);
            }
            $user = $this->userModel->find_by_id(session()->get('user_id'));
            if ($user) {
                return $user;
            }
        }
        return null;
    }

    public function is_admin(): bool
    {
        if ($this->authService->is_user_logged_in()) {
            $user = $this->userModel->find_by_id(session()->get('user_id'));
            return isset($user['is_admin']) && $user['is_admin'] == 1;
        }
        return false;
    }

    public function get_user_by_id(int $id): ?array
    {
        return $this->userModel->find_by_id($id);
    }

    public function update_password(string $email, string $new_password): bool
    {
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

        $user_id = $this->userModel->find_by_email($email)['id'];

        return $this->userModel->update($user_id, ['password' => $hashed_password]);
    }

    public function delete_user(int $id): bool
    {
        return $this->userModel->delete($id);
    }

}
