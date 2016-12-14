<?php

if(!class_exists('WP_List_Table')){

  require_once( __HELPFUL_DIR__ . 'resources/class-wp-list-table.php' );

}


class Stats_List_Table extends WP_List_Table {

    public $settings;
    public $allStats;
    public $perPage;

     function __construct($args){
         parent::__construct( array_merge($args, array(
        'singular'=> 'Stats',
        'plural' => 'Stats',
        'ajax'   => false,
         ) ));
     }

    function column_default( $item, $column_name ) {
        switch( $column_name ) {
            case 'title':
            case 'yes':
            case 'no':
            case 'yesPercent':
            return $this->custom_column_value($column_name,$item);
            default:
            return print_r( $item, true ) ;
        }
    }

    function custom_column_value($column_name,$item){
        switch( $column_name ) {
            case 'title':
                return '<a href="' . $item["link"] . '" rel="bookmark" title="' . $item["title"]. '">' . $item["title"] . '</a>';
            case 'yes':
                return $item["yes"];
            case 'no':
                return $item["no"];
            case 'yesPercent':
                return $item["yesPercent"];
        }
        return "";
    }

    public function get_sortable_columns(){
        return $sortable = array(
			'yes'=>array('yes',false),
            'no'=>array('no',false),
			'yesPercent'=>array('yesPercent',true),
        );
    }

    function get_columns(){
        return $columns= array(
          'title'=>__('Title', __HELPFUL_PLUGIN_SLUG__),
          'yes'=>__($this->settings["yes_title"], __HELPFUL_PLUGIN_SLUG__),
          'no'=>__($this->settings["no_title"], __HELPFUL_PLUGIN_SLUG__),
          'yesPercent'=>__($this->settings["yes_title"], __HELPFUL_PLUGIN_SLUG__).'%',
        );
    }

    function bulk_actions() { return array();}

    function prepare_items(){
        $totalitems = count($this->allStats["stats"]);

        $paged = !empty($_GET["paged"]) ? $_GET["paged"] : '';

        if(empty($paged) || !is_numeric($paged) || $paged<=0 ){ $paged=1; }

        $totalpages = ceil($totalitems/$this->perPage);

        if(!empty($paged) && !empty($this->perPage)){
          $offset=($paged-1)*$this->perPage;
        }

          $this->set_pagination_args( array(
             "total_items" => $totalitems,
             "total_pages" => $totalpages,
             "per_page" => $this->perPage,
          ) );

          $columns = $this->get_columns();

          $hidden   = array();

          $sortable = $this->get_sortable_columns();

          $this->_column_headers = array( $columns, $hidden, $sortable );

          $stats    = array_slice($this->allStats["stats"], $offset, $this->perPage, true);
          $this->items = $stats;

     }
}