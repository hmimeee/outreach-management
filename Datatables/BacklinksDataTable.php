<?php

namespace Modules\OutreachManagement\Datatables;

use App\DataTables\BaseDataTable;
use Modules\OutreachManagement\Entities\Backlink;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Illuminate\Support\Str;

class BacklinksDataTable extends BaseDataTable
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
        <li><a href="javascript:;" onclick="viewLink('.$row->id.')"><i class="fa fa-eye" aria-hidden="true"></i> View</a></li>';

        $action .= '<li><a href="javascript:;" onclick="editLink('.$row->id.')"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a></li>';

        if ($row->status !='approved' && $row->invoice == null) {
          $action .= '<li><a href="javascript:;" data-id="'.$row->id.'" id="deleteBacklink"><i class="fa fa-trash" aria-hidden="true"></i> Delete</a></li>';
        }

        $action .= '</ul> </div>';

        return $action;
      })
      ->editColumn('project', function ($row) {
        $url = route('member.projects.show', $row->project_id);

        if (auth()->user()->hasRole('admin')) {
          $url = route('admin.projects.show', $row->project_id);
        }
        return '<a target="_blank" href="'.$url.'">'.$row->project->project_name.'</a>';
      })
      ->editColumn('url', function ($row) {
        $website = '<a target="_blank" href="' . $row->url . '">' . $row->url . '</a>';
        return $website;
      })
      ->editColumn('backlink', function ($row) {
        $link = '<a href="javascript:;" onclick="viewLink(' . $row->id . ')">' . Str::limit($row->backlink, 40) . '</a>';
        return $link;
      })
      ->editColumn('visit', function ($row) {
        $link = '<a target="_blank" href="' . $row->backlink . '">Visit Link</a>';
        return $link;
      })
      ->editColumn('status', function ($row) {
        $class = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger'];
        return '<label class="label label-'.$class[$row->status].'">'. ucfirst($row->status) .'</label>';
      })
      ->addIndexColumn()
      ->rawColumns(['action', 'project', 'url', 'backlink', 'visit', 'status']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Product $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Backlink $model)
    {
      $request = $this->request();

      $sites =  $model->select('outreach_backlinks.*');

      if ($request->status != 'all' && $request->status != '') {
        $sites = $sites->where('status', $request->status);
      }

      return $sites;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
      return $this->builder()
      ->setTableId('links-table')
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
         window.LaravelDataTables["links-table"].buttons().container()
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
        'backlink' => ['data' => 'backlink', 'name' => 'backlink'],
        'visit',
        'project' => ['data' => 'project', 'name' => 'project_id'],
        'target url' => ['data' => 'url', 'name' => 'url'],
        'published_date' => ['name' => 'published_date'],
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
