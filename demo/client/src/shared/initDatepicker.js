import moment from 'moment';

/**
 * Initialize date range picker.
 */
export default node => {
  moment.locale('ja');
  return node
    .on('apply.daterangepicker', (evnt, picker = null) => {
      const isDateCleared = picker == null;
      if (isDateCleared)
        return;
      const format = picker.locale.format;
      picker.element.val(`${picker.startDate.format(format)} - ${picker.endDate.format(format)}`);
    })
    .on('cancel.daterangepicker', (evnt, picker) => {
      picker.element.val('').trigger('apply.daterangepicker');
    })
    .daterangepicker({
      timePicker: false,
      autoUpdateInput: false,
      // autoApply: true,
      showDropdowns: false,
      maxDate: moment().format('YYYY/M/D'),
      locale: {
        format: 'YYYY/M/D',
        daysOfWeek: moment.weekdaysMin(),
        monthNames: moment.monthsShort(),
        applyLabel: '決定',
        cancelLabel: '削除'
      }
    })
    .data('daterangepicker');
}