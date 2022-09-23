/**
 * Hostname check function.
 */
export default () => ({
  validate: input => {
    let valid = false;
    if (input.value === '')
      valid = true;
    else
      valid = /^(?!:\/\/)([a-zA-Z0-9-_]+\.)*[a-zA-Z0-9][a-zA-Z0-9-_]+\.[a-zA-Z]{2,11}?$/.test(input.value) || input.value === 'localhost';
      // valid = /^(?!:\/\/)([a-zA-Z0-9-_]+\.)*[a-zA-Z0-9][a-zA-Z0-9-_]+\.[a-zA-Z]{2,11}?$/.test(input.value);
    return {valid};
  }
})