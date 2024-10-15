<?php
// src/Models/User.php

namespace App\Models;

use PDO;
use PDOStatement;

class User {
    protected $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    private function execute(PDOStatement $stmt): bool
    {
        try {
            return $stmt->execute();
        } catch (\PDOException $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Run a costum query 
     * @param string $sql
     * @param array $params
     * @return bool|\PDOStatement
     */
    public function execute_query(string $sql, array $params = [])
    {
        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $this->execute($stmt);
        return $stmt;
    }

    /**
     * Check if a user is an admin
     * @param mixed $user_id
     * @return bool
     */
    public function is_admin($user_id): bool
    {
        $query = 'SELECT is_admin
                FROM users
                WHERE id = :id';

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $user_id);
        $this->execute($stmt);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user['is_admin'] === '1';
    }

    /**
     * Check if a user exists
     * @param mixed $email
     * @param mixed $username
     * @return bool
     */
    public function exists($email, $username): bool
    {
        $query = 'SELECT COUNT(*) as count
                FROM users
                WHERE email = :email OR username = :username';

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':username', $username);
        $this->execute($stmt);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int) $result['count'] > 0;
    }

    /**
     * Create a new user 
     * @param string $email
     * @param string $username
     * @param string $password
     * @param string $activation_code
     * @param int $expiry
     * @param bool $is_admin
     * @return bool
     */
    public function create(string $email, string $username, string $password, string $activation_code, int $expiry = 60 * 60 * 24 * 1, bool $is_admin = false): bool
    {
        // sanity check
        if ($this->exists($email, $username)) {
            return false;
        }

        $query = 'INSERT INTO users(username, email, password, is_admin, activation_code, activation_expiry)
            VALUES(:username, :email, :password, :is_admin, :activation_code,:activation_expiry)';
        
        $stmt = $this->db->prepare($query);

        $stmt->bindValue(':username', $username);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':password', password_hash($password, PASSWORD_BCRYPT));
        $stmt->bindValue(':is_admin', (int) $is_admin, PDO::PARAM_INT);
        $stmt->bindValue(':activation_code', hash_hmac('sha256', $activation_code, SECRET_KEY));
        $stmt->bindValue(':activation_expiry', date('Y-m-d H:i:s', time() + $expiry));


        return $this->execute($stmt);
    }

    /**
     * Activate a user
     * @param int $user_id
     * @return bool
     */
    public function activate(int $user_id): bool
    {
        $query = 'UPDATE users
                SET active = 1, activated_at = CURRENT_TIMESTAMP
                WHERE id=:id';
    
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
    
        return $this->execute($stmt);
    }

    /**
     * Update a user field, only the fields provided in the $data array will be updated
     * if no valid fields provided for update throw an exception
     
     * @param int $user_id
     * @param string $password
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function update($id, array $data): bool
    {
        $validFields = ['username', 'email', 'password', 'is_admin', 'active'];
        $data = array_intersect_key($data, array_flip($validFields));

        // if no valid fields provided for update throw an exception
        if (empty($data)) {
            throw new \InvalidArgumentException('No valid fields provided for update.');
        }

        // Create the SET part of the SQL query
        $setParts = implode(', ', array_map(fn($field) => "$field = :$field", array_keys($data)));

        // Build the query
        $query = "UPDATE users SET $setParts WHERE id = :id";

        $stmt = $this->db->prepare($query);

        // Binding
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $this->execute($stmt);
    }

    /**
     * Delete a user
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $query = 'DELETE FROM users
            WHERE id =:id';
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id);

        return $this->execute($stmt);
    }

    /**
     * Insert a new user token for the remember me feature
     * @param string $email
     * @return array
     */
    public function insert_user_token(int $user_id, string $selector, string $validator, string $expiry): bool
    {
        $query = 'INSERT INTO user_tokens(user_id, selector, hashed_validator, expiry)
                VALUES(:user_id, :selector, :hashed_validator, :expiry)';

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':user_id', $user_id);
        $stmt->bindValue(':selector', $selector);
        $stmt->bindValue(':hashed_validator', hash_hmac('sha256', $validator, SECRET_KEY));
        $stmt->bindValue(':expiry', $expiry);

        return $this->execute($stmt);
    }

    /**
     * Delete all user tokens
     * @param int $user_id
     * @return bool
     */
    public function delete_user_token(int $user_id): bool
    {
        $query = 'DELETE FROM user_tokens WHERE user_id = :user_id';

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':user_id', $user_id);

        return $this->execute($stmt);
    }

    /**
     * Find a user by email
     * @param string $email
     * @return array
     */
    public function find_by_email($email)
    {
        $query = 'SELECT id, username, email, password, active, is_admin
                FROM users
                WHERE email=:email';

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':email', $email);
        $this->execute($stmt);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Find a user by username
     * @param string $username
     * @return array
     */
    public function find_by_username($username)
    {
        $query = 'SELECT id, username, email, password, active, is_admin
                FROM users
                WHERE username=:username';

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':username', $username);
        $this->execute($stmt);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Find a user by id
     * @param int $id
     * @return array
     */
    public function find_by_id($id)
    {
        $query = 'SELECT id, username, email, password, active, is_admin
                FROM users
                WHERE id=:id';

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id);
        $this->execute($stmt);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Find a user by activation code 
     * @param string $activation_code
     * @return array
     */
    public function find_by_activation_code($activation_code)
    {
        $hashed_activation_code = hash_hmac('sha256', $activation_code, SECRET_KEY);

        $query = 'SELECT id, username, email, password, active, is_admin
                FROM users
                WHERE activation_code=:activation_code';

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':activation_code', $hashed_activation_code);
        $this->execute($stmt);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Find an unverified user by activation code
     * @param string $activation_code
     * @return mixed
     */
    public function find_unverified_user($email, $activation_code)
    {
        $hashed_activation_code = hash_hmac('sha256', $activation_code, SECRET_KEY);
        
        // find the user with the activation code
        $query = 'SELECT id, activation_code, activation_expiry < now() as expired
                FROM users
                WHERE active = 0 AND email=:email AND activation_code=:activation_code';

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':activation_code', $hashed_activation_code);
        $stmt->bindValue(':email', $email);
        $this->execute($stmt);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // if the activation code is not expired return the user
            if ((int) $user['expired'] === 0) {
                return $user;
            }
            
            // already expired, delete the in active user with expired activation code
            $this->delete($user['id']);
        }

        return null;
    }

    /**
     * Find a user token by selector
     * @param string $selector
     * @return array
     */
    public function find_user_token_by_selector(string $selector)
    {
        $query = 'SELECT id, selector, hashed_validator, user_id, expiry
                FROM user_tokens
                WHERE selector = :selector AND
                      expiry >= now()
                LIMIT 1';

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':selector', $selector);
        $this->execute($stmt);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Find a user by token
     * @param string $selector
     * @return array
     */
    public function find_user_by_token(string $selector)
    {
        $query = 'SELECT users.id, username
                FROM users
                INNER JOIN user_tokens ON user_id = users.id
                WHERE selector = :selector AND
                      expiry > now()
                LIMIT 1';

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':selector', $selector);
        $this->execute($stmt);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Find a user by oauth provider
     * @param string $provider
     * @param string $provider_id
     * @return array
     */
    public function find_by_oauth_provider(string $provider, string $provider_id): ?array
    {
        $query = "SELECT u.* FROM users u
                  JOIN user_oauth o ON u.id = o.user_id
                  WHERE o.provider = :provider AND o.provider_id = :provider_id";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':provider', $provider);
        $stmt->bindValue(':provider_id', $provider_id);
        $this->execute($stmt);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }
    
    /**
     * Register a user with oauth
     * @param array $user_data
     * @return int|bool
     */
    public function register_oauth_user(array $user_data): int|bool
    {
        $query = 'INSERT INTO users(username, email, password, active, created_at)
                  VALUES(:username, :email, NULL, 1, :created_at)';

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':username', $user_data['username']);
        $stmt->bindValue(':email', $user_data['email']);
        $stmt->bindValue(':created_at', date('Y-m-d H:i:s'));

        if ($this->execute($stmt)) {
            $user_id = $this->db->lastInsertId();

            $oauth_query = 'INSERT INTO user_oauth(user_id, provider, provider_id, refresh_token, created_at)
                            VALUES(:user_id, :provider, :provider_id, :refresh_token, :created_at)';

            $oauth_stmt = $this->db->prepare($oauth_query);
            $oauth_stmt->bindValue(':user_id', $user_id);
            $oauth_stmt->bindValue(':provider', $user_data['oauth_provider']);
            $oauth_stmt->bindValue(':provider_id', $user_data['provider_id']);
            $oauth_stmt->bindValue(':refresh_token', $user_data['refresh_token']);
            $oauth_stmt->bindValue(':created_at', date('Y-m-d H:i:s'));

            if ($this->execute($oauth_stmt))
                return $user_id;
        }

        throw new \Exception('Failed to register OAuth user.');
    }

    /**
     * Update the refresh token for a user
     * @param int $user_id
     * @param string $refresh_token
     * @return bool
     */
    public function update_refresh_token(int $user_id, string $refresh_token): bool
    {
        $query = 'UPDATE user_oauth SET refresh_token = :refresh_token, updated_at = :updated_at
                  WHERE user_id = :user_id AND provider = "google"';

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':user_id', $user_id);
        $stmt->bindValue(':refresh_token', $refresh_token);
        $stmt->bindValue(':updated_at', date('Y-m-d H:i:s'));

        return $this->execute($stmt);
    }
}
