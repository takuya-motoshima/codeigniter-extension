import '~/pages/userlogs.css';
import hbs from 'handlebars-extd';
import selectAll from '~/shared/selectAll';
import Datatable from '~/shared/Datatable';

function initTable() {
  logTable = new Datatable(ref.logTable, {
    ajax: {
      url: '/api/userlogs',
      data: d => {
        d.search = {
          name: ref.usernameOptions.val()
        }
      }
    },
    dom: `<'row'<'col-12 dataTables_pager'p>><'row'<'col-12'tr>><'row'<'col-12 dataTables_pager'p>>`,
    columnDefs: [
      {targets: 0, data: 'created', render: data => hbs.compile(`{{formatDate 'YYYY/M/D HH:mm:ss' data}}`)({data})},
      {targets: 1, data: 'name'},
      {targets: 2, data: 'ip'},
      {targets: 3, data: 'message'}
    ],
    order: [[0, 'desc']]
  });
}

function initForm() {
  $('body').on('input', '[data-on-search-change]' , evnt => {
    if (searchTimer)
      clearTimeout(searchTimer);
    searchTimer = setTimeout(() => logTable.reload(), 300);
  });
}

const ref = selectAll('#kt_app_content_container');
let logTable;
let searchTimer;
initTable();
initForm();