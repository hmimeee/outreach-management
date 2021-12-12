<?php

namespace Modules\OutreachManagement\Datatables;

use App\DataTables\BaseDataTable;
use Modules\OutreachManagement\Entities\Invoice;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class InvoicesDataTable extends BaseDataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
      return datatables()
      ->eloquent($query)
      ->addColumn('action', function ($row) {
        $action = '<div class="btn-group dropdown m-r-10">
        <button aria-expanded="false" data-toggle="dropdown" class="btn dropdown-toggle waves-effect waves-light" type="button"><i class="ti-more"></i></button>
        <ul role="menu" class="dropdown-menu pull-right">
        <li><a href="javascript:;" onclick="viewInvoice('.$row->id.')"><i class="fa fa-eye" aria-hidden="true"></i> View</a></li>';

        if ($row->status != 1) {
          $action .= '<li><a href="javascript:;" onclick="editInvoice('.$row->id.')"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a></li>';
          
          $action .= '<li><a href="javascript:;" data-id="'.$row->id.'" id="deleteInvoice"><i class="fa fa-trash" aria-hidden="true"></i> Delete</a></li>';
        }

        $action .= '</ul> </div>';

        return $action;
      })
      ->editColumn('name', function ($row) {
        return '<a href="javascript:;" onclick="viewInvoice('.$row->id.')">'.$row->name.'</a>';
      })

      ->editColumn('amount', function ($row) {
        return '$'.$row->amount;
      })

      ->editColumn('created_at', function ($row) {
        return $row->created_at->format('d M Y');
      })

      ->editColumn('outreach_site', function ($row) {
        $url = route('member.outreach-management.index');

        if (auth()->user()->hasRole('admin')) {
          $url = route('admin.outreach-management.index');
        }

        $website = '<a target="_blank" href="'.$url.'?view-site='.$row->outreach_site_id.'">'.$row->site->site.'</a>';

        return $website;
      })

      ->editColumn('status', function ($row) {
        $class = ['0' => 'danger', '1' => 'success'];
        if ($row->review) {
          $status = '<label class="label label-'.$class[$row->status].'">'. ($row->status ? 'Paid' : 'Unpaid') .'</label>';
        } else {
          $status = '<span class="label label-warning">Waiting for Review</span>';
        }
        return $status;

      })
      ->addIndexColumn()
      ->rawColumns(['action', 'name', 'status', 'outreach_site']);

    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Product $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Invoice $model)
    {
      $request = $this->request();

      $invoice =  $model->select('outreach_invoices.*');

      if ($request->status != 'all' && $request->status != '') {
        $invoice = $invoice->where('status', $request->status);
      }

      return $invoice;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
      return $this->builder()
      ->setTableId('invoices-table')
      ->columns($this->getColumns())
      ->minifiedAjax()
      ->dom("<'row'<'col-md-6'l><'col-md-6'Bf>><'row'<'col-sm-12'tr>><'row'<'col-sm-5'i><'col-sm-7'p>>")
      ->orderBy(0)
      ->destroy(true)
      ->responsive(true)
      ->serverSide(true)
      ->stateSave(true)
      ->processing(true)
      ->language(__("app.datatable"))
      ->buttons(
        Button::make(['extend'=> 'export','buttons' => ['excel', 'csv']])
      )
      ->parameters([
        'initComplete' => 'function () {
         window.LaravelDataTables["invoices-table"].buttons().container()
         .appendTo( ".bg-title .text-right")
       }',
       'fnDrawCallback' => 'function( oSettings ) {
        $("body").tooltip({
          selector: \'[data-toggle="tooltip"]\'
          })
        }',
      ]);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
      return [
        '#' => ['data' => 'id', 'name' => 'id', 'visible' => true],
        'name' => ['data' => 'name', 'name' => 'name'],
        'outreach site' => ['data' => 'outreach_site', 'name' => 'outreach_site_id'],
        'generated_at' => ['data' => 'created_at', 'name' => 'created_at'],
        'amount' => ['data' => 'amount', 'name' => 'amount'],
        'status' => ['data' => 'status', 'name' => 'status'],
        Column::computed('action')
        ->exportable(false)
        ->printable(false)
        ->orderable(false)
        ->searchable(false)
        ->width(150)
        ->addClass('text-center')
      ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
      return 'outreach_' . date('YmdHis');
    }

    public function pdf()
    {
      set_time_limit(0);
      if ('snappy' == config('datatables-buttons.pdf_generator', 'snappy')) {
        return $this->snappyPdf();
      }

      $pdf = app('dompdf.wrapper');
      $pdf->loadView('datatables::print', ['data' => $this->getDataForPrint()]);

      return $pdf->download($this->getFilename() . '.pdf');
    }
  }
