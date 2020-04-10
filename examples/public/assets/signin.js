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
    if (!response) {
      return void validator.showErrors({ username: 'Wrong username or password' });
    }
    location.href = '/';
  }
});
