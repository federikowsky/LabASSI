<div class="reset-body">
    <div class="main">
        <section class="reset-password">
            <div class="container">
                <div class="reset-content">
                    <form action="/auth/password/reset" method="post" id="reset_password_form" class="reset-form">
                        <h2>Reset Password</h2>
                        <?= csrf_field() ?>
                        <?= ewt_field($inputs['ewt']) ?>
                        <div class="form-group">
                            <label class="form-label text-white" for="password">New Password:</label>
                            <input type="password" id="password" name="password" placeholder="Enter New Password"
                                class="form-input <?= error_class($errors, 'password') ?>" required>
                            <small><?= $errors['password'] ?? '' ?></small>
                        </div>
                        <div class="form-group">
                            <label class="form-label text-white" for="password2">Confirm Password:</label>
                            <input type="password" id="password2" name="password2"  placeholder="Confirm Password"
                                class="form-input <?= error_class($errors, 'password2') ?>" required>
                            <small><?= $errors['password2'] ?? '' ?></small>
                        </div>
                        <div class="form-group" id="form-group-submit">
                            <button type="submit" id="submit" class="form-submit mt-3">Reset Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>

