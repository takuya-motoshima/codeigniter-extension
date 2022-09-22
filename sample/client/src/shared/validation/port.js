/**
 * Port number check function.
 */
export default () => ({
  validate: input => {
    const value = Number(input.value);
    let valid = !isNaN(value) && Number.isInteger(value) && value >= 0 && value <= 65535;
    return {valid};
  }
})