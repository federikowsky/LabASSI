<div class="forgot-body">
    <div class="main">
        <section class="forgot-password">
            <div class="container">
                <div class="forgot-content">
                    <form action="/auth/password/forgot" method="post" id="forgot_password_form" class="forgot-form">
                        <h2 class="form-title">Forgot Password</h2>
                        <?= csrf_field() ?>
                        <div class="form-group">
                            <label for="email" class="form-label text-white">Email Address:</label>
                            <input type="email" name="email" id="email" class="form-input"
                                value="<?= $inputs['email'] ?? '' ?>" placeholder="Your Email" required />
                            <small>
                                <?= $errors['email'] ?? '' ?>
                            </small>
                        </div>
                        <div class="form-group" id="form-group-submit">
                            <button type="submit" id="submit" class="form-submit mt-3">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>