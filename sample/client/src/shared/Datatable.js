import initTooltip from '~/shared/initTooltip';

/**
 * DataTable.
 */
export default class {
  /**
   * Construct DataTable.
   */
  constructor(table, options) {
    // Get the target table.
    if ($.type(table) === 'string' || table instanceof HTMLTableElement)
      table = $(table);
    else if (!(table instanceof $))
      throw new TypeError('For the table parameter, specify a character string, HTMLTableElement, or a JQuery object of HTMLTableElement');
    this.table = table;

    // Whether the data acquisition method is Ajax
    const isAjax = 'ajax' in options;
    if (isAjax) {
      // For Ajax, send a cookie to the server.
      options.ajax.xhrFields = {withCredentials: true};

      // Add an element called "actions" corresponding to columns such as edit button and delete button to the record of response data.
      // [caution]An error will occur if there is no data corresponding to the column.
      const dataSrc = options.ajax.dataSrc||undefined;
      options.ajax.dataSrc = (res) => {
        res.data = res.data.map(row => Object.assign(row, {actions: ''}));
        if (dataSrc)
          res = dataSrc(res);
        return res.data;
      }
    }

    // Save drawCallback option.
    let drawCallback;
    if ('drawCallback' in options) {
      drawCallback = options.drawCallback;
      delete options.drawCallback;
    }

    // Save createdRow option.
    let createdRow;
    if ('createdRow' in options) {
      createdRow = options.createdRow;
      delete options.createdRow;
    }

    // Return data table instance.
    this.dt = this.table
      // .on('init.dt', () => console.log(`Table initialisation complete: ${new Date().getTime()}`))
      .on('draw.dt', () => {
        $("#table_processing").hide();
      })
      .DataTable(Object.assign({
        // responsive: true,
        // scrollCollapse: true,
        scrollX: true,
        // Display page up and down.
        dom: `<'row'<'col-12'f>><'row'<'col-12 dataTables_pager'p>><'row'<'col-12'tr>><'row'<'col-12 dataTables_pager'p>>`,
        // dom: `<'row'<'col-12'f>><'row'<'col-12'tr>><'row'<'col-12 dataTables_pager'p>>`,
        pageLength: 30,
        searchDelay: 500,
        processing: true,
        serverSide: isAjax,
        createdRow: (row, data, dataIndex) => {
          // Add the data ID to the tr element.
          if (data.id)
            $(row).attr('data-id', data.id);
          if (createdRow)
            createdRow(row, data, dataIndex);
        },
        drawCallback: settings => {
          // Initialize Bootstrap tooltip in the data table.
          if (bootstrap && bootstrap.Tooltip) {
            // Initialize the tooltip in the dynamically added line.
            initTooltip(this.table);

            // // Initialize the menu in the dynamically added line.
            // KTMenu.createInstances('[data-kt-menu="true"]');
          }
          if (drawCallback)
            drawCallback(settings);
        },
        fnServerParams: aoData => {
          const columns = Object.assign({}, aoData.columns);
          delete aoData.columns;
          if (aoData.order.length > 0) {
            const {column, dir} = aoData.order[0];
            aoData.order = columns[column].data;
            aoData.dir = dir;
          } else {
            aoData.order = null;
            aoData.dir = null;
          }
          aoData.search = aoData.search.value;
          // aoData.order.forEach((items, index) => aoData.order[index].column = aoData.columns[items.column].aoData);
        },
        language: {
          sEmptyTable: '該当データは見つかりません',
          sInfo: ' _TOTAL_ 件中 _START_ から _END_ まで表示',
          sInfoEmpty: ' 0 件中 0 から 0 まで表示',
          sInfoFiltered: '（全 _MAX_ 件より抽出）',
          sInfoPostFix: '',
          sInfoThousands: ',',
          sLengthMenu: '_MENU_ 件表示',
          sLoadingRecords: '&nbsp;',
          sProcessing: '<div class="datatable-spinner"></div>',
          //sLoadingRecords: '読み込み中...',
          //sProcessing: '処理中...',
          sSearch: `<span class="svg-icon svg-icon-muted svg-icon-1"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24"/>
                        <path d="M14.2928932,16.7071068 C13.9023689,16.3165825 13.9023689,15.6834175 14.2928932,15.2928932 C14.6834175,14.9023689 15.3165825,14.9023689 15.7071068,15.2928932 L19.7071068,19.2928932 C20.0976311,19.6834175 20.0976311,20.3165825 19.7071068,20.7071068 C19.3165825,21.0976311 18.6834175,21.0976311 18.2928932,20.7071068 L14.2928932,16.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                        <path d="M11,16 C13.7614237,16 16,13.7614237 16,11 C16,8.23857625 13.7614237,6 11,6 C8.23857625,6 6,8.23857625 6,11 C6,13.7614237 8.23857625,16 11,16 Z M11,18 C7.13400675,18 4,14.8659932 4,11 C4,7.13400675 7.13400675,4 11,4 C14.8659932,4 18,7.13400675 18,11 C18,14.8659932 14.8659932,18 11,18 Z" fill="#000000" fill-rule="nonzero"/>
                      </g>
                    </svg></span>`,
          //sSearch: '<i class="fas fa-search"></i>',
          sSearchPlaceholder: 'キーワードを入力',
          sZeroRecords: '該当データは見つかりません',
          // oPaginate: {
          //   sFirst: '先頭',
          //   sLast: '最終',
          //   sNext: '次',
          //   sPrevious: '前'
          // },
          oAria: {
            sSortAscending: ': 列を昇順に並べ替えるにはアクティブにする',
            sSortDescending: ': 列を降順に並べ替えるにはアクティブにする'
          }
        }
      }, options));

    // Removed class to hide table during Datatable initialization.
    // table.removeClass('dataTables_ready');
    // Return data table instance.

    // In order to display the loading image in the center, set the "position" of the parent element of the loading image (.dataTables_processing) to "relative".
    $('#table_processing').parent().css('position', 'relative');

    // Readjust the column widths once the window is resized.
    $(window).on('resize', () => {
      this.dt.columns.adjust();
    });
  }

  /**
   * Delete row.
   */
  deleteRow(tr) {
    this.dt.row(tr).remove().draw();
  }

  /**
   * Add row.
   */
  addRow(data, paging = true) {
    const rowNode = this.dt.row.add(data).draw(paging);
    return rowNode;
  }

  /**
   * Update row.
   * 
   * @param  {HTMLTableRowElement}                     tr
   * @param  {object}                                  updateData
   * @param  {boolean}                                 redraw
   * @param  {boolean|'full-reset'|'full-hold'|'page'} paging   full-reset or true (default): the ordering and search will be recalculated and the rows redrawn in their new positions. The paging will be reset back to the first page.
   *                                                            full-hold or false          : the ordering and search will be recalculated and the rows redrawn in their new positions. The paging will not be reset - i.e. the current page will still be shown.
   *                                                            page                        : ordering and search will not be updated and the paging position held where is was. This is useful for paging when data has not been changed between draws.
   */
  updateRow(tr, updateData, redraw = true, paging = true) {
    const data = this.dt.row(tr).data();
    Object.assign(data, updateData);
    const res = this.dt.row(tr).data(Object.assign(data, updateData));
    if (redraw)
      res.draw(paging);
  }

  /**
   * Returns the Data object associated with the Row.
   */
  getRowData(tr) {
    return this.dt.row(tr).data();
  }

  /**
   * Reload data.
   */
  reload() {
    this.dt.ajax.reload(null, false);
  }

  /**
   * Adjust column layout
   */
  adjustColumns() {
    this.dt.columns.adjust();
  }

  /**
   * Returns row count.
   */
  getRowCount(rowSelector = undefined) {
    return this.dt.rows(rowSelector).count();
  }

  /**
   * Returns row nodes.
   * 
   * @return {HTMLTableRowElement[]}
   */
  getRowNodes() {
    return this.dt.rows().nodes().to$().toArray();
  }

  /**
   * Returns row object.
   * 
   * @return {DataTables.Api}
   */
  getRowObject(rowNode) {
    return this.dt.rows(rowNode);
  }

  /**
   * Returns a table wrapper element.
   */
  get container() {
    return $(this.dt.table().container());
  }

  /**
   * Returns a table filter container element.
   */
  get filterContainer() {
    return this.container.find('.dataTables_filter:first');
  }
}