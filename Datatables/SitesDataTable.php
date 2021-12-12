<?php

namespace Modules\OutreachManagement\Datatables;

use App\DataTables\BaseDataTable;
use Modules\OutreachManagement\Entities\Site;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Modules\OutreachManagement\Entities\OutreachSetting;

class SitesDataTable extends BaseDataTable
{
    public $setting;
    
    public function __construct()
    {
        parent::__construct();
        $this->setting = OutreachSetting::first();
    }

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
        <li><a href="javascript:;" onclick="viewSite('.$row->id.')"><i class="fa fa-eye" aria-hidden="true"></i> View</a></li>';

        $action .= '<li><a href="javascript:;" onclick="editSite('.$row->id.')"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a></li>';

        if ($row->status != 'approved') {
          $action .= '<li><a href="javascript:;" data-id="'.$row->id.'" id="deleteSite"><i class="fa fa-trash" aria-hidden="true"></i> Delete</a></li>';
        }

        $action .= '</ul> </div>';

        return $action;
      })
          ->editColumn('website', function ($row) {
            return '<a href="javascript:;" onclick="viewSite('.$row->id.')">'.$row->site.'</a>';
          })
          ->editColumn('ahref_snap_link', function ($row) {
            if ($row->ahref_snap != null) {
              $link = '<a target="_blank" href="'.$row->ahref_snap.'">Link</a>';
            } else {
              $link = '--';
            }
            
            return $link;
          })
          ->editColumn('post_price', function ($row) {
            if ($row->post_price) {
              $price = '$'.$row->post_price;
            } else {
              $price = '--';
            }

            return $price;
          })
          ->editColumn('link_price', function ($row) {
            if ($row->link_price) {
              $price = '$'.$row->link_price;
            } else {
              $price = '--';
            }

            return $price;
          })
          ->editColumn('status', function ($row) {
            $class = ['rejected' => 'danger', 'approved' => 'success', 'soft rejected' => 'warning', 'pending' => 'info'];
            return '<label class="label label-'.$class[$row->status].'">'. ucwords($row->status) .'</label>';

          })
          ->addIndexColumn()
          ->rawColumns(['action', 'website', 'status', 'ahref_snap_link']);

        }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Product $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Site $model)
    {
      $request = $this->request();

      $sites =  $model->select('outreach_sites.*');

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
      ->setTableId('sites-table')
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
         window.LaravelDataTables["sites-table"].buttons().container()
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
        'website' => ['data' => 'website', 'name' => 'website'],
        'niche' => ['data' => 'niche', 'name' => 'niche'],
        'traffic' => ['data' => 'traffic', 'name' => 'traffic'],
        'traffic_value' => ['data' => 'traffic_value', 'name' => 'traffic_value'],
        'spam_score' => ['data' => 'spam_score', 'name' => 'spam_score'],
        'post_price (each)' => ['data' => 'post_price', 'name' => 'post_price'],
        'link_price (each)' => ['data' => 'link_price', 'name' => 'link_price'],
        'ahref_snap_link' => ['data' => 'ahref_snap_link', 'name' => 'ahref_snap'],
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
