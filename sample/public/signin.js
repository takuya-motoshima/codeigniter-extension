(() => {

  /**
   * Set up login form
   * 
   * @return {void}
   */
  function setupLoginForm() {
    const validator = $('#signupForm').validate({
      submitHandler: async (form, event) => {
        event.preventDefault();
        const response = await $.ajax({
          url: 'api/user/signin',
          type: 'POST',
          data: new FormData(form),
          processData: false,
          contentType: false
        });
        console.log('response=', response);
        if (!response) {
          return void validator.showErrors({ username: 'Wrong username or password' });
        }
        location.href = '/';
      }
    });
  }

  // Set up login form
  setupLoginForm();

  // Display BAN message
  if (Cookies.get('show_ban_message')) {
    Cookies.remove('show_ban_message')
    alert('Logged out because it was logged in on another terminal.');
  }
})();