<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\AuthService;
use App\Core\Logger;
use App\Core\Flash;
use App\Facades\ {
    EWT,
    Filter
};

use App\Exceptions\HTTP\MethodNotAllowedException;
use App\Services\UserService;

class AuthController extends BaseController
{
    protected $authService;
    protected $userService;

    public function __construct(AuthService $authService, UserService $userService) {
        $this->authService = $authService;
        $this->userService = $userService;
    }

    /*************************************** Registration ***************************************/

    public function register()
    {
        if ($this->authService->is_user_logged_in()) {
            return redirect('/');
        }

        $errors = [];
        $inputs = [];

        if (request()->is_method('post')) {
            $fields = [
                'csrf_token' => 'string',
                'username' => 'string | required | alphanumeric | between: 3, 25 | unique: users, username',
                'email' => 'email | required | email | unique: users, email',
                'password' => 'string | required | secure',
                'password2' => 'string | required | same: password',
                'agree' => 'string | required'
            ];

            $messages = [
                'password2' => [
                    'required' => 'Please enter the password again',
                    'same' => 'The password does not match'
                ],
                'agree' => [
                    'required' => 'You need to agree to the terms of services to register'
                ],
            ];

            [$inputs, $errors] = Filter::filter(request()->post(), $fields, $messages);

            if ($errors) {
                return view('auth/register')->with_input([
                    'errors' => $errors,
                    'inputs'=> $inputs
                ]);
            }

            $activation_code = $this->authService->generate_code();
            $ewt = EWT::generate([
                'username' => $inputs['username'],
                'email' => $inputs['email'],
                'activation_code' => $activation_code
            ], 3600);

            if ($this->authService->register($inputs['email'], $inputs['username'], $inputs['password'], $activation_code)) {
                $this->authService->send_activation_email($inputs['email'], $ewt);
                return redirect('auth/login')->with_message(
                    'Your account has been created successfully. Please check your email to activate your account.'
                );
            }
            
        } elseif (request()->is_method('get')) {
            [$errors, $inputs] = session_flash('errors', 'inputs');
            return view('auth/register')->with_input([
                'errors' => $errors,
                'inputs'=> $inputs
            ]);
        } else {
            throw new MethodNotAllowedException('Cannot access this page directly');
        }
    }

    /*************************************** Login ***************************************/

    public function login()
    {
        if ($this->authService->is_user_logged_in()) {
            return redirect('/');
        }

        $inputs = [];
        $errors = [];

        if (request()->is_method('post')) {
            $fields = [
                'csrf_token' => 'string',
                'username' => 'string | required',
                'password' => 'string | required',
                'remember_me' => 'string'
            ];

            $messages = [
                'username' => [
                    'required' => 'Please enter your username'
                ],
                'password' => [
                    'required' => 'Please enter your password'
                ]
            ];

            [$inputs, $errors] = Filter::filter(request()->post(), $fields, $messages);

            if ($errors) {
                return view('auth/login')->with_input([
                    'errors' => $errors,
                    'inputs'=> $inputs
                ]);
            }

            $user = $this->authService->login($inputs['username'], $inputs['password'], isset($inputs['remember_me']));

            if (!$user) {
                return view('auth/login')->with_input([
                    'errors' => $errors,
                    'inputs'=> $inputs
                ])->with_message(
                    'Invalid username or password',
                    Flash::FLASH_ERROR
                );
            }

            // login successfully
            return redirect('/');

        } elseif (request()->is_method('get')) {
            [$errors, $inputs] = session_flash('errors', 'inputs');
            
            return view('auth/login')->with_input([
                'errors' => $errors,
                'inputs'=> $inputs
            ]);
        } else {
            throw new MethodNotAllowedException('Cannot access this page directly');
        }
    }

    /*************************************** Logout ***************************************/

    public function logout()
    {
        if (request()->is_method('post')) {
            $this->authService->logout();
            return redirect('/');
        }
        throw new MethodNotAllowedException('Cannot access this page directly');
    }

    /*************************************** Activation ***************************************/

    public function activate()
    {
        $errors = [];
        $inputs = [];

        if ($this->authService->is_user_logged_in()) {
            return redirect('/');
        }

        if (request()->is_method('get')) {
            $fields = [
                'ewt' => 'string | required'
            ];

            $messages = [
                'ewt' => [
                    'required' => 'Please enter the activation code'
                ]
            ];

            [$inputs, $errors] = Filter::filter(request()->get(), $fields, $messages);

            if (!$errors) {
                $payload = EWT::decode($inputs['ewt']);
                $activation_code = $payload['activation_code'];
                $email = $payload['email'];

                if ($this->authService->is_user_verified($email)) {
                    return redirect('auth/login')->with_message(
                        'Your account is already activated. Please log in.',
                        Flash::FLASH_INFO
                    );
                }

                if($this->authService->activate($email, $activation_code)) {
                    return redirect('auth/login')->with_message(
                        'Your account has been activated successfully.'
                    );
                }
            }

            return redirect('auth/register')->with_message(
                'The activation link is not valid, please register again.', 
                Flash::FLASH_ERROR
            );
        } else {
            throw new MethodNotAllowedException('Cannot access this page directly');
        }

    }


    /*************************************** Update Password ***************************************/
    
    public function update_password()
    {
        $errors = [];
        $inputs = [];

        if (request()->is_method('post')) {
            $fields = [
                'csrf_token' => 'string',
                'old_password' => 'string | required',
                'password' => 'string | required | secure',
                'password2' => 'string | required | same: password'
            ];

            $messages = [
                'old_password' => [
                    'required' => 'Please enter your old password'
                ],
                'password2' => [
                    'required' => 'Please enter the password again',
                    'same' => 'The password does not match'
                ]
            ];

            [$inputs, $errors] = Filter::filter(request()->post(), $fields, $messages);
            if (!$errors) {
                $email = $this->userService->user('email');

                // controlla se old_password è corretta
                if ($this->authService->verify_password($email, $inputs['old_password'])) {
                    $this->userService->update_password($email, $inputs['password']);
                    return redirect()->back()->with_message(
                        'Your password has been updated successfully.'
                    );
                }
                $errors['old_password'] = 'The old password is incorrect';
            }
            return redirect()->back()->with_input([
                'errors' => $errors,
                'inputs'=> $inputs
            ]);
        } elseif (request()->is_method('get')) {                
            [$errors, $inputs] = session_flash('errors', 'inputs');
            return redirect()->back()->with_input([
                'errors' => $errors,
                'inputs'=> $inputs
            ]);
        } else {
            throw new MethodNotAllowedException('Cannot access this page directly');
        }
    }


    /*************************************** Reset Password ***************************************/

    public function reset_password()
    {
        $errors = [];
        $inputs = [];

        if (request()->is_method('post')) {
            $fields = [
                'csrf_token' => 'string',
                'ewt' => 'string | required',
                'password' => 'string | required | secure',
                'password2' => 'string | required | same: password'
            ];

            $messages = [
                'password2' => [
                    'required' => 'Please enter the password again',
                    'same' => 'The password does not match'
                ]
            ];

            [$inputs, $errors] = Filter::filter(request()->post(), $fields, $messages);

            if (!$errors) {
                $payload = EWT::decode($inputs['ewt']);
                $email = $payload['email'];
                
                $this->userService->update_password($email, $inputs['password']);
                return redirect('auth/login')->with_message(
                    'Your password has been reset successfully.'
                );
            }
            return view('auth/reset')->with_input([
                'errors' => $errors,
                'inputs'=> $inputs
            ]);

        } elseif (request()->is_method('get')) {
            $fields = [
                'ewt' => 'string | required'
            ];

            $messages = [
                'ewt' => [
                    'required' => 'Please enter the reset code',
                ]
            ];

            [$inputs, $errors] = Filter::filter(request()->get(), $fields, $messages);

            if (!$errors) {
                return view('auth/reset')->with_input([
                    'errors' => $errors,
                    'inputs'=> $inputs
                ]);
            }
            return redirect('auth/forgot')->with_message(
                'The reset link is not valid, please try again.', 
                Flash::FLASH_ERROR
            );
        } else {
            throw new MethodNotAllowedException('Cannot access this page directly');
        }
    }


    /*************************************** Forgot Password ***************************************/

    public function forgot_password()
    {
        if ($this->authService->is_user_logged_in()) {
            return redirect('/');
        }

        $errors = [];
        $inputs = [];

        if (request()->is_method('post')) {
            $fields = [
                'csrf_token' => 'string',
                'email' => 'email | required | email'
            ];

            $messages = [
                'email' => [
                    'required' => 'Please enter your email'
                ]
            ];

            [$inputs, $errors] = Filter::filter(request()->post(), $fields, $messages);

            if ($errors) {
                return view('auth/forgot')->with_input([
                    'errors' => $errors,
                    'inputs'=> $inputs
                ]);
            }

            $reset_code = EWT::generate([
                'email' => $inputs['email']
            ], 3600);

            $this->authService->send_forgot_email($inputs['email'], $reset_code);
            
            return redirect('auth/login')->with_message(
                'Please check your email to reset your password.'
            );
 
        } elseif (request()->is_method('get')) {
            [$errors, $inputs] = session_flash('errors', 'inputs');
            return view('auth/forgot')->with_input([
                'errors' => $errors,
                'inputs'=> $inputs
            ]);
        } else {
            throw new MethodNotAllowedException('Cannot access this page directly');
        }
    }
}
