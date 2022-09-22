import hbs from 'handlebars-extd';
import Modal from '~/shared/Modal';
import selectAll from '~/shared/selectAll';
import Validation from '~/shared/Validation';
import initClipboard from '~/shared/initClipboard';
import initTooltip from '~/shared/initTooltip';
import Dialog from '~/shared/Dialog';
import Toast from '~/shared/Toast';
import ImageInput from '~/shared/ImageInput';
import UserApi from '~/api/UserApi';

export default class extends Modal {
  #userApi = new UserApi();
  #mode;
  #userId = null;
  #validation;
  #ref;

  async init(mode, userId = null) {
    // console.log(`mode=${mode}, userId=${userId}`);
    this.#mode = mode;
    this.#userId = userId;
    const {data: user} = this.#mode === 'create' ?
      {data: {}} :
      await this.#userApi.getUser(this.#userId);
    // console.log('user=', user);
    const node = this.render(user);
    const instance = new bootstrap.Modal(node);
    this.#ref = selectAll(node);
    this.#initValidation();
    this.#initForm();
    new KTScroll(this.#ref.scroll.get(0));
    return [node, instance];
  }

  render(user) {
    const html = hbs.compile(
      `<div class="modal fade" tabindex="-1" aria-hidden="true">
        <!--begin::Modal dialog-->
        <div class="modal-dialog modal-dialog-centered mw-650px">
          <!--begin::Modal content-->
          <div class="modal-content">
            <!--begin::Form-->
            <form data-ref="form" class="form" novalidate="novalidate">
              <!--begin::Modal header-->
              <div class="modal-header" id="header{{uniqueId}}">
                <!--begin::Modal title-->
                <h2>
                  {{#if (eq mode 'create')}}
                    Create a user
                  {{else if (eq mode 'update')}}
                    Update user
                  {{/if}}
                </h2>
                <!--end::Modal title-->
                <!--begin::Close-->
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal" aria-label="Close">
                  <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                  <span class="svg-icon svg-icon-1"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                    <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                  </svg></span>
                  <!--end::Svg Icon-->
                </div>
                <!--end::Close-->
              </div>
              <!--end::Modal header-->
              <!--begin::Modal body-->
              <div class="modal-body py-10 px-lg-17">
                <!--begin::Scroll-->
                <div data-ref="scroll" class="scroll-y me-n7 pe-7" id="scroll{{uniqueId}}" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#header{{uniqueId}}" data-kt-scroll-wrappers="#scroll{{uniqueId}}" data-kt-scroll-offset="300px">
                  <!--begin::Input group-->
                  <div class="fv-row mb-10 d-flex flex-column">
                    <!--begin::Label-->
                    <label class="fs-5 fw-bolder form-label mb-2"><span class="required">Profile photo</span></label>
                    <!--end::Label-->
                    <!--begin::Image input-->
                    <div data-ref="uploader" style="width: 125px; height: 125px;"></div>
                    <input data-ref="icon" name="user[icon]" type="hidden">
                    <!--end::Image input-->
                    <!--begin::Hint-->
                    <div class="form-text text-gray-700">JPG, JPEG or PNG file formats are accepted.</div>
                    <!--end::Hint-->
                  </div>
                  <!--end::Input group-->
                  <!--begin::Input group-->
                  <div class="d-flex flex-column mb-7 fv-row">
                    <!--begin::Label-->
                    <label class="required fs-6 fw-bolder mb-2">Role</label>
                    <!--end::Label-->
                    <!--begin::Input-->
                    <select name="user[role]" class="form-select form-select-solid">
                      <option value="">Select roll</option>
                      <option value="admin"{{#if (eq user.role 'admin')}} selected{{/if}}>Admin</option>
                      <option value="member"{{#if (eq user.role 'member')}} selected{{/if}}>General User</option>
                    </select>
                    <!--end::Input-->
                  </div>
                  <!--end::Input group-->
                  <!--begin::Input group-->
                  <div class="d-flex flex-column mb-7 fv-row">
                    <!--begin::Label-->
                    <label class="required fs-6 fw-bolder mb-2">Email (login ID)</label>
                    <!--end::Label-->
                    <!--begin::Input-->
                    <input class="form-control form-control-solid" name="user[email]" type="email" value="{{user.email}}" maxlength="255" />
                    <!--end::Input-->
                  </div>
                  <!--end::Input group-->
                  <!--begin::Input group-->
                  <div class="d-flex flex-column mb-7 fv-row">
                    <!--begin::Label-->
                    <label class="required fs-6 fw-bolder mb-2">Name</label>
                    <!--end::Label-->
                    <!--begin::Input-->
                    <input class="form-control form-control-solid" name="user[name]" type="text" value="{{user.name}}" maxlength="30" />
                    <!--end::Input-->
                  </div>
                  <!--end::Input group-->
                  <!--begin::Input group-->
                  <div class="fv-row mb-10">
                    <div class="d-flex flex-wrap align-items-center mb-2">
                      <!--begin::Label-->
                      <label class="fs-6 fw-bolder required">Password</label>
                      <!--end::Label-->
                      {{#if (eq mode 'update')}}
                        <!--begin::Action-->
                        <label class="form-check form-check-inline form-check-solid ms-auto">
                          <input data-bs-toggle="collapse" data-bs-target="#passwordCollapse" class="form-check-input" name="user[changePassword]" type="checkbox" value="1">
                          <span class="fw-bolder ps-2 fs-6">Change your password</span>
                        </label>
                        <!--end::Action-->
                      {{/if}}
                    </div>
                    {{#if (eq mode 'update')}}<div class="collapse" id="passwordCollapse">{{/if}}
                      <!--begin::Input wrapper-->
                      <div class="position-relative">
                        <input data-ref="user.password" name="user[password]" class="form-control form-control-lg form-control-solid" type="password" autocomplete="off" maxlength="128" style="background-image: none !important;" />
                        <span data-on-toggle-password-visibility class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2">
                          <i class="bi bi-eye-slash fs-2"></i>
                          <i class="bi bi-eye fs-2 d-none"></i>
                        </span>
                      </div>
                      <!--end::Input wrapper-->
                    {{#if (eq mode 'update')}}</div>{{/if}}
                  </div>
                  <!--end::Input group-->
                </div>
                <!--end::Scroll-->
              </div>
              <!--end::Modal body-->
              <!--begin::Modal footer-->
              <div class="modal-footer flex-center">
                <!--begin::Button-->
                <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</button>
                <!--end::Button-->
                <!--begin::Button-->
                <button type="submit" class="btn btn-primary">
                  <span class="indicator-label">
                    {{#if (eq mode 'create')}}
                      Create
                    {{else if (eq mode 'update')}}
                      Updating
                    {{/if}}
                  </span>
                  <span class="indicator-progress">
                    {{#if (eq mode 'create')}}
                      Creating a user...
                    {{else if (eq mode 'update')}}
                      Updating a user...
                    {{/if}}
                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                  </span>
                </button>
                <!--end::Button-->
              </div>
              <!--end::Modal footer-->
            </form>
            <!--end::Form-->
          </div>
          <!--end::Modal content-->
        </div>
        <!--end::Modal dialog-->
      </div>`)({mode: this.#mode, user, uniqueId: +new Date});
    return $(html).appendTo('body');
  }

  #initValidation() {
    const enablePasswordValidation = this.#mode === 'create';
    this.#validation = new Validation(this.#ref.form.get(0), {
      'user[role]': {
        validators: {
          notEmpty: {message: 'Rolls are required.'}
        }
      },
      'user[email]': {
        validators: {
          notEmpty: {message: 'Email is required.'},
          emailAddress: {message: 'Email is incorrect.'},
          remote: {
            url: '/api/users/email-exists',
            method: 'GET',
            data: () => this.#mode === 'update' ? {excludeUserId: this.#userId} : {},
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
            enabled: enablePasswordValidation
          },
          regexp: {
            regexp: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&\-\.])[a-z\d@$!%*?&\-\.]{8,}$/i,
            message: 'Alphanumeric symbols (@$!%*?&-.) Please enter at least 8 characters with a mix of.',
            enabled: enablePasswordValidation
          }
        }
      }
    });
  }

  #initForm() {
    this.#validation.onValid(async () => {
      try {
        this.#validation.onIndicator();
        const {data} = this.#mode === 'create' ?
          await this.#userApi.createUser(new FormData(this.#validation.form)) :
          await this.#userApi.updateUser(this.#userId, new FormData(this.#validation.form));
        this.#validation.offIndicator();
        if (data.error)
          if (data.error === 'userNotFound') {
            await Dialog.warning('This user has been deleted.');
            return void super.hide(true);
          } else
            throw Error('Unknown error');
        Toast.success(`User ${{create: 'created', update: 'updated'}[this.#mode]}.`);
        super.hide(true);
      } catch (err) {
        this.#validation.offIndicator();
        Dialog.unknownError();
        throw err;
      }
    });
    this.#ref.form
      .on('click', '[data-on-toggle-password-visibility]', evnt => {
        const span = $(evnt.currentTarget);
        if (this.#ref.user.password.attr('type') === 'password') {
          this.#ref.user.password.attr('type', 'text');
          span.find('.bi-eye').removeClass('d-none');
          span.find('.bi-eye-slash').addClass('d-none');
        } else {
          this.#ref.user.password.attr('type', 'password');
          span.find('.bi-eye').addClass('d-none');
          span.find('.bi-eye-slash').removeClass('d-none');
        }
      })
      .on('show.bs.collapse hide.bs.collapse', '#passwordCollapse', evnt => {
        if (evnt.type === 'show')
          this.#validation.enableValidator('user[password]');
        else
          this.#validation.disableValidator('user[password]');
      });
    if (this.#ref.uploader)
      new ImageInput(this.#ref.uploader.get(0), {
        currentImage: this.#mode === 'update' ? `/upload/${this.#userId}.png` : null,
        defaultImage: '/build/media/misc/blank-user-icon.png',
        hidden: this.#ref.icon.get(0),
        resize: false
      });
    initClipboard(this.#ref.form);
    initTooltip(this.#ref.form);
  }
}