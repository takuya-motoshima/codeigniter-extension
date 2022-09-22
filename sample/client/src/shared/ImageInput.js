import hbs from 'handlebars-extd';
import initTooltip from '~/shared/initTooltip';
import fusion from 'deep-fusion';
import Scissor from 'js-scissor';
import compareImg from 'compare-img';
import fetchDataUrl from '~/shared/fetchDataUrl';

/**
 * Image input.
 * 
 * @example
 * <div id="imageInput"></div>
 * 
 * import ImageInput from '~/shared/ImageInput';
 * 
 * // Initialize image input.
 * const imageInput =  new ImageInput(document.querySelector('#imageInput'), {
 *   enableCancel: false,
 *   currentImage: 'current.png',
 *   defaultImage: 'default.png'
 * });
 * imageInput.on('change', dataUrl => {});
 * 
 * @see {@link https://preview.keenthemes.com/metronic8/demo1/documentation/forms/image-input.html} Custom Bootstrap Image Input with Preview Component by Keenthemes.
 */
export default class {
  /**
   * Construct image input.
   */
  constructor(context, options) {
    // Returns an error if the context is not an HTML element.
    if (!(context instanceof HTMLElement))
      throw new TypeError('The context parameter is invalid');

    // Initialize options.
    this.options = fusion({
      enableCancel: false,
      currentImage: null,
      defaultImage: null,
      width: 125,
      height: 125,
      fit: 'contain',
      language: {
        change: 'Change the image.',
        remove: 'Delete an image.',
        cancel: 'Cancels changes.'
      },
      readonly: false,
      mime: 'image/png',
      hidden: null,
      resize: true,
      maxWidth: 960
    }, options);

    // Attach uploader UI.
    this.attach(context);
  }

  /**
   * Attach uploader UI.
   */
  async attach(context) {
    // If there is a default image, save the base64 of the default image.
    if (this.options.defaultImage)
      this.options.defaultImageBase64 = await fetchDataUrl(this.options.defaultImage);

    // If the default image and the displayed image are the same, the image change cancel button is not displayed in the initial display.
    if (this.options.currentImage && this.options.defaultImage) {
      const useDef = await compareImg(this.options.currentImage, this.options.defaultImage);
      if (useDef)
        this.options.currentImage = null;
    }

    // Embed the current image in the hidden element.
    if (this.options.defaultImage || this.options.currentImage) {
      let src = this.options.defaultImageBase64;
      if (this.options.currentImage) {
        const dataUrl = await fetchDataUrl(this.options.currentImage);
        if (dataUrl)
          src = dataUrl;
        else
          this.options.currentImage = null;
      }
      if (this.options.hidden)
        this.options.hidden.value = src;
    }

    // Draw uploader UI.
    this.instance = this.render(context, this.options);

    // Set event.
    this.instance.on('kt.imageinput.changed', async input => {
      // If cancellation is disabled, the delete button is forcibly displayed when editing the image.
      if (!this.options.enableCancel)
        input.removeElement.style.display = 'flex';
    });
    this.instance.on('kt.imageinput.removed', async input => {
      // Set the default image when the image is canceled.
      if (this.options.defaultImage && this.options.hidden)
        this.options.hidden.value = this.options.defaultImageBase64;

      // If cancellation is disabled, the delete button is forcibly hidden when deleting an image.
      if (!this.options.enableCancel)
        input.removeElement.style.display = 'none';
    });

    // Monitor image changes.
    const observer = new MutationObserver(async mutationsList => {
      for (let mutation of mutationsList) {
        if (mutation.type !== 'attributes' || mutation.attributeName !== 'style')
          continue;

        // Get the image source from the background style.
        const matched = this.instance.wrapperElement.style.backgroundImage.match(/(?:\(['"]?)(.*?)(?:['"]?\))/);
        if (!matched)
          continue;
        const url = matched[1];
          
        // Resize and return the image.
        const dataUrl = /^data:image\/[a-z]+;base64,/.test(url) ? url : await fetchDataUrl(url);

        // Set the selected image to the hidden element.
        if (this.options.hidden) {
          const scissor = new Scissor(dataUrl);
          if (this.options.resize) {
            if (this.options.width > this.options.height)
              this.options.hidden.value = (await scissor.resize(this.options.width, null, {format: this.options.mime})).toBase64();
            else
              this.options.hidden.value = (await scissor.resize(null, this.options.height, {format: this.options.mime})).toBase64();
          } else
            this.options.hidden.value = (await scissor.resize(this.options.maxWidth, null, {format: this.options.mime})).toBase64();
        }
      }
    });
    observer.observe(this.instance.wrapperElement, { 
      attributes: true, 
      childList: false,
      characterData: true,
      attributeFilter: ['style'],
    });
  }

  /**
   * Draw image uploader element.
   */
  render(context, options) {
    const html = `
      <div class="image-input-wrapper bgi-position-center" style="background-size: {{fit}}; {{#if currentImage}}background-image: url({{currentImage}});{{/if}} width: {{width}}px; height: {{height}}px;"></div>
      <label class="btn btn-icon btn-circle btn-color-muted btn-active-color-primary w-25px h-25px bg-body shadow {{ifx readonly 'd-none'}}"
              data-kt-image-input-action="change"
              data-bs-toggle="tooltip" data-bs-dismiss="click" title="{{language.change}}">
              <i class="bi bi-pencil-fill fs-7"></i>
              <input type="file" name="avatar" accept=".png, .jpg, .jpeg" />
              <input type="hidden" name="avatar_remove" />
      </label>
      {{#if enableCancel}}
        <span class="btn btn-icon btn-circle btn-color-muted btn-active-color-primary w-25px h-25px bg-body shadow {{ifx readonly 'd-none'}}"
              data-kt-image-input-action="cancel"
              data-bs-toggle="tooltip" data-bs-dismiss="click" title="{{language.cancel}}">
              <i class="bi bi-x fs-2"></i>
        </span>
      {{/if}}
      <span class="btn btn-icon btn-circle btn-color-muted btn-active-color-primary w-25px h-25px bg-body shadow {{ifx readonly 'd-none'}}"
            data-kt-image-input-action="remove"
            data-bs-toggle="tooltip" data-bs-dismiss="click" title="{{language.remove}}">
            <i class="bi bi-x fs-2"></i>
      </span>`;
    context.classList.add('image-input', 'image-input-outline', options.currentImage ? 'image-input-changed' : 'image-input-empty', 'bg-body');
    // context.style.backgroundColor = '#babbbe';
    context.style.width = 'fit-content';
    context.style.backgroundImage = `url(${options.defaultImage})`;
    context.setAttribute('data-kt-image-input', 'false');
    context.insertAdjacentHTML('afterbegin', hbs.compile(html)(options));
    initTooltip(context);
    const instance = new KTImageInput(context);
    if (options.currentImage && !options.enableCancel)
      instance.removeElement.style.display = 'flex';

    // Removes the name attribute that was automatically added to the file element and the name attribute of the hidden element that was automatically added.
    // This prevents unintended data from being sent when the form is sent.
    instance.inputElement.removeAttribute('name');
    instance.hiddenElement.removeAttribute('name');
    return instance;
  }
}