<?php /** @noinspection ALL */

namespace App\Http\Controllers;

use App\AdvancedSearch;
use App\People;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
//Search-bar functions
    public function index(){
        $columns_coll = DB::select('SELECT COLUMN_NAME, IS_NULLABLE, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH FROM information_schema.columns WHERE table_name = \'people\'');
        return view('main',compact('columns_coll'));
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

            //make other or conditions
            if ($row_count > 1) {
                //get types and titles of rows from field_1
                $titles_types = [];
                foreach ($array1 as $field_1) {
                    $field1_array = explode(" ", $field_1);
                    $titles_types[] = ['title' => $field1_array[1], 'type' => $field1_array[0]];
                }

                ////titles of other or conditions
                $titles_other_rows = [];
                foreach ($titles_types as $key => $value){
                    $titles_other_rows[] = $value['title'];
                }
                ////types of other or conditions
                $types_other_rows = [];
                foreach ($titles_types as $key => $value){
                    $types_other_rows[] = $value['type'];
                }

                ///////titles of OR conditions
                $titlesss []= $field_1_title;
                $titless[]= array_merge($titlesss, $titles_other_rows);
                $titles= $titless[0];

                ///////types of OR conditions
                $typesss []= $field_1_type;
                $typess[]= array_merge($typesss, $types_other_rows);
                $types = $typess[0];

                ///////fields 2 of OR conditions
                $arrayyy2 []= $field2_row0;
                $arrayy2[]= array_merge($arrayyy2, $array2);
                $fieldd_2 = $arrayy2[0];

                ///////fields 3 of OR conditions
                $arrayyy3 []= $field3_row0;
                $arrayy3[]= array_merge($arrayyy3, $array3);
                $fieldd_3 = $arrayy3[0];

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
            $titles = [];
            $types = [];
            $fieldd_2 = [];
            $fieldd_3 = [];
        }


        else{
            $prv_queries = $request->prv_query;
            $database = '';

            if($prv_queries!=null){
                ///more than one previuos query
                if(count($prv_queries) != 2){
                    for ($i = 0; $i < count($prv_queries)-2; $i++) {
                        if ($prv_queries[$i] != "")
                            $database .= "(" . $prv_queries[$i] . ")" . " and ";
                    }
                    $db = $static_db . $database ."( ". $prv_queries[count($prv_queries) - 2]." )";
                    $result_second_section = "";
                    $titles = [];
                    $types = [];
                    $fieldd_2 = [];
                    $fieldd_3 = [];
                }
                ///one previuos query
                else{
                    foreach ($prv_queries as $query){
                        $database .= $query;
                    }
                    $db =  $static_db . $database;
                    $result_second_section = "";
                    $titles = [];
                    $types = [];
                    $fieldd_2 = [];
                    $fieldd_3 = [];
                }
            }
            ////no previous query
            else{
                $db = 'select * from ' . $table_name ;
                $result_second_section = "";
                $titles = [];
                $types = [];
                $fieldd_2 = [];
                $fieldd_3 = [];
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
                'titles'=>$titles,
                'types'=>$types,
                'fields_2'=>$fieldd_2,
                'fields_3'=>$fieldd_3
            ]
        );
    }

//search-list functions
    public function index_list()
    {
        $columns_coll = DB::select('SELECT COLUMN_NAME, IS_NULLABLE, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH FROM information_schema.columns WHERE table_name = \'people\'');
        return view('main-list',compact('columns_coll'));
    }
    public function edit(Request $request)
    {

        $table_name = 'people'  ;
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
                    if ($value['name'] == 'rowEdit[0][field_1]') {
                        $field1_row0 = $value['value'];
                    } elseif ($value['name'] == 'rowEdit[0][field_2]') {
                        $field2_row0 = $value['value'];
                    } elseif ($value['name'] == 'rowEdit[0][field_3]') {
                        $field3_row0 = $value['value'];
                    }
                    //get other rowsfields
                    if ($value['name'] == "rowEdit[" . $i . "][field_1]") {
                        $fields_1[$i] = $value['value'];
                    } elseif ($value['name'] == "rowEdit[" . $i . "][field_2]") {
                        $fields_2[$i] = $value['value'];
                    } elseif ($value['name'] == "rowEdit[" . $i . "][field_3]") {
                        $fields_3[$i] = $value['value'];
                    }
                }
            }

            //get array of fields1,2,3
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

            //make other or conditions
            if ($row_count > 1) {
                //get types and titles of rows from field_1
                $titles_types = [];
                foreach ($array1 as $field_1) {
                    $field1_array = explode(" ", $field_1);
                    $titles_types[] = ['title' => $field1_array[1], 'type' => $field1_array[0]];
                }

                ////titles of other or conditions
                $titles_other_rows = [];
                foreach ($titles_types as $key => $value){
                    $titles_other_rows[] = $value['title'];
                }
                ////types of other or conditions
                $types_other_rows = [];
                foreach ($titles_types as $key => $value){
                    $types_other_rows[] = $value['type'];
                }

                ///////titles of OR conditions
                $titlesss []= $field_1_title;
                $titless[]= array_merge($titlesss, $titles_other_rows);
                $titles= $titless[0];

                ///////types of OR conditions
                $typesss []= $field_1_type;
                $typess[]= array_merge($typesss, $types_other_rows);
                $types = $typess[0];

                ///////fields 2 of OR conditions
                $arrayyy2 []= $field2_row0;
                $arrayy2[]= array_merge($arrayyy2, $array2);
                $fieldd_2 = $arrayy2[0];

                ///////fields 3 of OR conditions
                $arrayyy3 []= $field3_row0;
                $arrayy3[]= array_merge($arrayyy3, $array3);
                $fieldd_3 = $arrayy3[0];

                //make condition strings of other rows
                $or_condtions = [];
                $advanced_search = new AdvancedSearch();
                $lenght = "";
                if(count($array3) < count($array2)){
                    $lenght = "is short";
                }

                foreach ($array2 as $key => $field_2) {
//                    if($lenght == "is short"){
                        if(!isset($array3[$key])){
                            $array3[$key] = "";
                            $or_condtions[] = $advanced_search->switch_db($titles_types[$key]['title'], $field_2, $array3[$key]) . '';
                        }
//                    }
                    else {
                        $or_condtions[] = $advanced_search->switch_db($titles_types[$key]['title'], $field_2, $array3[$key]) . '';
                    }
                }

                //join values of the $or_condition array
                $other_conditions = '';
                foreach ($or_condtions as $key => $condition) {
                    $other_conditions .= $condition;
                }
            }
            //row = 1
            else{
                $titles = $field_1_title;
                $types = $field_1_type;
                $fieldd_2 = $field2_row0;
                $fieldd_3 = $field3_row0;
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
                'titles'=>$titles,
                'types'=>$types,
                'fields_2'=>$fieldd_2,
                'fields_3'=>$fieldd_3
            ]
        );
    }

//all data function
    public function totaldata()
    {
        $data = People::orderBy('created_at','asc')->get();
        return response()->json(['data'=>$data]);
    }
}



