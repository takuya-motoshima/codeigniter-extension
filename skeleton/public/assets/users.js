/**
 * Display user list in data table.
 */
function initDatatable() {
  return $('#table').DataTable({
    ajax: {
      url: '/api/users',
      // Added data corresponding to the action column.
      // [caution]An error will occur if there is no data corresponding to the column.
      dataSrc: response => response.data.map(row => Object.assign(row, {actions: ''}))
    },
    columns: [
      {data: 'id', width: 30},
      {data: 'role', width: 50},
      {data: 'email'},
      {data: 'name'},
      {data: 'modified', width: 120},
      {data: 'actions', width: 270}
    ],
    columnDefs: [
      {
        targets: 0,
        render: (data, type, row, meta) => data.toString().padStart(10, '0')
      },{
        targets: -1,
        orderable: false,
        render: (data, type, row, meta) => 
          `<a href="/users/${row.id}" class="btn btn-success">Edit</a>
          <button on-delete type="button" class="btn btn-danger">Delete</button>`
      }
    ],
    scrollX: true,
    scrollCollapse: true,
    responsive: true,
    dom: `<'row'<'col-12'f>><'row'<'col-12'tr>><'row'<'col-12 dataTables_pager'p>>`,
    pageLength: 30,
    searchDelay: 500,
    processing: true,
    serverSide: true,
    fnServerParams: aoData => {
      const columns = Object.assign({}, aoData.columns);
      delete aoData.columns;
      const {column, dir} = aoData.order[0];
      aoData.order = columns[column].data;
      aoData.dir = dir;
      aoData.search = aoData.search.value;
      // aoData.order.forEach((items, index) => aoData.order[index].column = aoData.columns[items.column].aoData);
    }
  });
}

// Main processing.
(async () => {
  // const users = await $.ajax({url: '/api/users'});
  // console.log('users=', users);
  const dt = initDatatable();
})();