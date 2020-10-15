// Set the error display position of jQuery Validation Plugin
$.validator.setDefaults({
  errorPlacement: (error, input) => {
    if (input.parent().is('.form-group')) {
      error.insertAfter(input);
    } else {
      input.parent().append(error);
      input.closest('.form-group').append(error);
    }
  }
});