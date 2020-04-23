const validator = $('#signupForm').validate({
  submitHandler: async (form, event) => {
    try {
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
    } catch (error) {
      console.error(error);
      alert('An unexpected error has occurred');
    }
  }
});
