<div class="login-modal" id="loginModal" aria-modal="true" role="dialog" style="display:none;">
  <div class="login-modal__overlay" id="loginModalOverlay"></div>
  <div class="login-modal__container">
    <button class="login-modal__close" id="loginModalClose" aria-label="Close">&times;</button>
    <form class="login-form" id="loginForm" method="post" action="">
      <h2 class="login-form__title">Login</h2>
      <?php if (!empty($login_error)): ?>
        <div class="login-form__error"> <?php echo htmlspecialchars($login_error); ?> </div>
      <?php endif; ?>
      <div class="login-form__group">
        <label for="login-username" class="login-form__label">Username</label>
        <input type="text" id="login-username" name="username" class="login-form__input" required autocomplete="username">
      </div>
      <div class="login-form__group">
        <label for="login-password" class="login-form__label">Password</label>
        <input type="password" id="login-password" name="password" class="login-form__input" required autocomplete="current-password">
      </div>
      <div class="login-form__group login-form__group--checkbox">
        <input type="checkbox" id="login-remember" name="remember" class="login-form__checkbox">
        <label for="login-remember" class="login-form__label login-form__label--checkbox">Remember Me</label>
      </div>
      <button type="submit" class="login-form__submit">Login</button>
    </form>
  </div>
</div> 