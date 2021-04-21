<?php

namespace App\Http\Controllers;

use App\People;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController2 extends Controller
{
    public function index(){
        $columns_coll = DB::select('SELECT COLUMN_NAME, IS_NULLABLE, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH FROM information_schema.columns WHERE table_name = \'people\'');
        return view('main2',compact('columns_coll'));
    }

    public function data()
    {
        $data =People::orderBy('created_at', 'asc')->get();
        return response()->json(['data'=>$data]);
    }


    public function search(Request $request)
    {
        $table_name = 'people';
        $static_db = 'select * from ' . $table_name . ' where ';

        if($request->data!=null){
            $row_count = $request->row_count;
            $data = $request->data;

            $field1_row0 = '';
            $field2_row0 = '';
            $field3_row0 = '';
            $fields_1 = [];
            $fields_2 = [];
            $fields_3 = [];

            for ($i = 1; $i < $row_count + 1; $i++) {
                foreach ($data as $key => $value) {
                    //get rows 0
                    if ($value['name'] == 'rows[0][field_1]') {
                        $field1_row0 = $value['value'];
                    } elseif ($value['name'] == 'rows[0][field_2]') {
                        $field2_row0 = $value['value'];
                    } elseif ($value['name'] == 'rows[0][field_3]') {
                        $field3_row0 = $value['value'];
                    }
                    //get other rowsfields
                    if ($value['name'] == "rows[" . $i . "][field_1]") {
                        $fields_1[$i] = $value['value'];
                    } elseif ($value['name'] == "rows[" . $i . "][field_2]") {
                        $fields_2[$i] = $value['value'];
                    } elseif ($value['name'] == "rows[" . $i . "][field_3]") {
                        $fields_3[$i] = $value['value'];
                    }
                }
            }

            $array1 = array_values($fields_1);
            $array2 = array_values($fields_2);
            $array3 = array_values($fields_3);



            //field_1---row 0
            $field_1_array = explode(" ", $field1_row0);
            $field_1_title = $field_1_array[1];
            $field_1_type = $field_1_array[0];

            //make first condition
            switch ($field2_row0) {
                case 'like':
                case 'not like':
                    $first_condition = $field_1_title . ' ' . $field2_row0 . " '%" . $field3_row0 . "%'";
                    break;
                case 'is not null':
                case 'is null':
                case '= 1':
                case '= 0':
                    $first_condition = $field_1_title . ' ' . $field2_row0;
                    break;
                default:
                    $first_condition = $field_1_title . $field2_row0 . "'" . $field3_row0 . "'";
                    break;
            }

            //make other conditions
            if ($row_count > 1) {
                //get types and titles of rows from field_1
                $titles_types = [];
                foreach ($array1 as $field_1) {
                    $field1_array = explode(" ", $field_1);;
                    $titles_types[] = ['title' => $field1_array[1], 'type' => $field1_array[0]];
                }

                //make condition strings of other rows
                $or_condtions = [];
                $advanced_search = new AdvancedSearch();
                foreach ($array2 as $key => $field_2) {
                    $or_condtions[] = $advanced_search->switch_db($titles_types[$key]['title'], $field_2, $array3[$key]) . '';
                }

                //join values of the $or_condition array
                $other_conditions = '';
                foreach ($or_condtions as $key => $condition) {
                    $other_conditions .= $condition;
                }
            }

            //make query string

            if ($row_count > 1) {
                //query or conditions
                $db = $static_db . $first_condition . $other_conditions;
                $result_second_section = $first_condition . $other_conditions;
            } else {
                $db = $static_db . $first_condition;  //query one condition
                $result_second_section = $first_condition;
            }

            //Make AND conditions
            if (($request->prv_query) != null) {
                $prv_queries = $request->prv_query;
                $database = '';
                foreach ($prv_queries as $key => $query) {
                    if ($query != "")
                        $database .= "(" . $query . ")" . " and ";
                }
                $db = $static_db . $database . "(" . $result_second_section . ")";
            }

        }

        //////remove all queries by clicking the remove icon in search bar
        elseif($request->prv_query == "all"){
            $db = 'select * from ' . $table_name;
            $result_second_section = "";
        }

        //////and condition queries
        else{
            $prv_queries = $request->prv_query;
            $database = '';

            if($prv_queries!=null){
                if(count($prv_queries) != 2){
                    for ($i = 0; $i < count($prv_queries)-2; $i++) {
                        if ($prv_queries[$i] != "")
                            $database .= "(" . $prv_queries[$i] . ")" . " and ";
                    }
                    $db = $static_db . $database ."( ". $prv_queries[count($prv_queries) - 2]." )";
                    $result_second_section = "";
                }else{
                    foreach ($prv_queries as $query){
                        $database .= $query;
                    }
                    $db =  $static_db . $database;
                    $result_second_section = "";
                }
            }else{
                $db = 'select * from ' . $table_name ;
                $result_second_section = "";
            }
        }

        //Get data from database
        if ($request->all() != null) {
            $result = DB::select(DB::raw($db));
        } else {
            $result = DB::select(DB::raw('select * from people'));
        }

        //return results
        return response()->json([
                'result'=>$result,
                'db'=>$result_second_section,
                'natije2'=>$db,
                'formHTML'=>$formHTML
            ]
        );
    }
}
