import fusion from 'deep-fusion';

/**
 * Display various dialogs.
 */
export default class {
  /**
   * Show confirm.
   */
  static async confirm(text, title = null, options = null) {
    // Initialize options.
    options = fusion({
      icon: 'question',
      confirmButtonText: 'OK',
      cancelButtonText: 'Cancel',
      customClass: {
        htmlContainer: 'overflow-hidden',
        confirmButton: `btn btn-primary fw-bolder`,
        cancelButton: 'btn btn-light fw-bolder'
      },
      didOpen: toast => {},
      preConfirm: () => {}
    }, options);

    // Show the dialog.
    return (await Swal.fire({
      html: text,
      title,
      showCancelButton: true,
      buttonsStyling: false,
      ...options
    })).isConfirmed;
  }

  /**
   * Show success.
   */
  static async success(text, title = null, options = null) {
    // Initialize options.
    options = fusion({
      confirmButtonText: 'OK',
      customClass: {
        confirmButton: `btn btn-primary fw-bolder`,
      }
    }, options);

    // Show the dialog.
    return Swal.fire({
      html: text,
      title,
      icon: 'success',
      buttonsStyling: false,
      ...options
    });
  }

  /**
   * Show error.
   */
  static async error(text, title = null, options = null) {
    // Initialize options.
    options = fusion({
      confirmButtonText: 'OK',
    }, options);

    // Show the dialog.
    return Swal.fire({
      text,
      title,
      icon: 'error',
      buttonsStyling: false,
      customClass: {
        confirmButton: `btn btn-danger fw-bolder`,
      },
      ...options
    });
  }

  /**
   * Show warning.
   */
  static async warning(text, title = null, options) {
    // Initialize options.
    options = fusion({
      confirmButtonText: 'OK'
    }, options);

    // Show the dialog.
    return Swal.fire({
      html: text,
      title,
      icon: 'warning',
      buttonsStyling: false,
      customClass: {
        confirmButton: `btn btn-warning fw-bolder`,
      },
      ...options
    });
  }

  /**
   * Show info.
   */
  static async info(text, title = null, options) {
    // Initialize options.
    options = fusion({
      confirmButtonText: 'OK'
    }, options);

    // Show the dialog.
    return Swal.fire({
      html: text,
      title,
      icon: 'info',
      buttonsStyling: false,
      customClass: {
        confirmButton: `btn btn-primary fw-bolder`,
      },
      ...options
    });
  }

  /**
   * Show unknown error.
   */
  static async unknownError() {
    return this.error(
      'The process was interrupted due to an error. Please try again. If the error occurs repeatedly, please contact our Contact Center.',
      'An unexpected error has occurred.'
    );
  }

  /**
   * Show loading.
   */
  static async loading(text, title = null, options) {
    // Show the dialog.
    return Swal.fire({
      html: text,
      title,
      allowEscapeKey: false,
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading()
      }
    });
  }

  /**
   * Close.
   */
  static close() {
    Swal.close();
  }
}