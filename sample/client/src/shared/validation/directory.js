/**
 * Directory path check function.
 */
export default () => ({
  validate: input => {
    let valid = false;
    if (input.value === '')
      valid = true;
    else
      valid = /^\/$|(\/[a-zA-Z_0-9-]+)+$/.test(input.value);
    return {valid};
  }
})