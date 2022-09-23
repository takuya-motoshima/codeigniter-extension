import '~/pages/editPersonal.css';
import selectAll from '~/shared/selectAll';
import Validation from '~/shared/Validation';
import Toast from '~/shared/Toast';
import Dialog from '~/shared/Dialog';
import trim from '~/shared/trim';
import UserApi from '~/api/UserApi';

function initValidation() {
  validation = new Validation(ref.personalForm.get(0), {
    'user[email]': {
      validators: {
        notEmpty: {message: 'Email is required.'},
        emailAddress: {message: 'Email is incorrect.'},
        remote: {
          url: '/api/users/email-exists',
          method: 'GET',
          message: 'This email is in use by another user.'
        }
      }
    },
    'user[name]': {
      validators: {
        notEmpty: {message: 'Name is required.'}
      }
    },
    'user[password]': {
      validators: {
        notEmpty: {
          message: 'Password is required.',
          enabled: false
        },
        regexp: {
          regexp: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&\-\.])[a-z\d@$!%*?&\-\.]{8,}$/i,
          message: 'Alphanumeric symbols (@$!%*?&-.) Please enter at least 8 characters with a mix of.',
          enabled: false
        },
        remote: {
          url: '/api/users/password-security-check',
          method: 'GET',
          message: 'The same string as the current password and email cannot be used.',
          enabled: false
        }
      }
    }
  });
}

function initForm() {
  validation.onValid(async () => {
    try {
      validation.onIndicator();
      const {data} = await userApi.updateProfile(new FormData(validation.form));
      validation.offIndicator();
      if (data.error)
        if (data.error === 'userNotFound') {
          await Dialog.warning('The account you are using has been deleted and you will be forcibly logged out.');
          return void userApi.logout();
        } else
          throw Error('Unknown error');
      Toast.success('Personal settings have been updated.');
      const currentEmail = trim(ref.email.val(), true);
      if (originalEmail !== currentEmail) {
        await Dialog.info('Email address has been changed. Please re-login.');
        return void userApi.logout();
      }
    } catch (err) {
      validation.offIndicator();
      Dialog.unknownError();
      throw err;
    }
  });
  ref.personalForm
    .on('show.bs.collapse hide.bs.collapse', '#passwordCollapse', evnt => {
      if (evnt.type === 'show')
        validation.enableValidator('user[password]');
      else
        validation.disableValidator('user[password]');
    })
    .on('click', '[data-on-toggle-password-visibility]', evnt => {
      const span = $(evnt.currentTarget)      
      if (ref.password.attr('type') === 'password') {
        ref.password.attr('type', 'text');
        span.find('.bi-eye').removeClass('d-none');
        span.find('.bi-eye-slash').addClass('d-none');
      } else {
        ref.password.attr('type', 'password');
        span.find('.bi-eye').addClass('d-none');
        span.find('.bi-eye-slash').removeClass('d-none');
      }
    })
    .on('reset', () => {
      if (ref.changePassword.prop('checked')) {
        ref.changePassword.prop('checked', false);
        const collapse = bootstrap.Collapse.getInstance(ref.passwordCollapse.get(0));
        collapse.hide();
        validation.disableValidator('user[password]');
      }
    });
}

const userApi = new UserApi();
const ref = selectAll('#kt_app_content_container');
const originalEmail = trim(ref.email.val(), true);
let validation;
initValidation();
initForm();