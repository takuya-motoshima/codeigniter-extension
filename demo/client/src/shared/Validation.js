import validations from './validation/index';

/**
 * Form validation.
 */
export default class {
  /**
   * Construct form validation.
   */
  constructor(form, fields) {
    // Check parameters.
    if (typeof form === 'string')
      form = document.querySelector(form);
    else if (!(form instanceof HTMLFormElement))
      throw new TypeError('For the form parameter, specify a character string, HTMLFormElement');

    // Register the validator function.
    for(let [name, func] of Object.entries(validations))
      FormValidation.validators[name] = func;

    // Form element.
    this.form = form;

    // Submit button element.
    this.submit = this.form.querySelector('[type="submit"]');

    // Find the submit button element outside the form.
    if (!this.submit && this.form.id)
      // If the submit button is outside the form.
      this.submit = document.querySelector(`[form="${this.form.id}"]`);
    // if (!this.submit)
    //   console.warn('The submit button was not found');

    // Set up validation.
    const options = {
      fields,
      plugins: {
        trigger: new FormValidation.plugins.Trigger(),
        bootstrap: new FormValidation.plugins.Bootstrap5({
          rowSelector: '.fv-row'
          // rowSelector: (field, ele) => {
          //   switch (field) {
          //     case 'firstName':
          //     case 'lastName':
          //       return '.col-sm-4';
          //     case 'city':
          //     case 'state':
          //     case 'zipcode':
          //       return '.col-sm-3';
          //     default:
          //       return '.form-group';
          //   }
          // }
          // , eleInvalidClass: ''
          // , eleValidClass: ''
        }),
        // submitButton: new FormValidation.plugins.SubmitButton(),
        // Validate hidden fields as well.
        excluded: new FormValidation.plugins.Excluded({
          excluded: (field, element, elements) => $(element).is(':disabled')
        })
      }
    };
    if (this.submit)
      options.plugins.submitButton = new FormValidation.plugins.SubmitButton();
    this.fv = FormValidation.formValidation(this.form, options);
  }

  /**
   * Triggered when the form is completely validated, and all fields are valid.
   *
   * @param  {function} handler Event handler function.
   */
  onValid(handler) {
    this.fv.on('core.form.valid', handler);
    return this;
  }

  /**
   * Triggered when the form is completely validated, and all fields are invalid.
   *
   * @param  {function} handler Event handler function.
   */
  onInvalid(handler) {
    this.fv.on('core.form.invalid', handler);
    return this;
  }

  /**
   * Show loading indication.
   */
  onIndicator() {
    this.submit.setAttribute('data-kt-indicator', 'on');
    this.submit.setAttribute('disabled', 'disabled');
    return this;
  }

  /**
   * Hide loading indication.
   */
  offIndicator() {
    this.submit.removeAttribute('data-kt-indicator');
    this.submit.removeAttribute('disabled');
    return this;
  }

  /**
   * Show error message.
   * 
   * @param  {string} field     The field name.
   * @param  {string} status    The new status. It can be one of the following value.
   *                            'NotValidated': The field is not yet validated
   *                            'Valid': The field is valid
   *                            'Invalid': The field is invalid
   * @param  {string} validator The validator name. If it isn't specified, all validators will be updated.
   */
  setError(field, validator) {
    this.fv.updateFieldStatus(field, 'Invalid', validator);
    return this;
  }

  /**
   * Enable particular validator for given field.
   * 
   * @param  {string} field     The field name.
   * @param  {string} validator The validator name. If it isn't specified, all validators will be enabled.
   */
  enableValidator(field, validator = undefined) {
    this.fv.enableValidator(field, validator);
    return this;
  }

  /**
   * Disable particular validator for given field.
   * 
   * @param  {string} field     The field name.
   * @param  {string} validator The validator name. If it isn't specified, all validators will be disabled.
   */
  disableValidator(field, validator = undefined) {
    this.fv.disableValidator(field, validator);
    return this;
  }

  /**
   * Added items to verify.
   * 
   * @example
   * // The index of row
   * let rowIndex = 0;
   * const demoForm = document.getElementById('demoForm');
   * const fv = FormValidation.formValidation(demoForm, {
   *   fields: {},
   *   plugins: {
   *     trigger: new FormValidation.plugins.Trigger(),
   *     submitButton: new FormValidation.plugins.SubmitButton()
   *   }
   * });
   * fv.on('core.field.added', evnt => {
   *   console.log(`Add ${evnt.field} field`);
   * });
   * document.getElementById('addButton').addEventListener('click', () => {
   *   rowIndex++;
   *   demoForm.insertAdjacentHTML('beforeend', `<input name="task[${rowIndex}].name">`);
   *   fv.addField(`task[${rowIndex}].name`, {
   *     notEmpty: {message: 'The name is required'}
   *   })
   * });
   * 
   * @see {@link https://formvalidation.io/guide/api/add-field|FormValidation • addField() method}
   * @param  {string} field   The field name.
   * @param  {object} validators Validator rules. If the field is already defined, it will be merged with the original validator rule.
   */
   addField(field, validators) {
    this.fv.addField(field, {validators});
    return this;
  }

  /**
   * Return entire fields option.
   */
  getFields() {
    return this.fv.getFields();
  }

  /**
   * Remove the validation field.
   * 
   * @param {string} field The field name.
   */
  removeField(field) {
    const fields = this.getFields();
    if (field in fields)
      this.fv.removeField(field);
    return this;
  }

  /**
   * Remove all validation fields.
   * 
   * @param {string} field The field name.
   */
  removeAllField() {
    for (let field of Object.keys(this.getFields()))
      this.removeField(field);
  }

  /**
    * Added new validation rule.
    * 
    * @example
    * import FormValidation from '~/shared/FormValidation';
    *
    * const demoForm = document.getElementById('demoForm');
    * const fv = new FormValidation(demoForm, {});
    * fv.addRule('strongPassword', () => {
    *   return {
    *     validate: function(input) {
    *       const value = input.value;
    *       // Check the password strength
    *       if (value.length < 8)
    *         return {valid: false};
    *       return {valid: true};
    *     }
    *   };
    * });
    * @see {@link https://formvalidation.io/guide/api/register-validator/|FormValidation • registerValidator() method}
    * @param {string}   name The name of validator.
    * @param {Function} func  The validator function
    */
  addRule(name, func) {
    this.fv.registerValidator(name, func);
    return this;
  }

  /**
    * Clear field messages.
    *
    * @see {@link https://formvalidation.io/guide/api/reset-field/|resetField() method - FormValidation}
    * @param {string}   name The field name.
    * @param {boolean}  func If true, the method resets field value to empty or remove checked, selected attributes.
    */
  resetField(name, reset = false) {
    this.fv.resetField(name, reset);
    return this;
  }

  /**
   * Validate all fields.
   *
   * @returns  {boolean} Returns true if there are no validate errors.
   */
  async validate() {
    return await this.fv.validate()  === 'Valid';
  }
}