/**
 * Send login request.
 */
async function login(form) {
  const res = await $.ajax({
    type: 'POST',
    url: '/api/users/login',
    data: new FormData(form),
    contentType: false,
    processData: false
  });

  //Login failed.
  if (res.error) return void alert('The user name or password is incorrect.');

  // Login successful.
  location.href = '/';
}

// Main processing.
(async () => {
  // Click the login button.
  $('[on-login]').on('submit', async event => {
    event.preventDefault();
    await login(event.currentTarget);
  });
})();