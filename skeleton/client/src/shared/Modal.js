import hbs from 'handlebars-extd';
import * as utils from '~/shared/utils';

/**
 * base class of modal.
 */
export default class {
  node;
  instance;
  resolve;
  res = false;
  #callStack = [];

  /**
   * Show Modal.
   */
  async show(...params) {
    // Modal initialization.
    if (utils.isAsyncFunction(this.init))
      [this.node, this.instance] = await this.init.apply(this, params);
    else 
      [this.node, this.instance] = this.init.apply(this, params);

    // When the modal is closed, return a response and then dispose this modal.
    this.node
      .on('shown.bs.modal', () => {
        this.afterShown();
      })
      .on('hidden.bs.modal', () => {
        this.resolve(this.res);
        this.dispose();
        this.afterHidden();
      });

    // Initialize response.
    this.res = false;

    // Show Modal.
    this.instance.show();

    // Return Promise to Client.
    return new Promise(resolve => this.resolve = resolve);
  }

  /**
   * Modal initialization.
   * Must be implemented in a subclass.
   */
  async init() {}

  /**
   * Dispose Modal.
   */
  dispose() {
    this.instance.dispose();
    this.node.remove();
  }

  /**
   * Hide Modal.
   */
  hide(res = undefined) {
    if (res !== undefined)
      this.res = res;
    this.instance.hide();
  }

  /**
   * Enable hide with escape key.
   */
  enableHideWithEscapeKey() {
    this.instance._config.keyboard = true;
  }

  /**
   * Disable hide with escape key.
   */
  disableHideWithEscapeKey() {
    this.instance._config.keyboard = false;
  }

  /**
   * Show blockUI.
   */
  showBlockUI(message) {
    const blockUI = new KTBlockUI(this.node.find('.modal-content').get(0), {
      message: hbs.compile(
        `<div class="blockui-message fw-bolder">
          <span class="spinner-border text-primary me-3"></span>
          <span>{{message}}</span>
        </div>`)({message})
    });
    blockUI.block();
    this.#callStack.push(blockUI);
  }

  /**
   * Hide blockUI.
   */
  hideBlockUI() {
    if (this.#callStack.length === 0)
      return;
    const blockUI = this.#callStack.pop();
    blockUI.release();
    blockUI.destroy();
  }

  /**
   * The event after the modal showed. Implementation is done in a subclass.
   */
  afterShown() {}

  /**
   * The event after the modal is hidden. Implementation is done in a subclass.
   */
  afterHidden() {}
}